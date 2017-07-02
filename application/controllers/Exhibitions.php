<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Exhibitions extends MY_Controller {
    public function __construct() {
        parent::__construct();
    }

    /**
     * @param $exhibition_id
     */
    public function apply($exhibition_id) {
        $this->load->model(['apply_model', 'exhibition_model', 'artwork_model', 'place_model']);
        $this->load->library('applylib');
        $exhibition = $this->exhibition_model->get_by_id($exhibition_id);

        if ($this->accountlib->get_user_type() !== USER_TYPE_ARTIST) {
            alert_and_redirect('창작자 회원만 전시 지원이 가능합니다.');
        }

        $artworks = [];
        if ($this->input->method() === 'get') {
            $artworks = $this->artwork_model->get_apply_status_by_user_id_and_exhibition_id(
                $this->accountlib->get_user_id(),
                $exhibition->id
            );
        } elseif ($this->input->method() === 'post') {
            $artwork_ids = $this->input->post('artwork_id');
            $reason = $this->input->post('reason');

            foreach ($artwork_ids as $artwork_id) {
                $is_valid_artwork = !empty($this->artwork_model->get_bare_by_id($artwork_id));
                if ($is_valid_artwork) {
                    $result = $this->apply_model->get_by_exhibition_id_and_artwork_id($exhibition->id, $artwork_id);
                    if (empty($result)) {
                        $this->apply_model->insert($exhibition->id, $artwork_id, APPLY_STATUS_IN_REVIEW, $reason);
                    }
                }
            }

            $place = $this->place_model->get_bare_by_id($exhibition->place_id);
            $place_owner = $this->user_model->get_by_id($place->user_id);

            $this->applylib->send_apply_email($place_owner->email, $artwork_ids, $this->accountlib->get_user_name());

            alert_and_redirect('지원이 완료되었습니다.', '/places/' . $exhibition->place_id);
        }

        $this->twig->display('places/apply', ['exhibition' => $exhibition, 'artworks' => $artworks, 'place_id' => $exhibition->place_id]);
    }
}
