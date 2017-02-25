<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ExhibitionApi extends API_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(['apply_model', 'exhibition_model', 'place_model', 'artwork_model']);
        $this->load->library('applylib');
    }

    public function accept() {
        $applied_artwork_ids = $this->input->post('applied_artwork_ids');
        $exhibition_id = $this->input->post('exhibition_id');
        $email_list = [];

        if (!$this->_is_my_exhibition($exhibition_id)) {
            $this->return_fail_response('501', ['message' => '본인의 전시만 수락 할 수 있습니다.']);
        }
        if (!$this->_is_applied_artworks($exhibition_id, $applied_artwork_ids)) {
            $this->return_fail_response('502', ['message' => '본인의 전시에 지원한 작품만 수락할 수 있습니다.']);
        }

        foreach ($applied_artwork_ids as $applied_artwork_id) {
            $result = $this->apply_model->update_status($exhibition_id, $applied_artwork_id, APPLY_STATUS_ACCEPTED);
            $id = $this->exhibition_model->insert_exhibition_artworks($exhibition_id, $applied_artwork_id);

            if ($id === false || $result === false) {
                $this->return_fail_response('500', ['message' => '데이터베이스 업데이트 에러']);
            }
            //email 발송 대상 리스트
            $artwork = $this->artwork_model->get_by_id($applied_artwork_id);
            $email_list[] = $artwork->user->email;
        }
        //중복값 제거
        $email_list = array_unique($email_list);

        $this->applylib->send_accepted_email($email_list, $exhibition_id, $applied_artwork_ids);
        $this->return_success_response();
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
