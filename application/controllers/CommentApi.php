<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CommentApi extends API_Controller {
    public function __construct() {
        parent::__construct();

        $this->load->model('comment_model');
    }

    public function index() {
        $type = $this->input->post('type');
        $type_id = $this->input->post('type_id');
        try {
            $comments = $this->comment_model->get_comments_by_type_id($type, $type_id);
            $this->set_success_response(['comments' => $comments]);
        } catch (Exception $e) {
            $this->set_fail_response('101', ['message' => $e->getMessage()]);
        }
        $this->return_response();
    }

    public function insert() {
        $user_id = $this->accountlib->get_user_id();

        $type = $this->input->post('type');
        $type_id = $this->input->post('type_id');
        $comment = $this->input->post('comment');

        try {
            $result_id = $this->comment_model->insert_comment($type, $user_id, $type_id, $comment);

            if ($result_id !== NULL) {
                $this->set_success_response([
                    'type' => $type,
                    'type_id' => $type_id,
                    'comment_id' => $result_id,
                    'comment' => $comment,
                    'result_type' => 'insert'
                ]);
            } else {
                $this->set_fail_response('101', ['message' => 'Failed to insert']);
            }
        } catch (Exception $e) {
            $this->set_fail_response('101', ['message' => $e->getMessage()]);
        }
        $this->return_response();
    }

    public function update() {
        // TODO 본인것만 수정 하도록 함
        $user_id = $this->accountlib->get_user_id();

        $type = $this->input->post('type');
        $type_comment_id = $this->input->post('type_comment_id');
        $comment = $this->input->post('comment');

        try {
            $result_id = $this->comment_model->update_comment($type, $type_comment_id, $comment);

            if ($result_id !== NULL) {
                $this->set_success_response(['type' => $type, 'type_comment_id' => $type_comment_id, 'result_id' => $result_id, 'comment' => $comment, 'result_type' => "update"]);
            } else {
                $this->set_fail_response('101', ['message' => 'Failed to update']);
            }
        } catch (Exception $e) {
            $this->set_fail_response('101', ['message' => $e->getMessage()]);
        }
        $this->return_response();
    }

    public function delete() {
        // TODO 본인것만 삭제 하도록 함
        $user_id = $this->accountlib->get_user_id();

        $type = $this->input->post('type');
        $type_comment_id = $this->input->post('type_comment_id');

        try {
            $affected_rows = $this->comment_model->delete_comment($type, $type_comment_id);

            if ($affected_rows !== NULL) {
                $this->set_success_response(['type' => $type, 'type_comment_id' => $type_comment_id, 'result_type' => "delete"]);
            } else {
                $this->set_fail_response('101', ['message' => 'Failed to delete']);
            }
        } catch (Exception $e) {
            $this->set_fail_response('101', ['message' => $e->getMessage()]);
        }


        $this->return_response();
    }
}
