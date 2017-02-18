<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PickApi extends API_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('pick_model');
    }

    public function index() {
        $this->load->library(['form_validation']);
        $this->load->helper('url');

        // Form validation
        $this->form_validation->set_rules('type', 'type', 'required|trim');
        $this->form_validation->set_rules('type_id', 'type_id', 'required|trim');

        if ($this->input->method() === 'post') {
            if ($this->form_validation->run() === TRUE) {
                $user_id = $this->accountlib->get_user_id();

                $type_id = $this->input->post('type_id');
                $type = $this->input->post('type');

                $result_id = NULL;
                $result_type = NULL;

                try {
                    if ($this->pick_model->check_pick($type, $user_id, $type_id)) {
                        $result_id = $this->pick_model->delete_pick($type, $user_id, $type_id);
                        $result_type = 'off';
                    } else {
                        $result_id = $this->pick_model->insert_pick($type, $user_id, $type_id);
                        $result_type = 'on';
                    }

                    if ($result_id !== NULL) {
                        $this->set_success_response(['type_id' => $type_id, 'result_type' => $result_type, 'type' => $type]);
                    } else {
                        $this->set_fail_response('101', ['message' => 'Failed to insert into DB']);
                    }
                } catch (Exception $e) {
                    $this->set_fail_response('104', ['message' => $e->getMessage()]);
                }
            } else {
                $this->set_fail_response('102', ['message' => 'validation error']);
            }
        } else {
            $this->set_fail_response('103', ['message' => 'no data']);
        }

        return $this->output->set_output(json_encode($this->result));
    }
}
