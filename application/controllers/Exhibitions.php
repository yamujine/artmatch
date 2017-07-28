<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Exhibitions extends MY_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model(['place_model', 'artwork_model', 'exhibition_model', 'apply_model']);
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
        if ($this->accountlib->get_user_type() !== USER_TYPE_ARTIST) {
            alert_and_redirect('창작자 회원만 전시 지원이 가능합니다.');
        }

        $exhibition = $this->exhibition_model->get_by_id($exhibition_id);

        $artworks = $this->apply_model->get_by_user_id_and_exhibition_id(
            $this->accountlib->get_user_id(),
            $exhibition->id
        );
        if (count($artworks) === 0) {
            alert_and_redirect('작품이 있는 창작자만 지원이 가능합니다.');
        }

        $recent_artwork_id = 0;
        $applied_artwork_count = 0;
        $reason = '';
        foreach ($artworks as $artwork) {
            if (strlen($artwork->apply_status) > 0) {
                $applied_artwork_count += 1;
            }
            if (!empty($artwork->reason) && $recent_artwork_id < $artwork->id) {
                $reason = $artwork->reason;
                $recent_artwork_id = $artwork->id;
            }
        }

        $data = [
            'exhibition' => $exhibition,
            'artworks' => $artworks,
            'applied_artwork_count' => $applied_artwork_count,
            'reason' => $reason,
            'place_id' => $exhibition->place_id
        ];

        $this->twig->display('exhibitions/apply', $data);
    }
}
