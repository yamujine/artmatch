<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * TODO: API 명세 설정 json or html
 */
class PlaceApi extends MY_Controller {
    public function index() {
        $this->load->library('twig');
        $this->load->model('place_model');

        $query = $this->input->get('q');
        $offset = $this->input->get('offset');
        $limit = $this->input->get('limit');

        $data['places'] = $this->place_model->gets($query, $offset, $limit);

        $this->twig->display('api/places', $data);
    }
}
