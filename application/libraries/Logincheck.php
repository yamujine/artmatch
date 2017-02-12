<?php
class Logincheck {

    protected $CI;

    public function __construct() {
        $this->CI =& get_instance();
        $this->CI->load->library('session');
    }

    public function is_login() {
        if (!$this->_validate_session()) {
            return FALSE;
        }
        return TRUE;
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
            return $this->CI->session->userdata('user_id');
        }
        return FALSE;
    }

    public function get_is_auth() {
        if ($this->_validate_session()) {
            return $this->CI->session->userdata('is_auth');
        }
        return FALSE;
    }

    private function _validate_session() {

        $id = $this->CI->session->userdata('id');
        $email = $this->CI->session->userdata('email');
        $type = $this->CI->session->userdata('type');
        $user_id = $this->CI->session->userdata('user_name');
        $profile_image = $this->CI->session->userdata('profile_image');
        $is_auth = $this->CI->session->userdata('is_auth');
        $is_admin = $this->CI->session->userdata('is_admin');

        if ($id == '' || $email == '' || $type == '' || $user_id == '' || $profile_image == '' || $is_auth == '' || $is_admin == '') {
            return FALSE;
        }
        return TRUE;
    }
}