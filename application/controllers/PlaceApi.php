<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PlaceApi extends MY_Controller {
    public function index() {
        $this->load->library('twig');
        $this->load->model('place_model');

        $query = $this->input->get('q');
        $offset = $this->input->get('offset');
        $limit = $this->input->get('limit');

        $data['places'] = $this->place_model->gets($limit, $offset, $query);

        $this->twig->display('api/places', $data);
    }
}
