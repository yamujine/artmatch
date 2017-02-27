<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class LoginCheck {
    protected $CI;

    public function __construct() {
        $this->CI =& get_instance();
        $this->CI->load->helper('url');
    }

    /**
     * Controller constructor 내에 $this->allow_without_login = ['function_name']; 으로 작성하면 그 함수는 로그인 체크를 하지 않음
     */
    public function check_login() {
        if ($this->CI->accountlib->is_login() === false) {
            if (empty($this->CI->allow_without_login) || !in_array($this->CI->router->method, $this->CI->allow_without_login)) {
                redirect('/account/login');
            }
        } elseif ($this->CI->accountlib->is_auth() === false) {
            if (empty($this->CI->allow_without_auth) || !in_array($this->CI->router->method, $this->CI->allow_without_auth)) {
                redirect('/account/not_authenticated');
            }
        }
    }
}
