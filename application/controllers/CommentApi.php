<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CommentApi extends API_Controller {

    public $types = [TYPE_ARTWORKS, TYPE_PLACES];

    public function __construct() {
        parent::__construct();
        $this->load->model(['artwork_comment_model', 'place_comment_model']);
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
        $limit = $this->input->post('limit');
        $offset = $this->input->post('offset');

        if ($type === TYPE_ARTWORKS) {
            $comments = $this->artwork_comment_model->get_comments_by_artwork_id($type_id, $limit, $offset);
            $comment_count = $this->artwork_comment_model->get_count_of_comments_by_artwork_id($type_id);
        } elseif ($type === TYPE_PLACES) {
            $comments = $this->place_comment_model->get_comments_by_place_id($type_id, $limit, $offset);
            $comment_count = $this->place_comment_model->get_count_of_comments_by_place_id($type_id);
        }

        $comments = $this->twig->render('api/comments', ['comments' => array_reverse($comments), 'type' => $type]);
        $this->return_success_response(['comments' => $comments, 'comment_count' => $comment_count]);
    }

    public function insert() {
        $user_id = $this->accountlib->get_user_id();

        $type = $this->input->post('type');
        $type_id = $this->input->post('type_id');
        $comment = $this->input->post('comment');

        $this->validate_type_and_type_id();

        if ($type === TYPE_ARTWORKS) {
            $result_id = $this->artwork_comment_model->insert_comment($user_id, $type_id, $comment);
        } elseif ($type === TYPE_PLACES) {
            $result_id = $this->place_comment_model->insert_comment($user_id, $type_id, $comment);
        }
        if ($result_id === NULL) {
            $this->return_fail_response('101', ['message' => 'Failed to insert']);
        }

        $comment = $this->_get_comment($type, $result_id);
        if (empty($comment)) {
            $this->return_fail_response('102', ['message' => 'Failed to get comment']);
        }

        if ($type === TYPE_ARTWORKS) {
            $comment_count = $this->artwork_comment_model->get_count_by_artwork_id($type_id);
        } elseif ($type === TYPE_PLACES) {
            $comment_count = $this->place_comment_model->get_count_by_place_id($type_id);
        }
        if ($comment_count === null) {
            $this->return_fail_response('102', ['message' => 'Failed to count comment']);
        }

        $insert_comment = $this->twig->render('api/comments',
            ['comments' => [$comment], 'type' => $type, 'type_id' => $type_id]);

        $this->return_success_response([
            'type' => $type,
            'type_id' => $type_id,
            'comment_id' => $result_id,
            'comment' => $comment,
            'comment_count' => $comment_count,
            'insert_comment' => $insert_comment,
            'result_type' => 'insert'
        ]);
    }

    public function update() {
        $user_id = $this->accountlib->get_user_id();

        $type = $this->input->post('type');
        $type_comment_id = $this->input->post('type_comment_id');
        $comment = $this->input->post('comment');

        $current_comment = $this->_get_comment($type, $type_comment_id);
        if (empty($current_comment)) {
            $this->return_fail_response('101', ['message' => '존재하지 않는 코멘트 입니다.']);
        }

        if ($current_comment->user_id !== $user_id) {
            $this->return_fail_response('101', ['message' => '본인의 코멘트만 수정할 수 있습니다.']);
        }

        if ($type === TYPE_ARTWORKS) {
            $result_id = $this->artwork_comment_model->update_comment($type_comment_id, $comment);
        } elseif ($type === TYPE_PLACES) {
            $result_id = $this->place_comment_model->update_comment($type_comment_id, $comment);
        }
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

    private function _get_comment($type, $comment_id) {
        if ($type === TYPE_ARTWORKS) {
            $comment = $this->artwork_comment_model->get($comment_id);
        } elseif ($type === TYPE_PLACES) {
            $comment = $this->place_comment_model->get($comment_id);
        }

        return $comment;
    }

    public function delete() {
        $user_id = $this->accountlib->get_user_id();

        $type = $this->input->post('type');
        $type_comment_id = $this->input->post('type_comment_id');

        $current_comment = $this->_get_comment($type, $type_comment_id);
        if (empty($current_comment)) {
            $this->return_fail_response('101', ['message' => '존재하지 않는 코멘트 입니다.']);
        }
        if ($current_comment->user_id !== $user_id) {
            $this->return_fail_response('101', ['message' => '본인의 코멘트만 삭제할 수 있습니다.']);
        }

        if ($type === TYPE_ARTWORKS) {
            $affected_rows = $this->artwork_comment_model->delete_comment($type_comment_id);
        } elseif ($type === TYPE_PLACES) {
            $affected_rows = $this->place_comment_model->delete_comment($type_comment_id);
        }
        if ($affected_rows === 0) {
            $this->return_fail_response('101', ['message' => 'Failed to delete']);
        }

        if ($type === TYPE_ARTWORKS) {
            $comment_count = $this->artwork_comment_model->get_count_by_artwork_id($current_comment->artwork_id);
        } elseif ($type === TYPE_PLACES) {
            $comment_count = $this->place_comment_model->get_count_by_place_id($current_comment->place_id);
        }
        if ($comment_count === null) {
            $this->return_fail_response('102', ['message' => 'Failed to count comment']);
        }

        $this->return_success_response([
            'type' => $type,
            'type_comment_id' => $type_comment_id,
            'comment_count' => $comment_count,
            'result_type' => 'delete'
        ]);
    }
}
