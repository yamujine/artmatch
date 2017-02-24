<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CommentApi extends API_Controller {

    public $types = [TYPE_ARTWORKS, TYPE_PLACES];

    public function __construct() {
        parent::__construct();
        $this->load->model('comment_model');
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

    /**
     * API 요청시 $type과 $type_id가 실제 존재하는지 검증
     */
    private function validate_type_and_type_id() {
        $type = $this->input->post('type');
        $type_id = $this->input->post('type_id');

        $this->load->model(['artwork_model', 'place_model']);
        if ($type === TYPE_ARTWORKS) {
            $object = $this->artwork_model->get_bare_by_id($type_id);
            $msg = '작품';
        } elseif ($type === TYPE_PLACES) {
            $object = $this->place_model->get_bare_by_id($type_id);
            $msg = '장소';
        }

        if (empty($object)) {
            $this->return_fail_response('404', ['message' => '존재하지 않는 ' . $msg . ' 입니다.']);
        }
    }

    public function index() {
        $type = $this->input->post('type');
        $type_id = $this->input->post('type_id');

        $comments = $this->comment_model->get_comments_by_type_id($type, $type_id);

        $this->return_success_response(['comments' => $comments, 'comment_count' => count($comments)]);
    }

    public function insert() {
        $user_id = $this->accountlib->get_user_id();

        $type = $this->input->post('type');
        $type_id = $this->input->post('type_id');
        $comment = $this->input->post('comment');

        $this->validate_type_and_type_id();

        $result_id = $this->comment_model->insert_comment($type, $user_id, $type_id, $comment);
        if ($result_id === NULL) {
            $this->return_fail_response('101', ['message' => 'Failed to insert']);
        }
        $comment = $this->comment_model->get_by_id($type, $result_id);
        if ($comment === NULL) {
            $this->return_fail_response('102', ['message' => 'Failed to get comment']);
        }

        $this->return_success_response([
            'type' => $type,
            'type_id' => $type_id,
            'comment_id' => $result_id,
            'comment' => $comment,
            'result_type' => 'insert'
        ]);

        $data['user_img'] = $comment->profile_image;
        $data['user_date'] = $comment->posted_at;
        $data['user_comment'] = $comment->comment;

        $this->twig->display('artworks/detail', $data);
    }

    public function update() {
        // TODO: 본인것만 수정 하도록 함
        $user_id = $this->accountlib->get_user_id();

        $type = $this->input->post('type');
        $type_comment_id = $this->input->post('type_comment_id');
        $comment = $this->input->post('comment');

        $result_id = $this->comment_model->update_comment($type, $type_comment_id, $comment);
        if ($result_id === NULL) {
            $this->return_fail_response('101', ['message' => 'Failed to update']);
        }

        $this->return_success_response([
            'type' => $type,
            'type_comment_id' => $type_comment_id,
            'result_id' => $result_id,
            'comment' => $comment,
            'result_type' => 'update'
        ]);
    }

    public function delete() {
        // TODO: 본인것만 삭제 하도록 함
        $user_id = $this->accountlib->get_user_id();

        $type = $this->input->post('type');
        $type_id = $this->input->post('type_id');
        $type_comment_id = $this->input->post('type_comment_id');

        $affected_rows = $this->comment_model->delete_comment($type, $type_comment_id);
        if ($affected_rows === 0) {
            $this->return_fail_response('101', ['message' => 'Failed to delete']);
        }
        $comments = $this->comment_model->get_comments_by_type_id($type, $type_id);

        $this->return_success_response([
            'type' => $type,
            'type_comment_id' => $type_comment_id,
            'comment_count' => count($comments),
            'result_type' => 'delete'
        ]);
    }
}
