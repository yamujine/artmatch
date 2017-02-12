<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UsersApi extends API_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->library(['email', 'encryption', 'twig']);
        $this->load->helper('url');
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
            $this->_set_user_session($user);
            $this->_send_mail($user->email, $user->id);
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
                $this->_set_user_session($user);
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
                $this->_set_user_session($user);
                $this->set_success_response(['message' => 'authorize success']);
            } else {
                $this->set_fail_response('104', ['message' => 'invalid access']);
            }
        } else {
            $this->set_fail_response('104', ['message' => 'invalid access']);
        }

        return $this->output->set_output(json_encode($this->result));
    }

    private function _send_mail($user_email, $user_id) {
		$data['code'] = urlencode($this->encryption->encrypt($user_email . '/' . $user_id));
		$email_html = $this->twig->render('email/verify', $data);

		$this->email->initialize(['mailtype' => 'html']);
		$this->email->from('no-reply@pickartyou.com', 'pickartyou');
		$this->email->subject('pickartyou 이메일 인증');
        $this->email->message($email_html);
        $this->email->to($user_email);
        $this->email->send();
    }

    private function _set_user_session($user) {
        $refresh = (array) $this->user_model->get_by_id($user->id);
        $this->session->set_userdata($refresh);
        $this->session->set_userdata(['logged_in' => true]);
    }
}
