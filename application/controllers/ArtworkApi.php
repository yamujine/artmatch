<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ArtworkApi extends MY_Controller {
    public function index() {
        $this->load->library('twig');
        $this->load->model('artwork_model');

        $query = $this->input->get('q');
        $offset = $this->input->get('offset');
        $limit = $this->input->get('limit');

        $data['artworks'] = $this->artwork_model->gets($limit, $offset, $query);

        $this->twig->display('api/artworks', $data);
    }
}
