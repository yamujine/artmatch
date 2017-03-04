<?php

/*
 * 이 컨트롤러를 상속받아 구현하는 컨트롤러는 application/json 타입의 리턴을 기본으로 함
 */

class API_Controller extends CI_Controller {
    /* @var array 로그인 없이 사용 가능한 함수 이름을 입력 */
    public $allow_without_login = [];
    /* @var array 이메일 인증이 필요 없이 사용 가능한 함수 이름을 입력 */
    public $allow_without_auth = [];

    public $result;

    public function __construct() {
        parent::__construct();
        $this->output->set_content_type('application/json');
        $this->result = ['result' => false, 'errorCode' => null, 'body' => null];
        $this->twig->addGlobal('session', $_SESSION);
        /* @var array $defined_constants 사용자 정의 상수를 Twig global로 등록 */
        $defined_constants = get_defined_constants(true)['user'];
        foreach ($defined_constants as $constant => $value) {
            $this->twig->addGlobal($constant, $value);
        }
    }

    public function return_success_response(array $body = []) {
        $this->result['result'] = true;
        $this->result['body'] = $body;

        $this->return_response();
    }

    public function return_fail_response($error_code, array $body) {
        $this->result['result'] = false;
        $this->result['errorCode'] = $error_code;
        $this->result['body'] = $body;

        $this->return_response();
    }

    /**
     * 이 함수는 현재 설정된 API Response를 리턴하고 바로 종료함
     */
    public function return_response() {
        $this->output->set_output(json_encode($this->result))->_display();
        die;
    }
}
