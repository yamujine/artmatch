<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends MY_Controller {
	public function index() {
		$this->load->model(['artwork_model', 'place_model']);
		$type = $this->input->get('type') ?: 'artworks';
		$query = $this->input->get('q');

		if ($type === 'artworks') {
			$result = $this->artwork_model->gets(9, 0, $query);
		} elseif ($type === 'places') {
			$result = $this->place_model->gets(9, 0, $query);
		}

		$data = [
			'type' => $type,
			'query' => $query,
			'items' => $result
		];
		$this->twig->display('main', $data);
	}
}
