<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends MY_Controller {
    public function index() {
        $user_id = $this->accountlib->get_user_id();
        $type = $this->input->get('type') ?: TYPE_ARTWORKS;

        $data = $this->_render_content_list($user_id, $type);

        $this->twig->display('main', $data);
    }

    public function api($type) {
        $user_id = $this->accountlib->get_user_id();
        $limit = $this->input->get('limit');
        $offset = $this->input->get('offset');

        $data = $this->_render_content_list($user_id, $type, $limit, $offset, $user_id, false);

        $this->twig->display('api/items', $data);
    }

    private function _render_content_list($user_id, $type, $limit = 9, $offset = 0, $use_pick_artists = true) {
        $data = [];
        $this->load->library(['tag']);
        $this->load->model(['artwork_model', 'place_model', 'comment_model', 'pick_model', 'exhibition_model']);
        $query = $this->input->get('q');

        if ($use_pick_artists) {
            $pick_artworks = $this->artwork_model->get_pick_artworks($user_id);
            $data['pick_artworks'] = $pick_artworks;
        }

        if ($type === TYPE_ARTWORKS) {
            $result = $this->artwork_model->gets($limit, $offset, $query, $user_id);
            $total_count = $this->artwork_model->get_total_count($query);
        } elseif ($type === TYPE_PLACES) {
            $result = $this->place_model->gets($limit, $offset, $query, $user_id);
            $total_count = $this->place_model->get_total_count($query);

            foreach ($result as $item) {
                $item->exhibit_artwork_count = $this->exhibition_model->get_exhibit_artwork_count_by_place_id($item->id);
                // Tag html 생성
                $item->tags_html = $this->tag->render_tag_html($item->tags);
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
