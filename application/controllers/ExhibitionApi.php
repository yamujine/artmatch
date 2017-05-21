<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ExhibitionApi extends API_Controller {

    const TYPE_CREATE = 1;
    const TYPE_UPDATE = 2;
    const TYPE_DELETE = 3;

    public function __construct() {
        parent::__construct();
        $this->load->model(['apply_model', 'exhibition_model', 'place_model', 'artwork_model']);
        $this->load->library('applylib');
    }

    public function accept() {
        $applied_artwork_ids = $this->input->post('applied_artwork_ids');
        $exhibition_id = $this->input->post('exhibition_id');
        $accepted_artwork_list = [];
        $to_send_list = [];

        if (!$this->_is_my_exhibition($exhibition_id)) {
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

    public function create() {
        if (!$this->_is_valid_exhibition_form(self::TYPE_CREATE)) {
            $this->return_fail_response('100', ['message' => validation_errors()]);
        }

        $place_id = $this->input->post('place_id');
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        $artwork_count = $this->input->post('artwork_count');
        $is_free = $this->input->post('is_free');

        $result_id = $this->exhibition_model->insert($place_id, $start_date, $end_date, $artwork_count, $is_free);
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
        $artwork_count = $this->input->post('artwork_count');
        $is_free = $this->input->post('is_free');


        $result = $this->exhibition_model->update($id, $place_id, $start_date, $end_date, $artwork_count, $is_free);
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

            // TODO: 기획 확인 - 기존 무료 전시 여부 제거해야하는지, 전시 상태 새로 추가되는 것인지
            $this->form_validation->set_rules('is_free', '무료 전시 여부', 'required|numeric', [
                'required' => '무료 전시 여부를 입력해주세요.',
                'integer' => '무료 전시 여부는 숫자로만 입력이 가능합니다.'
            ]);
            $this->form_validation->set_rules('status', '전시 상태', 'required|numeric', [
                'required' => '전시 상태를 입력해주세요.',
                'integer' => '전시 상태는 숫자로만 입력이 가능합니다.'
            ]);
        }

        return $this->form_validation->run();
    }

    /**
     * 본인의 전시인지 체크
     * @param $exhibition_id
     * @return bool
     */
    private function _is_my_exhibition($exhibition_id) {
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
    private function _is_applied_artworks($exhibition_id, $applied_artworks_ids) {
        foreach ($applied_artworks_ids as $applied_artworks_id) {
            $result = $this->apply_model->get_by_exhibition_id_and_artwork_id($exhibition_id, $applied_artworks_id);
            if ($result === NULL) {
                return false;
            }
        }

        return true;
    }

}
