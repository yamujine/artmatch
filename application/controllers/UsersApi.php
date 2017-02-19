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
        if ($this->form_validation->run() === TRUE) {
            $hashed_password = password_hash($this->input->post('password'), PASSWORD_BCRYPT);
            $uploaded_image_name = $this->imageupload->upload_images('profile_image', true, 'profile');

            $id = $this->user_model->add(
                $this->input->post('email'),
                $hashed_password,
                $this->input->post('user_name'),
                $uploaded_image_name,
                $this->input->post('type')
            );

            if ($id) {
                $user = $this->user_model->get_by_id($id);
                $this->accountlib->generate_user_session($user->id);
                $this->accountlib->send_email_authentication($user->email, $user->id);
                $this->set_success_response(['message' => 'signup success']);
            } else {
                $this->set_fail_response('101', ['message' => 'duplicated email']);
            }
        } else {
            $this->set_fail_response('105', ['message' => $this->form_validation->error_string()]);
        }

        return $this->output->set_output(json_encode($this->result));
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
            $this->set_fail_response('102', ['message' => $error_msg]);
        } else {
            if (password_verify($this->input->post('password'), $user->password)) {
                $this->set_success_response(['message' => 'login success']);
                $this->accountlib->generate_user_session($user->id);
            } else {
                $this->set_fail_response('103', ['message' => 'password is not corrected']);
            }
        }

        return $this->output->set_output(json_encode($this->result));
    }

    public function update_profile_image() {
        $user_id = $this->accountlib->get_user_id();
        $current_image = $this->user_model->get_by_id($user_id)->profile_image;
        $uploaded_image_name = $this->imageupload->upload_images('profile_image', true, 'profile');

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

        if ($result === TRUE) {
            $this->set_success_response(['message' => 'update success']);
        } else {
            $this->set_fail_response('500', ['message' => $this->db->error()]);
        }
        return $this->output->set_output(json_encode($this->result));
    }

    public function update_password() {
        $user = $this->user_model->get_by_id($this->accountlib->get_user_id());

        if (password_verify($this->input->post('current_password'), $user->password)) {
            $hashed_password = password_hash($this->input->post('new_password'), PASSWORD_BCRYPT);
            $result = $this->user_model->update_password($user->id, $hashed_password);
            if ($result === TRUE) {
                $this->set_success_response(['message' => 'update success']);
            } else {
                $this->set_fail_response('500', ['message' => $this->db->error()]);
            }
        } else {
            $this->set_fail_response('103', ['message' => 'password is not corrected']);
        }
        return $this->output->set_output(json_encode($this->result));
    }

    public function check_username() {
        $username = $this->input->get('username');
        if (empty($username)) {
            $this->set_fail_response('101', ['message' => '유저 아이디가 입력되지 않았습니다.']);
        } else {
            $result = $this->user_model->get_by_user_name($username);
            if ($result) {
                $this->set_fail_response('102', ['message' => '이미 사용중인 유저 아이디입니다.']);
            } else {
                $this->set_success_response(['message' => '사용 가능한 유저 아이디입니다.']);
            }
        }

        return $this->output->set_output(json_encode($this->result));
    }

    public function check_email() {
        $email = $this->input->get('email');
        if (empty($email)) {
            $this->set_fail_response('101', ['message' => '이메일이 입력되지 않았습니다.']);
        } else {
            $result = $this->user_model->check_email($email);
            if ($result) {
                $this->set_fail_response('102', ['message' => '이미 사용중인 이메일입니다.']);
            } else {
                $this->set_success_response(['message' => '사용 가능한 이메일입니다.']);
            }
        }

        return $this->output->set_output(json_encode($this->result));
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
        $this->form_validation->set_rules('user_name', '유저 아이디', 'required|alpha_dash|is_unique[users.user_name]', [
            'required' => '유저 아이디가 입력되지 않았습니다.',
            'alpha_dash' => '아이디에 사용할 수 없는 문자열이 포함되어 있습니다. (영 소문자, 숫자, -, _ 만 가능)',
            'is_unique' => '중복된 유저 아이디입니다.'
        ]);
        $this->form_validation->set_rules('type', '회원 구분', 'required|in_list[0,1]', [
            'required' => '회원 구분이 입력되지 않았습니다.',
            'in_list' => '회원 구분값이 올바르지 않습니다.'
        ]);
    }

    private function _set_user_session($user) {
        $userdata = (array)$this->user_model->get_by_id($user->id);
        $this->session->set_userdata($userdata);
    }
}
