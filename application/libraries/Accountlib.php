<?php

class Accountlib {

    protected $CI;

    public function __construct() {
        $this->CI =& get_instance();
        $this->CI->load->model(['user_model']);
        $this->CI->load->library(['twig', 'encryption', 'email']);
    }

    public function is_login() {
        if (!$this->_validate_session()) {
            return FALSE;
        }

        return TRUE;
    }

    public function is_auth() {
        if ($this->_validate_session()) {
            if ($this->CI->session->userdata('is_auth') === '0') {
                return FALSE;
            } else {
                return TRUE;
            }
        }

        return FALSE;
    }

    public function get_email() {
        if ($this->_validate_session()) {
            return $this->CI->session->userdata('email');
        }

        return FALSE;
    }

    public function get_user_name() {
        if ($this->_validate_session()) {
            return $this->CI->session->userdata('user_name');
        }

        return FALSE;
    }

    public function get_user_id() {
        if ($this->_validate_session()) {
            return $this->CI->session->userdata('id');
        }

        return FALSE;
    }

    public function get_user() {
        if ($this->_validate_session()) {
            $user = new stdClass();
            $user->id = $this->CI->session->userdata('id');
            $user->email = $this->CI->session->userdata('email');
            $user->type = $this->CI->session->userdata('type');
            $user->user_name = $this->CI->session->userdata('user_name');
            $user->profile_image = $this->CI->session->userdata('profile_image');
            $user->is_auth = $this->CI->session->userdata('is_auth');
            $user->is_admin = $this->CI->session->userdata('is_admin');

            return $user;
        }

        return FALSE;
    }

    public function send_email_authentication($user_email, $user_id) {
        $data['code'] = urlencode($this->CI->encryption->encrypt($user_email . '/' . $user_id));
        $email_html = $this->CI->twig->render('email/verify', $data);

        $this->CI->email->initialize(['mailtype' => 'html']);
        $this->CI->email->from('no-reply@pickartyou.com', 'pickartyou');
        $this->CI->email->subject('pickartyou 이메일 인증');
        $this->CI->email->message($email_html);
        $this->CI->email->to($user_email);
        $this->CI->email->send();
    }

    public function generate_user_session($user_id) {
        $userdata = (array)$this->CI->user_model->get_by_id($user_id);
        $this->CI->session->set_userdata($userdata);
    }

    private function _validate_session() {
        $id = $this->CI->session->userdata('id');
        $email = $this->CI->session->userdata('email');
        $type = $this->CI->session->userdata('type');
        $user_name = $this->CI->session->userdata('user_name');
        // $profile_image = $this->CI->session->userdata('profile_image');
        $is_auth = $this->CI->session->userdata('is_auth');
        $is_admin = $this->CI->session->userdata('is_admin');

        if ($id === '' || $email === '' || $type === '' || $user_name === '' || $is_auth === '' || $is_admin === '') {
            return FALSE;
        }

        return TRUE;
    }
}
