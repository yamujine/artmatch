<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Artworks extends MY_Controller
{
	public function detail($artwork_number)
	{
		echo $artwork_number . ' artwork detail';
	}

	public function upload()
	{
		$this->load->model('artwork_model');
		$this->load->library(['form_validation', 'upload', 'tag']);
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
				$uploaded_image_name = $this->_upload_images('image');
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
					$uploaded_image_names = $this->_upload_bulk_images('extra_images');
					$this->artwork_model->insert_images($result_id, $uploaded_image_names);

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

	private function _upload_images($param, $generate_thumbs = true)
	{
		// Load upload library
		$this->upload->initialize([
			'upload_path' => './uploads/',
			'allowed_types' => 'gif|jpg|png|jpeg',
			'file_ext_tolower' => TRUE,
			'max_size' => 2048, // 2MB
			'encrypt_name' => TRUE
		]);

		// Upload Image
		$this->upload->do_upload($param);

		// Generate Thumbnails
		if ($generate_thumbs) {
			$this->_generate_thumbnails($this->upload->data('full_path'));
		}

		return $this->upload->data('file_name');
	}

	private function _upload_bulk_images($param, $generate_thumbs = true)
	{
		$this->upload->initialize([
			'upload_path' => './uploads/',
			'allowed_types' => 'gif|jpg|png|jpeg',
			'file_ext_tolower' => TRUE,
			'max_size' => 2048, // 2MB
			'encrypt_name' => TRUE
		]);

		// Upload Images
		$this->upload->do_multi_upload($param);

		foreach ($this->upload->get_multi_upload_data() as $uploaded_data) {
			if ($generate_thumbs) {
				$this->_generate_thumbnails($uploaded_data['full_path']);
			}
			$uploaded_file_names[] = $uploaded_data['file_name'];
		}

		return $uploaded_file_names;
	}

	private function _generate_thumbnails($original_image_path)
	{
		/*
		 * _thumb -> 512x512
		 * _thumb_small -> 128x128
		 */
		$this->load->library('image_lib');
		$default_config = [
			'image_library' => 'gd2',
			'source_image' => $original_image_path,
			'create_thumb' => TRUE,
			'maintain_ratio' => TRUE
		];

		$this->image_lib->initialize(array_merge($default_config, ['width' => 512, 'height' => 512]));
		$this->image_lib->resize();

		$this->image_lib->initialize(array_merge($default_config, ['width' => 128, 'height' => 128, 'thumb_marker' => '_thumb_small']));
		$this->image_lib->resize();
	}
}
