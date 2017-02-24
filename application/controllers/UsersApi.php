<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UsersApi extends API_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->library(['email', 'encryption', 'form_validation', 'twig', 'imageupload']);
        $this->load->helper('url');
        $this->load->model('user_model');
    }

    public function register() {
        // Validation
        $this->_validate_signup_form();
        if ($this->form_validation->run() !== TRUE) {
            $this->return_fail_response('105', ['message' => $this->form_validation->error_string()]);
        }

        $hashed_password = password_hash($this->input->post('password'), PASSWORD_BCRYPT);
        $uploaded_image_name = $this->imageupload->upload_image('profile_image', true, 'profile');

        $id = $this->user_model->add(
            $this->input->post('email'),
            $hashed_password,
            $this->input->post('user_name'),
            $uploaded_image_name,
            $this->input->post('type')
        );

        if (!$id) {
            $this->return_fail_response('101', ['message' => 'duplicated email']);
        }

        $user = $this->user_model->get_by_id($id);
        $this->accountlib->generate_user_session($user->id);
        $this->accountlib->send_email_authentication($user->email, $user->id);

        $this->return_success_response(['message' => 'signup success']);
    }

    public function login() {
        $email_or_username = $this->input->post('email_or_username');
        if (strpos($email_or_username, '@') !== false) {
            $user = $this->user_model->get_by_email($email_or_username);
            $error_msg = '이메일을 찾을 수 없습니다.';
        } else {
            $user = $this->user_model->get_by_user_name($email_or_username);
            $error_msg = '아이디를 찾을 수 없습니다.';
        }

        if (!$user) {
            $this->return_fail_response('102', ['message' => $error_msg]);
        }

        if (!password_verify($this->input->post('password'), $user->password)) {
            $this->return_fail_response('103', ['message' => 'password is not corrected']);
        }

        $this->accountlib->generate_user_session($user->id);

        $this->return_success_response(['message' => 'login success']);
    }

    public function update_profile_image() {
        $user_id = $this->accountlib->get_user_id();
        $current_image = $this->user_model->get_by_id($user_id)->profile_image;
        $uploaded_image_name = $this->imageupload->upload_image('profile_image', true, 'profile');

        if (!empty($uploaded_image_name)) {
            if (!empty($current_image)) {
                //현재 이미지가 기본이미지가 아니면 삭제
                $this->imageupload->delete_image('/profile/' . $current_image);
            }
        }
        $result = $this->user_model->update_profile_image(
            $user_id,
            $uploaded_image_name
        );

        if (!$result) {
            $this->return_fail_response('500', ['message' => $this->db->error()]);
        }

        $this->return_success_response(['message' => 'update success']);
    }

    public function change_password() {
        $user = $this->user_model->get_by_id($this->accountlib->get_user_id());

        if (!password_verify($this->input->post('current_password'), $user->password)) {
            $this->return_fail_response('103', ['message' => 'password is not corrected']);
        }

        $hashed_password = password_hash($this->input->post('new_password'), PASSWORD_BCRYPT);
        $result = $this->user_model->update_password($user->id, $hashed_password);
        if (!$result) {
            $this->return_fail_response('500', ['message' => $this->db->error()]);
        }

        $this->return_success_response(['message' => 'update success']);
    }

    public function check_username() {
        $username = $this->input->get('username');
        if (empty($username)) {
            $this->return_fail_response('101', ['message' => '유저 아이디가 입력되지 않았습니다.']);
        }

        if (!$this->is_not_prohibitied_user_name($username)) {
            $this->return_fail_response('103', ['message' => '유저 아이디에 사용할 수 없는 단어가 포함되어 있습니다.']);
        }

        $result = $this->user_model->get_by_user_name($username);
        if ($result) {
            $this->return_fail_response('102', ['message' => '이미 사용중인 유저 아이디입니다.']);
        }

        $this->return_success_response(['message' => '사용 가능한 유저 아이디입니다.']);
    }

    public function check_email() {
        $email = $this->input->get('email');
        if (empty($email)) {
            $this->return_fail_response('101', ['message' => '이메일이 입력되지 않았습니다.']);
        }

        $is_already_used = $this->user_model->check_email($email);
        if ($is_already_used) {
            $this->return_fail_response('102', ['message' => '이미 사용중인 이메일입니다.']);
        }

        $this->return_success_response(['message' => '사용 가능한 이메일입니다.']);
    }

    public function reset_password() {
        $this->load->helper('string');
        $email = $this->input->post('email');
        $temp_password = random_string('alpha', 8);
        $user = $this->user_model->get_by_email($email);

        if ($user === NULL) {
            $this->return_fail_response('500', ['message' => '해당 이메일로 가입된 유저가 없습니다.']);
        }

        $hashed_password = password_hash($temp_password, PASSWORD_BCRYPT);
        $this->user_model->update_password($user->id, $hashed_password);
        $this->accountlib->send_email_temp_password($email, $temp_password);

        $this->return_success_response(['message' => '이메일로 임시 비밀번호를 전송해 드렸습니다']);
    }

    private function _validate_signup_form() {
        // Register validation
        $this->form_validation->set_error_delimiters('', "\r\n");
        $this->form_validation->set_rules('email', '이메일', 'required|valid_email|is_unique[users.email]', [
            'required' => '이메일 주소가 입력되지 않았습니다.',
            'valid_email' => '유효한 이메일 주소가 아닙니다.',
            'is_unique' => '중복된 이메일 주소입니다.'
        ]);
        $this->form_validation->set_rules('password', '패스워드', 'required', [
            'required' => '패스워드가 입력되지 않았습니다.'
        ]);
        $this->form_validation->set_rules('user_name', '유저 아이디', 'required|alpha_dash|is_unique[users.user_name]|max_length[15]|callback_is_not_prohibitied_user_name', [
            'required' => '유저 아이디가 입력되지 않았습니다.',
            'alpha_dash' => '아이디에 사용할 수 없는 문자열이 포함되어 있습니다. (영 소문자, 숫자, -, _ 만 가능)',
            'is_unique' => '중복된 유저 아이디입니다.'
        ]);
        $this->form_validation->set_rules('type', '회원 구분', 'required|in_list[0,1]', [
            'required' => '회원 구분이 입력되지 않았습니다.',
            'in_list' => '회원 구분값이 올바르지 않습니다.'
        ]);
    }

    public function is_not_prohibitied_user_name($user_name) {
        if (in_array($user_name, ['admin', 'me', 'pickartyou'], false)) {
            $this->form_validation->set_message('is_not_prohibitied_user_name', '유저 아이디에 사용할 수 없는 단어가 포함되어 있습니다.');

            return false;
        }

        return true;
    }
}
