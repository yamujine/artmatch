<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class LandingApi extends API_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('landing_model');
    }

    public function index() {
        $email = $this->input->post('email');
        $landing_history = $this->landing_model->get_by_email($email);

        if (!empty($landing_history)) {
            $this->set_fail_response('101', ['message' => '이미 알림이 설정된 이메일 입니다.']);
        } else {
            if (!$this->landing_model->insert($email)) {
                $this->set_fail_response('102', ['message' => '등록도중 문제가 발생했습니다.']);
            } else {
                $this->set_success_response(['message' => '등록해주셔서 감사합니다.']);
            }
        }

        $this->return_response();
    }
}
