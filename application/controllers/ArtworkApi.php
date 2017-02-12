<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * TODO: API 명세 설정 json or html
 */
class ArtworkApi extends MY_Controller {
    public function index() {
    	$this->load->library('twig');
		$this->load->model('artwork_model');

		$query = $this->input->get('q');
		$offset = $this->input->get('offset');
		$limit = $this->input->get('limit');

		$data['artworks'] = $this->artwork_model->gets($query, $offset, $limit);

        $this->twig->display('api/artworks', $data);
    }
}
