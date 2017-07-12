<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends MY_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model(['user_model', 'artwork_pick_model', 'place_pick_model', 'artwork_model', 'place_model']);
    }

    /**
     * [Public]
     * 마이페이지 메인
     */
    public function detail($user_name) {
        if ($user_name === 'me') {
            $user_name = $this->accountlib->get_user_name();
        }

        $data = $this->_get_user_details($user_name);
        // 내 작품, 장소 리스트
        $objects = [];
        if ($data['user']->type === USER_TYPE_ARTIST) {
            $objects = $this->artwork_model->get_all_by_user_id($data['user']->id);
        } else if ($data['user']->type === USER_TYPE_PLACE_OWNER) {
            $objects = $this->place_model->get_all_by_user_id($data['user']->id);
        }
        $data['my_objects'] = $objects;

        $this->twig->display('users/mypage', $data);
    }

    /**
     * [Private]
     * 장소 소유자의 지원자 확인 / 작품 소유자의 지원 현황 페이지
     */
    public function apply_status() {
        $user_details = $this->_get_user_details($this->accountlib->get_user_name());

        $this->load->model(['exhibition_model', 'apply_model']);
        $exhibition_list = [];
        $total_applied_count = 0;

        $type = ($user_details['user']->type === USER_TYPE_ARTIST) ? 'apply' : 'applicant';

        $data = ['type' => $type];
        if ($type === 'apply') {
            $exhibition_objects = $this->apply_model->get_by_user_id($user_details['user']->id);
            $exhibition_ids = array_unique(array_map(function ($value) {
                return $value->exhibition_id;
            }, $exhibition_objects));

            $exhibitions = $this->exhibition_model->get_by_ids($exhibition_ids);

            $exhibition_list = [];
            foreach ($exhibitions as $exhibition) {
                $applied_artworks = $this->artwork_model->get_apply_status_by_exhibition_id($exhibition->id);

                if (!empty($applied_artworks)) {
                    $exhibition->applied_artworks = $applied_artworks;
                    $exhibition_list[] = $exhibition;
                }
            }

            $data = array_merge($data, [
                'exhibitions' => $exhibition_list
            ]);

        } elseif ($type === 'applicant') {
            $places = $this->place_model->get_all_bare_by_user_id($this->accountlib->get_user_id());
            foreach ($places as $place) {
                $exhibitions = $this->exhibition_model->get_by_place_id($place->id);

                foreach ($exhibitions as $exhibition) {
                    $applied_artworks = $this->artwork_model->get_apply_status_by_exhibition_id($exhibition->id);

                    if (!empty($applied_artworks)) {
                        $exhibition->applied_artworks = $applied_artworks;
                        $total_applied_count += count($applied_artworks);
                        $exhibition_list[] = $exhibition;
                    }
                }
            }

            $data = array_merge($data, [
                'exhibitions' => $exhibition_list,
                'total_applied_count' => $total_applied_count
            ]);
        }

        $data = array_merge($data, $user_details);

        $this->twig->display('users/apply_status', $data);
    }

    /**
     * [Private]
     * 유저가 픽한 것 조회
     */
    public function picks() {
        $user_details = $this->_get_user_details($this->accountlib->get_user_name());

        $pick_type = $this->input->get('type');
        $pick_type = $pick_type ?: TYPE_ARTWORKS;

        $my_picks = [];
        if ($pick_type === TYPE_ARTWORKS) {
            $my_picks = $this->artwork_model->get_picked_by_user_id($this->accountlib->get_user_id());
        } elseif ($pick_type === TYPE_PLACES) {
            $my_picks = $this->place_model->get_picked_by_user_id($this->accountlib->get_user_id());
        }

        $data = [
            'pick_type' => $pick_type,
            'my_picks' => $my_picks
        ];
        $data = array_merge($data, $user_details);

        $this->twig->display('users/picks', $data);
    }

    private function _get_user_details($user_name) {
        $user = $this->user_model->get_by_user_name($user_name);

        if ($user === null) {
            alert_and_redirect('존재하지 않는 회원입니다');
        }

        // 내 작품, 장소 수
        $total_object_count = 0;
        if ($user->type === USER_TYPE_ARTIST) {
            $total_object_count = $this->artwork_model->get_count_by_user_id($user->id);
        } else if ($user->type === USER_TYPE_PLACE_OWNER) {
            $total_object_count = $this->place_model->get_count_by_user_id($user->id);
        }

        // 받은 pick 카운트
        $given_pick_count = 0;
        if ($user->type === USER_TYPE_ARTIST) {
            $given_pick_count = $this->artwork_pick_model->get_given_artwork_pick_by_user_id($user->id);
        } else if ($user->type === USER_TYPE_PLACE_OWNER) {
            $given_pick_count = $this->place_pick_model->get_given_place_pick_by_user_id($user->id);
        }

        return [
            'user' => $user,
            'is_my_page' => $user->id === $this->accountlib->get_user_id(),
            'total_object_count' => $total_object_count,
            'given_pick_count' => $given_pick_count
        ];
    }
}
