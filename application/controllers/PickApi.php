<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PickApi extends CI_Controller {
    public function __construct() {
        parent::__construct();

        $this->load->model('pick_model');

        $this->output->set_content_type("application/json");
        $this->result = array('result' => false, 'errorCode' => null, 'body' => null);
    }

    public function index() {
        $this->load->library(['form_validation']);
        $this->load->helper('url');

        // Form validation
        $this->form_validation->set_rules('type', 'type', 'required|trim');
        $this->form_validation->set_rules('place_id', 'place_id', 'required|trim');

        if ($this->input->method() === 'post') {
            if ($this->form_validation->run() === TRUE) {
                // TODO: Uncomment line below
                // $user_id = $this->session->user_id;
                // TODO: Remove line below
                $user_id = 9282;
                $place_id = $this->input->post('place_id');
                $type = $this->input->post('type');

                $result_id = NULL;
                $result_type = NULL;

                if ($this->pick_model->check_pick($type, $user_id, $place_id)) {
                    $result_id = $this->pick_model->delete_pick($type, $user_id, $place_id);
                    $result_type = "off";
                } else {
                    $result_id = $this->pick_model->insert_pick($type, $user_id, $place_id);
                    $result_type = "on";
                }

                if ($result_id !== NULL) {
                    $this->response_success(array('place_id' => $place_id, 'result_type' => $result_type, 'type' => $type));
                } else {
                    $this->response_fail('101', array('message' => 'Failed to insert into DB'));
                }
            } else {
                $this->response_fail('102', array('message' => 'validation error'));
            }
        } else {
            $this->response_fail('103', array('message' => 'no data'));
        }

        return $this->output->set_output(json_encode($this->result));
    }

    public function response_success($body) {
        $this->result['result'] = true;
        $this->result['body'] = $body;
    }

    public function response_fail($error_code, $body) {
        $this->result['result'] = false;
        $this->result['body'] = $body;
        $this->result['errorCode'] = $error_code;
    }
}
