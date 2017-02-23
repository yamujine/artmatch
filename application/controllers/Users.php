<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends MY_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model(['user_model', 'pick_model', 'artwork_model', 'place_model']);
    }

    public function detail($user_name) {
        $data = $this->_get_user_details($user_name);

        $this->twig->display('users/mypage', $data);
    }

    public function me() {
        $user_name = $this->accountlib->get_user_name();
        $pick_type = $this->input->get('type');

        $data = $this->_get_user_details($user_name, true, $pick_type);

        $this->twig->display('users/mypage', $data);
    }

    private function _get_user_details($user_name, $is_my_page = false, $pick_type = '') {
        $user = $this->user_model->get_by_user_name($user_name);

        // 내 작품, 장소 리스트
        if ($user->type === USER_TYPE_ARTIST) {
            $mine = $this->artwork_model->get_all_by_user_id($user->id);
        } else if ($user->type === USER_TYPE_PLACE_OWNER) {
            $mine = $this->place_model->get_all_by_user_id($user->id);
        }

        // 받은 pick 카운트
        if ($user->type === USER_TYPE_ARTIST) {
            $given_pick_count = $this->pick_model->get_given_artwork_pick_by_user_id($user->id);
        } else if ($user->type === USER_TYPE_PLACE_OWNER) {
            $given_pick_count = $this->pick_model->get_given_place_pick_by_user_id($user->id);
        }

        // 내가 pick한 작품 장소 리스트
        $my_picks = [];
        if ($is_my_page && !empty($pick_type)) {
            if ($pick_type === TYPE_ARTWORKS) {
                $my_picks = $this->pick_model->get_artworks_by_user_id($user->id);
            } elseif ($pick_type === TYPE_PLACES) {
                $my_picks = $this->pick_model->get_places_by_user_id($user->id);
            }
        }

        return [
            'user' => $user,
            'is_my_page' => $is_my_page,
            'pick_type' => $pick_type,
            'mine' => $mine,
            'given_pick_count' => $given_pick_count,
            'my_picks' => $my_picks
        ];
    }
}
