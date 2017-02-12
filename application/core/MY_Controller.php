<?php
/*
 * 이 컨트롤러를 상속받아 구현하는 컨트롤러는 Twig 템플릿 엔진을 기본으로 로드함
 */
class MY_Controller extends CI_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->library('twig');
		$this->load->helper('url');
		// Twig 관련 글로벌 설정은 이곳 또는 application/libraries/Twig.php 에 작성
		$this->twig->addGlobal('session', $_SESSION);

		if ($this->accountlib->is_login() === false) {
			redirect('/account/login');
		}
	}
}

if (!class_exists('API_Controller')) {
	require_once APPPATH.'core/API_Controller.php';
}
