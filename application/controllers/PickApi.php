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

            if ($type === TYPE_ARTWORKS) {
                $pick_count = $this->pick_model->get_count_by_artwork_id($type_id);
            } else if ($type === TYPE_PLACES) {
                $pick_count = $this->pick_model->get_count_by_place_id($type_id);
            } else {
                throw new Exception("type error. type=".$type);
            }

            if ($result_id !== NULL) {
                $this->set_success_response(['type_id' => $type_id, 'result_type' => $result_type, 'type' => $type, 'pick_count' => $pick_count]);
            } else {
                $this->set_fail_response('101', ['message' => 'Failed to insert into DB']);
            }
        } catch (Exception $e) {
            $this->set_fail_response('104', ['message' => $e->getMessage()]);
        }

        $this->return_response();
    }
}
