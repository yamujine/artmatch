<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class LandingApi extends API_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->library(['form_validation']);
        $this->load->model('landing_model');
    }

    public function index() {
        $email = $this->input->post('email');

        $this->form_validation->set_error_delimiters('', "\r\n");
        $this->form_validation->set_rules('email', '이메일', 'required|valid_email|is_unique[landing.email]', [
            'required' => '이메일 주소가 입력되지 않았습니다.',
            'valid_email' => '유효한 이메일 주소가 아닙니다.',
            'is_unique' => '이미 알림이 설정된 이메일 주소입니다.'
        ]);

        if ($this->form_validation->run() !== TRUE) {
            $this->return_fail_response('105', ['message' => $this->form_validation->error_string()]);
        }

        if (!$this->landing_model->insert($email)) {
            $this->return_fail_response('102', ['message' => '등록도중 문제가 발생했습니다.']);
        }

        $this->return_success_response(['message' => '등록해주셔서 감사합니다.']);
    }
}
