<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ExhibitionApi extends API_Controller {

    const TYPE_CREATE = 1;
    const TYPE_UPDATE = 2;
    const TYPE_DELETE = 3;

    public function __construct() {
        parent::__construct();
        $this->load->model(['apply_model', 'exhibition_model', 'place_model', 'artwork_model']);
        $this->load->library(['applylib', 'form_validation']);
    }

    public function accept() {
        $applied_artwork_ids = $this->input->post('applied_artwork_ids');
        $exhibition_id = $this->input->post('exhibition_id');
        $accepted_artwork_list = [];
        $to_send_list = [];

        if (!$this->_is_my_exhibition_and_place($exhibition_id)) {
            $this->return_fail_response('501', ['message' => '본인의 전시만 수락 할 수 있습니다.']);
        }
        if (!$this->_is_applied_artworks($exhibition_id, $applied_artwork_ids)) {
            $this->return_fail_response('502', ['message' => '본인의 전시에 지원한 작품만 수락할 수 있습니다.']);
        }

        $already_applied_artwork_ids_objects = $this->exhibition_model->get_artwork_ids_by_exhibition_id($exhibition_id);
        $already_applied_artwork_ids = array_map(function ($value) {
            return $value->artwork_id;
        }, $already_applied_artwork_ids_objects);
        $unapplied_artwork_ids = array_diff($applied_artwork_ids, $already_applied_artwork_ids);

        foreach ($unapplied_artwork_ids as $unapplied_artwork_id) {
            $result = $this->apply_model->update_status($exhibition_id, $unapplied_artwork_id, APPLY_STATUS_ACCEPTED);
            $id = $this->exhibition_model->insert_exhibition_artworks($exhibition_id, $unapplied_artwork_id);

            if ($id === false || $result === false) {
                $this->return_fail_response('500', ['message' => '데이터베이스 업데이트 에러']);
            }

            $accepted_artwork = $this->artwork_model->get_by_id($unapplied_artwork_id);
            $accepted_artwork_list[] = $accepted_artwork;
        }

        foreach ($accepted_artwork_list as $accepted_artwork) {
            //같은 유저의 작품은 하나의 dept로 합침
            $to_send_list[$accepted_artwork->user->email][] = $accepted_artwork;
        }
        $this->applylib->send_accepted_email($to_send_list);
        $this->return_success_response(['message' => '선정이 완료되었습니다.']);
    }

    public function apply() {
        $exhibition_id = $this->input->post('exhibition_id');
        $artwork_ids = $this->input->post('artwork_id');
        $reason = $this->input->post('reason');

        $exhibition = $this->exhibition_model->get_by_id($exhibition_id);
        foreach ($artwork_ids as $artwork_id) {
            $artwork = $this->artwork_model->get_bare_by_id($artwork_id);
            if ($artwork === NULL) {
                $this->return_fail_response('501', ['message' => '존재하지 않는 작품입니다.']);
            } else if ($artwork->user_id !== $this->accountlib->get_user_id()) {
                $this->return_fail_response('502', ['message' => '본인의 작품으로만 지원할 수 있습니다.']);
            }

            $result = $this->_is_applied_artworks($exhibition->id, [$artwork_id]);
            if (!$result) {
                $this->return_fail_response('503', ['message' => '이미 지원한 작품입니다.']);
            }

            $result = $this->apply_model->insert($exhibition->id, $artwork_id, APPLY_STATUS_IN_REVIEW, $reason);
            if (!$result) {
                $this->return_fail_response('504', ['message' => '데이터베이스 인서트 에러']);
            }
        }

        $place = $this->place_model->get_bare_by_id($exhibition->place_id);
        $place_owner = $this->user_model->get_by_id($place->user_id);

        $this->applylib->send_apply_email($place_owner->email, $artwork_ids, $this->accountlib->get_user_name());

        $this->return_success_response(['message' => '지원이 완료되었습니다.']);
    }

    public function cancel_apply() {
        $exhibition_id = $this->input->post('exhibition_id');
        if (empty($exhibition_id)) {
            $this->return_fail_response('100', ['message' => '전시 아이디가 입력되지 않았습니다.']);
        }

        $exhibition = $this->exhibition_model->get_by_id($exhibition_id);
        if (!$exhibition) {
            $this->return_fail_response('501', ['message' => '존재하지 않는 전시 입니다.']);
        }

        $applies = $this->apply_model->get_by_exhibition_id_and_user_id($exhibition->id, $this->accountlib->get_user_id());
        $apply_ids = [];
        foreach ($applies as $apply) {
            if ($apply->status !== APPLY_STATUS_ACCEPTED) {
                $apply_ids[] = $apply->id;
            }
        }

        foreach ($apply_ids as $apply_id) {
            $result = $this->apply_model->delete($apply_id);
            if (!$result) {
                $this->return_fail_response('500', ['message' => '취소 중 데이터베이스 에러']);
            }
        }

        $this->return_success_response(['message' => '지원이 취소되었습니다.']);
    }

    public function create() {
        if (!$this->_is_valid_exhibition_form(self::TYPE_CREATE)) {
            $this->return_fail_response('100', ['message' => validation_errors()]);
        }

        $place_id = $this->input->post('place_id');
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        $title = $this->input->post('title');
        $artwork_count = $this->input->post('artwork_count');
        $is_free = $this->input->post('is_free');
        $is_applicable = $this->input->post('is_applicable');

        $result_id = $this->exhibition_model->insert($place_id, $start_date, $end_date, $title, $artwork_count, $is_free, $is_applicable);
        if ($result_id === NULL) {
            $this->return_fail_response('101', ['message' => $this->db->error()]);
        }

        $this->return_success_response(['message' => '전시가 등록되었습니다.']);
    }

    public function delete() {
        if (!$this->_is_valid_exhibition_form(self::TYPE_DELETE)) {
            $this->return_fail_response('100', ['message' => validation_errors()]);
        }

        $id = $this->input->post('id');
        $result = $this->exhibition_model->delete($id);
        if (!$result) {
            $this->return_fail_response('101', ['message' => $this->db->error()]);
        }

        $this->exhibition_model->delete_artworks($id);

        $this->return_success_response(['message' => '전시가 삭제 되었습니다.']);
    }

    public function update() {
        if (!$this->_is_valid_exhibition_form(self::TYPE_UPDATE)) {
            $this->return_fail_response('100', ['message' => validation_errors()]);
        }

        $id = $this->input->post('id');
        $place_id = $this->input->post('place_id');
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        $title = $this->input->post('title');
        $artwork_count = $this->input->post('artwork_count');
        $is_free = $this->input->post('is_free');
        $is_applicable = $this->input->post('is_applicable');

        $result = $this->exhibition_model->update($id, $place_id, $start_date, $end_date, $title, $artwork_count, $is_free, $is_applicable);
        if (!$result) {
            $this->return_fail_response('101', ['message' => $this->db->error()]);
        }

        $this->return_success_response(['message' => '전시가 수정되었습니다.']);
    }

    private function _is_valid_exhibition_form($type) {
        $this->form_validation->set_error_delimiters('', "\r\n");

        // 수정, 삭제시 전시 ID 검증
        if (in_array($type, [self::TYPE_UPDATE, self::TYPE_DELETE], TRUE)) {
            $this->form_validation->set_rules('id', 'id', 'required|integer', [
                'required' => '전시 ID가 입력되지 않았습니다.',
                'integer' => '전시 ID는 숫자로만 입력이 가능합니다.'
            ]);
        }

        // 생성, 수정시 파라미터 값 전부 검증
        if (in_array($type, [self::TYPE_CREATE, self::TYPE_UPDATE], TRUE)) {
            $this->form_validation->set_rules('place_id', 'place_id', 'required|integer', [
                'required' => '장소 ID가 입력되지 않았습니다.',
                'integer' => '장소 ID는 숫자로만 입력이 가능합니다.'
            ]);
            $this->form_validation->set_rules('start_date', '전시 시작 날짜', 'required|max_length[8]|numeric', [
                'required' => '전시 시작 날짜를 입력해주세요.',
                'max_length' => '전시 시작 날짜가 yyyymmdd 형식이 아닙니다.',
                'numeric' => '전시 시작 날짜가 yyyymmdd 형식이 아닙니다.'
            ]);
            $this->form_validation->set_rules('end_date', '전시 종료 날짜', 'required|max_length[8]|numeric', [
                'required' => '전시 종료 날짜를 입력해주세요.',
                'max_length' => '전시 종료 날짜가 yyyymmdd 형식이 아닙니다.',
                'numeric' => '전시 종료 날짜가 yyyymmdd 형식이 아닙니다.'
            ]);
            $this->form_validation->set_rules('title', '전시 제목', 'required|max_length[70]', [
                'required' => '전시 제목을 입력해주세요.',
                'max_length' => '전시 제목을 70자 이내로 입력해 주세요.'
            ]);
            $this->form_validation->set_rules('is_free', '무료 전시 여부', 'required|numeric', [
                'required' => '무료 전시 여부를 입력해주세요.',
                'integer' => '무료 전시 여부는 숫자로만 입력이 가능합니다.'
            ]);
            $this->form_validation->set_rules('is_applicable', '전시 지원 가능 여부', 'required|numeric', [
                'required' => '전시 지원 가능 여부를 입력해주세요.',
                'integer' => '전시 지원 가능 여부는 숫자로만 입력이 가능합니다.'
            ]);
        }

        return $this->form_validation->run();
    }

    /**
     * 본인의 전시인지 체크
     * @param $exhibition_id
     * @return bool
     */
    private function _is_my_exhibition_and_place($exhibition_id) {
        $exhibition = $this->exhibition_model->get_by_id($exhibition_id);
        if ($exhibition === NULL) {
            return false;
        }

        $place = $this->place_model->get_by_id($exhibition->place_id, $this->accountlib->get_user_id());
        if ($place === NULL) {
            return false;
        }

        return true;
    }

    /**
     * 해당 전시에 지원한 작품인지 체크
     * @param $exhibition_id
     * @param $applied_artworks_ids
     * @return bool
     */
    private function _is_applied_artworks($exhibition_id, array $applied_artworks_ids) {
        foreach ($applied_artworks_ids as $applied_artworks_id) {
            $result = $this->apply_model->get_by_exhibition_id_and_artwork_id($exhibition_id, $applied_artworks_id);
            if ($result === NULL) {
                return false;
            }
        }

        return true;
    }

}
