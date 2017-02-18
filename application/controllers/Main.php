<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends MY_Controller {
    public function index() {
        $this->load->model(['artwork_model', 'place_model', 'comment_model', 'pick_model']);
        $type = $this->input->get('type') ?: 'artworks';
        $query = $this->input->get('q');

        if ($type === 'artworks') {
            $result = $this->artwork_model->gets(9, 0, $query);
            $total_count = $this->artwork_model->get_total_count($query);
        } elseif ($type === 'places') {
            $result = $this->place_model->gets(9, 0, $query);
            $total_count = $this->place_model->get_total_count($query);

            if (!empty($result)) {
                foreach ($result as $res) {
                    $res->pick_count = $this->pick_model->get_count_by_place_id($res->id);
                    $res->comment_count = $this->comment_model->get_count_by_place_id($res->id);
                }
            }
        }

        $data = [
            'type' => $type,
            'query' => $query,
            'items' => $result,
            'total_count' => $total_count
        ];
        $this->twig->display('main', $data);
    }
}
