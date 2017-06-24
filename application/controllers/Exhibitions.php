<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Exhibitions extends MY_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model(['place_model', 'artwork_model', 'exhibition_model', 'artwork_comment_model', 'place_comment_model', 'place_pick_model', 'apply_model']);
        $this->load->library('tag');
        $this->load->helper('url');
    }

    /**
     * /exhibitions 주소는 메인으로 리다이렉트
     */
    public function index() {
        redirect('/');
    }

    /**
     * @param $exhibition_id
     */
    public function apply($exhibition_id) {
        $this->load->model('apply_model');
        $this->load->library('applylib');
//        $default_exhibition = $this->exhibition_model->get_one_by_place_id($exhibition_id);
//
//        if ($this->accountlib->get_user_type() !== USER_TYPE_ARTIST) {
//            alert_and_redirect('창작자 회원만 전시 지원이 가능합니다.');
//        }
//
//        if ($this->input->method() === 'get') {
//            $artworks = $this->artwork_model->get_apply_status_by_user_id_and_exhibition_id(
//                $this->accountlib->get_user_id(),
//                $default_exhibition->id
//            );
//        } elseif ($this->input->method() === 'post') {
//            $artwork_ids = $this->input->post('artwork_id');
//            $reason = $this->input->post('reason');
//
//            foreach ($artwork_ids as $artwork_id) {
//                $is_valid_artwork = !empty($this->artwork_model->get_bare_by_id($artwork_id));
//                if ($is_valid_artwork) {
//                    $result = $this->apply_model->get_by_exhibition_id_and_artwork_id($default_exhibition->id, $artwork_id);
//                    if (empty($result)) {
//                        $this->apply_model->insert($default_exhibition->id, $artwork_id, APPLY_STATUS_IN_REVIEW, $reason);
//                    }
//                }
//            }
//
//            $place = $this->place_model->get_bare_by_id($exhibition_id);
//            $place_owner = $this->user_model->get_by_id($place->user_id);
//
//            $this->applylib->send_apply_email($place_owner->email, $artwork_ids, $this->accountlib->get_user_name());
//
//            alert_and_redirect('지원이 완료되었습니다.', '/places/' . $exhibition_id);
//        }

//        $this->twig->display('exhibitions/apply', ['exhibition' => $default_exhibition, 'artworks' => $artworks, 'place_id' => $exhibition_id]);
        $this->twig->display('exhibitions/apply');
    }
}
