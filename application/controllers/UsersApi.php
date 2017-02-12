<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UsersApi extends API_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->library('email');
        $this->load->library('encryption');
        $this->load->model('user_model');
    }

    public function register() {
        /**
         * @todo Need validation check
         */
        $hash = password_hash($this->input->post('password'), PASSWORD_BCRYPT);
        $id = $this->user_model->add(
            $this->input->post('email'),
            $hash,
            $this->input->post('user_name'),
            $this->input->post('profile_image'),
            $this->input->post('type')
        );

        if ($id) {
            $user = $this->user_model->get_by_id($id);
            $this->set_user_session($user);
            $this->send_mail($user);
            $this->set_success_response(['message' => 'signup success']);
        } else {
            $this->set_fail_response('101', ['message' => 'duplicated email']);
        }

        return $this->output->set_output(json_encode($this->result));
    }

    public function login() {

        $email = $this->input->post('email');
        $user = $this->user_model->get_by_email($email);

        if (!$user) {
            $this->set_fail_response('102', ['message' => 'email is not founded']);
        } else {
            $password = $this->user_model->get_password($email);
            if (password_verify($this->input->post('password'), $password)) {
                $this->set_success_response(['message' => 'login success']);
                $this->set_user_session($user);
            } else {
                $this->set_fail_response('103', ['message' => 'password is not corrected']);
            }
        }

        return $this->output->set_output(json_encode($this->result));
    }

    public function verify() {
        $code = preg_split('#/#',$this->encryption->decrypt($this->input->get('key', FALSE)));
        list($email, $id) = $code;

        $user = $this->user_model->get_by_id($id);

        if ($user->email === $email && $user->id === $id) {
            $result = $this->user_model->authorize($id);
            if ($result) {
                $this->set_user_session($user);
                $this->set_success_response(['message' => 'authorize success']);
            } else {
                $this->set_fail_response('104', ['message' => 'invalid access']);
            }
        } else {
            $this->set_fail_response('104', ['message' => 'invalid access']);
        }

        return $this->output->set_output(json_encode($this->result));
    }

    public function send_mail($user) {
        $code = urlencode($this->encryption->encrypt($user->email . '/' . $user->id));
        $this->email->initialize(array('mailtype' => 'html'));
        $this->load->helper('url');
        $this->email->from('admin@pickartyou.com', 'pickartyou');
        $this->email->subject('pickartyou 회원가입 링크');
        $this->email->message('<a href=' . base_url() . 'api/users/verify?key=' . $code . ' style="color: white; font-weight: normal; text-decoration: none; word-break: break-word; font-size: 20px; line-height: 26px; border-top: 14px solid; border-bottom: 14px solid; border-right: 32px solid; border-left: 32px solid; background-color: #2ab27b; border-color: #2ab27b; display: inline-block; letter-spacing: 1px; min-width: 80px; text-align: center; border-radius: 4px; text-shadow: 0 1px 1px rgba(0,0,0,0.25);">
					Join
				</a>');
        $this->email->to($user->email);
        $this->email->send();
    }

    public function set_user_session($user) {
        $refresh = (array) $this->user_model->get_by_id($user->id);
        $this->session->set_userdata($refresh);
        $this->session->set_userdata(['logged_in' => true]);
    }
}
