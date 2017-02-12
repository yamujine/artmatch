<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CommentApi extends CI_Controller {
    public function __construct() {
        parent::__construct();

        $this->load->model('comment_model');

        $this->output->set_content_type("application/json");
        $this->result = array('result' => false, 'errorCode' => null, 'body' => null);
    }

    public function index() {
        return $this->output->set_output(json_encode($this->result));
    }

    public function list() {
        // TODO: Uncomment line below
        // $user_id = $this->session->user_id;
        // TODO: Remove line below

        $user_id = 9282;

        $type = $this->input->post('type');
        $type_id = $this->input->post('type_id');
        $comments = $this->comment_model->get_comments_by_type_id($type, $type_id);
        $this->response_success(array('comments' => $comments));
        return $this->output->set_output(json_encode($this->result));
    }

    public function insert() {
		// TODO: Uncomment line below
		// $user_id = $this->session->user_id;
		// TODO: Remove line below

		$user_id = 9282;

		$type = $this->input->post('type');
        $type_id = $this->input->post('type_id');
        $comment = $this->input->post('comment');

		$result_id = $this->comment_model->insert_comment($type, $user_id, $type_id, $comment);

		if ($result_id !== NULL) {
				$this->response_success(array('type' => $type, 'type_id' => $type_id, 'comment' => $comment, 'result_type' => "insert"));
		} else {
				$this->response_fail('101', array('message' => 'Failed to insert'));
		}
        return $this->output->set_output(json_encode($this->result));
    }

    public function update() {
        // TODO 본인것만 수정 하도록 함
        // TODO: Uncomment line below
        // $user_id = $this->session->user_id;
        // TODO: Remove line below

        $user_id = 9282;

        $type = $this->input->post('type');
        $type_comment_id = $this->input->post('type_comment_id');
        $comment = $this->input->post('comment');

		$result_id = $this->comment_model->update_comment($type, $type_comment_id, $comment);

		if ($result_id !== NULL) {
				$this->response_success(array('type' => $type, 'type_comment_id' => $type_comment_id, 'result_id' => $result_id, 'comment' => $comment, 'result_type' => "update"));
		} else {
				$this->response_fail('101', array('message' => 'Failed to update'));
		}
        return $this->output->set_output(json_encode($this->result));
    }

    public function delete() {
        // TODO 본인것만 삭제 하도록 함
        // TODO: Uncomment line below
        // $user_id = $this->session->user_id;
        // TODO: Remove line below

        $user_id = 9282;

        $type = $this->input->post('type');
        $type_comment_id = $this->input->post('type_comment_id');
		$result_id = $this->comment_model->delete_comment($type, $type_comment_id);

        if($result_id) {
                $this->response_success(array('type' => $type, 'type_comment_id' => $type_comment_id, 'result_type' => "delete"));
        } else {
                $this->response_fail('101', array('message' => 'Failed to delete'));
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
