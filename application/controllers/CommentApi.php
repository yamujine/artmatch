<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CommentApi extends CI_Controller {
    public function __construct() {
        parent::__construct();

        $this->load->model('comment_model');

        $this->output->set_content_type("application/json");
        $this->result = array('result' => false, 'errorCode' => null, 'body' => null);
    }

    public function index($id) {
        $this->load->library(['form_validation']);
        $this->load->helper('url');

        // Form validation
        $this->form_validation->set_rules('type', 'type', 'required|trim');
        $this->form_validation->set_rules('type_id', 'type_id', 'required|trim');
        $this->form_validation->set_rules('comment', 'comment', 'required');

        if ($this->input->method() === 'post') {
            if ($this->form_validation->run() === TRUE) {
                // TODO: Uncomment line below
                // $user_id = $this->session->user_id;
                // TODO: Remove line below
                $user_id = 9282;
                $type = $this->input->post('type');
                $type_id = $this->input->post('type_id');
                $comment = $this->input->post('comment');

                $result_id = NULL;
                $result_type = NULL;

                if (1) {
                    $result_id = $this->comment_model->insert_comment($type, $user_id, type_id);
                    $result_type = 'insert';
                } else if(1) {

                    $result_type = 'delete';
                } else if(1) {

                }

                if ($result_id !== NULL) {
                    $this->response_success(array('type_id' => $type_id, 'result_type' => $result_type, 'type' => $type));
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

    public function insert() {

    }

    public function update() {

    }

    public function delete() {
        // TODO 본인것만 삭제 하도록 함
        // TODO: Uncomment line below
        // $user_id = $this->session->user_id;
        // TODO: Remove line below
        $user_id = 9282;

        $type = $this->input->post('type');
        print($type);
        $type_comment_id = NULL;

        if ($type === "artwork") {
            $type_comment_id = $this->input->post('artwork_comment_id');
        } else if ($type === "place") {
            $type_comment_id = $this->input->post('place_comment_id');
        }

        $this->comment_model->delete_comment($type, $type_comment_id);
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
