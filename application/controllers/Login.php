<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// MY_Controller가 로그인 체크를 하여 /login route로 redirect를 하므로, 로그인 컨트롤러는 MY_Controller을 사용하지 않음
class Login extends CI_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->library('twig');
	}

	public function index() {
        if (!$this->logincheck->is_login()) {
            $user = NULL;
        } else {
            $user = [
                'email' => $this->logincheck->get_email(),
                'auth' => $this->logincheck->get_is_auth()
            ];
        }

        $data = ['user' => $user];
        $this->twig->display('login/login', $data);
    }
}
