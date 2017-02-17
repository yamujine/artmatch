<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PickApi extends API_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('pick_model');
    }

    public function index() {
        $this->load->helper('url');

        $type = $this->input->post('type');
        $type_id = $this->input->post('type_id');

        $user_id = $this->accountlib->get_user_id();

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

        return $this->output->set_output(json_encode($this->result));
    }
}
