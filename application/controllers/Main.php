<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends MY_Controller {
    public function index() {
        $type = $this->input->get('type') ?: 'artworks';
        $data = $this->_render_content_list($type);

        $this->twig->display('main', $data);
    }

    public function api($type) {
        $limit = $this->input->get('limit');
        $offset = $this->input->get('offset');

        $data = $this->_render_content_list($type, $limit, $offset, false);

        $this->twig->display('api/' . $type, $data);
    }

    private function _render_content_list($type, $limit = 9, $offset = 0, $use_pick_artists = true) {
        $data = [];
        $this->load->model(['artwork_model', 'place_model', 'comment_model', 'pick_model', 'exhibition_model']);
        $query = $this->input->get('q');

        if ($use_pick_artists) {
            $pick_artworks = $this->artwork_model->get_pick_artworks();
            $data['pick_artworks'] = $pick_artworks;
        }

        if ($type === 'artworks') {
            $result = $this->artwork_model->gets($limit, $offset, $query);
            $total_count = $this->artwork_model->get_total_count($query);
        } elseif ($type === 'places') {
            $result = $this->place_model->gets($limit, $offset, $query);
            $total_count = $this->place_model->get_total_count($query);

            foreach ($result as $item) {
                $item->exhibit_artwork_count = $this->exhibition_model->get_exhibit_artwork_count_by_place_id($item->id);
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
