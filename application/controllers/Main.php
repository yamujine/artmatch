<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends MY_Controller {
    public function index() {
        $type = $this->input->get('type') ?: TYPE_ARTWORKS;
        $data = $this->_render_content_list($type);

        $this->twig->display('main', $data);
    }

    public function api($type) {
        $limit = $this->input->get('limit');
        $offset = $this->input->get('offset');

        $data = $this->_render_content_list($type, $limit, $offset, false);

        $this->twig->display('api/items', $data);
    }

    private function _render_content_list($type, $limit = 9, $offset = 0, $use_pick_artists = true) {
        $data = [];
        $this->load->library(['tag', 'address']);
        $this->load->model(['artwork_model', 'place_model', 'comment_model', 'pick_model', 'exhibition_model']);
        $query = $this->input->get('q');

        if ($use_pick_artists) {
            $pick_artworks = $this->artwork_model->get_pick_artworks();
            $data['pick_artworks'] = $pick_artworks;

            foreach ($pick_artworks as $item) {
                // pick
                $user_id = $this->accountlib->get_user_id();
                $is_pick = $this->pick_model->is_artwork_pick($user_id, $item->id);
                $item->is_pick = $is_pick;
            }
        }

        if ($type === TYPE_ARTWORKS) {
            $result = $this->artwork_model->gets($limit, $offset, $query);
            $total_count = $this->artwork_model->get_total_count($query);

            foreach ($result as $item) {
                // pick
                $user_id = $this->accountlib->get_user_id();
                $is_pick = $this->pick_model->is_artwork_pick($user_id, $item->id);
                $item->is_pick = $is_pick;
            }
        } elseif ($type === TYPE_PLACES) {
            $result = $this->place_model->gets($limit, $offset, $query);
            $total_count = $this->place_model->get_total_count($query);

            foreach ($result as $item) {
                $item->exhibit_artwork_count = $this->exhibition_model->get_exhibit_artwork_count_by_place_id($item->id);
                // Tag html 생성
                $item->tags_html = $this->tag->render_tag_html($item->tags);

                // 주소 앞 2개 파트만 표시하고 자름
                $item->address = $this->address->extract_foremost_part($item->address);

                // pick
                $user_id = $this->accountlib->get_user_id();
                $is_pick = $this->pick_model->is_place_pick($user_id, $item->id);
                $item->is_pick = $is_pick;
            }
        }

        $data = array_merge($data, [
            'type' => $type,
            'query' => $query,
            'items' => $result,
            'total_count' => $total_count
        ]);

        return $data;
    }
}
