<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Artworks extends MY_Controller {
	public function __construct() {
			parent::__construct();
			$this->load->model('artwork_model');
	}

	public function index() {
			$artworks = $this->artwork_model->gets();
			$data = ['artworks' => $artworks];
			$this->twig->display('artworks/list', $data);
	}

	public function detail($artwork_number)
	{
		echo $artwork_number . ' artwork detail';
	}

	public function upload()
	{
		$this->load->library(['form_validation', 'upload', 'tag', 'imageupload']);
		$this->load->helper('url');

		// Form validation
		$this->form_validation->set_rules('title', 'title', 'required|trim');
		$this->form_validation->set_rules('status', 'status', 'required');
		$this->form_validation->set_rules('for_sale', 'for_sale', 'required');
		$this->form_validation->set_rules('use_comment', 'use_comment', 'required');
		$this->form_validation->set_rules('tags', 'tags', 'required|trim');

		$data = [];

		if ($this->input->method() === 'post') {
			if ($this->form_validation->run() === TRUE) {
				// TODO: Uncomment line below
				// $user_id = $this->session->user_id;
				// TODO: Remove line below
				$user_id = 9282;

				// Upload representative image first
				$uploaded_image_name = $this->imageupload->upload_images('image');
				$result_id = $this->artwork_model->insert(
					$user_id,
					$this->input->post('status'),
					$this->input->post('title'),
					$this->input->post('description'),
					$uploaded_image_name,
					$this->input->post('for_sale'),
					$this->input->post('use_comment'),
					$this->tag->refine_tags($this->input->post('tags'))
				);
				if ($result_id !== NULL) {
					// Upload extra images
					$uploaded_image_names = $this->imageupload->upload_bulk_images('extra_images');
					if (!empty($uploaded_image_names)) {
						$this->artwork_model->insert_images($result_id, $uploaded_image_names);
					}
					redirect('/artworks/' . $result_id);
				} else {
					$data['error'] = 'Failed to insert into DB';
				}

			} else {
				$data['error'] = validation_errors();
			}
			// Return requested values and errors
			$data = array_merge($data, $this->input->post());
		}

		$this->twig->display('artworks/upload', $data);
	}
}
