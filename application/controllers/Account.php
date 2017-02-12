<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// MY_Controller가 로그인 체크를 하여 /account/login route로 redirect를 하므로, 로그인 컨트롤러는 MY_Controller을 사용하지 않음
class Account extends CI_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->library('twig');
		$this->load->helper('url');
	}

	public function signup() {
		$this->twig->display('account/signup');
	}

	public function login() {
		// 이미 로그인된 경우 메인으로 돌리기
		if ($this->account->is_login()) {
			redirect('/');
		}

		$this->twig->display('login/login');
    }

    public function logout() {
		$this->session->unset_userdata(['id', 'email', 'password', 'type', 'username', 'profile_image','registered_at']);
		$this->session->sess_destroy();

		redirect('/');
	}
}
