<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PickApi extends API_Controller {

    public $types = [TYPE_ARTWORKS, TYPE_PLACES];

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->model(['artwork_pick_model', 'place_pick_model']);
        $this->validate_types();
    }

    /**
     * API 요청시 $type 파라미터 검증
     */
    private function validate_types() {
        $type = $this->input->post('type');

        if (!in_array($type, $this->types, false)) {
            $this->return_fail_response('101', ['message' => '올바른 타입이 아닙니다.']);
        }
    }

    public function index() {
        $user_id = $this->accountlib->get_user_id();

        $type = $this->input->post('type');
        $type_id = $this->input->post('type_id');

        $result_id = $result_type = null;
        if ($type === TYPE_ARTWORKS) {
            if ($this->artwork_pick_model->check_pick($user_id, $type_id)) {
                $result_id = $this->artwork_pick_model->delete_pick($user_id, $type_id);
                $result_type = 'off';
            } else {
                $result_id = $this->artwork_pick_model->insert_pick($user_id, $type_id);
                $result_type = 'on';
            }
            $pick_count = $this->artwork_pick_model->get_count_by_artwork_id($type_id);
        } elseif ($type === TYPE_PLACES) {
            if ($this->place_pick_model->check_pick($user_id, $type_id)) {
                $result_id = $this->place_pick_model->delete_pick($user_id, $type_id);
                $result_type = 'off';
            } else {
                $result_id = $this->place_pick_model->insert_pick($user_id, $type_id);
                $result_type = 'on';
            }
            $pick_count = $this->place_pick_model->get_count_by_place_id($type_id);
        }

        if ($result_id === NULL) {
            $this->return_fail_response('101', ['message' => 'Failed to insert into DB']);
        }

        $this->return_success_response([
            'type' => $type,
            'type_id' => $type_id,
            'result_type' => $result_type,
            'pick_count' => $pick_count
        ]);
    }
}
