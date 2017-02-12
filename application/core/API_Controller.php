<?php

/*
 * 이 컨트롤러를 상속받아 구현하는 컨트롤러는 application/json 타입의 리턴을 기본으로 하고, 로그인 체크를 하지 않음
 */
class API_Controller extends CI_Controller {

	public $result;

	public function __construct() {
		parent::__construct();
		$this->output->set_content_type('application/json');
		$this->result = ['result' => false, 'errorCode' => null, 'body' => null];
	}

	public function set_success_response($body) {
		$this->result['result'] = true;
		$this->result['body'] = $body;
	}

	public function set_fail_response($error_code, $body) {
		$this->result['result'] = false;
		$this->result['errorCode'] = $error_code;
		$this->result['body'] = $body;
	}
}
