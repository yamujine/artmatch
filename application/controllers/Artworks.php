<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Artworks extends MY_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->model(['artwork_model', 'place_model', 'exhibition_model']);
	}

	public function index() {
		$artworks = $this->artwork_model->gets();
		$data = ['artworks' => $artworks];
		$this->twig->display('artworks/list', $data);
	}

	public function detail($artwork_id) {
		$data = [];

		$artwork = $this->artwork_model->get_by_id($artwork_id);
		if ($artwork) {
			$data['artwork'] = $artwork;
			// 전시 이력
			$exhibitions = $this->exhibition_model->get_exhibitions_by_artwork_id($artwork_id);
			foreach ($exhibitions as $exhibition) {
				$exhibition->place = $this->place_model->get_bare_by_id($exhibition->place_id);
			}
			$data['exhibitions'] = $exhibitions;
		}

		$this->twig->display('artworks/detail', $data);
	}

	public function edit($artwork_id = null) {
		$this->load->library(['form_validation', 'upload', 'tag', 'imageupload']);
		$this->load->helper('url');

		$data = [];

		// Form validation
		$this->form_validation->set_rules('title', 'title', 'required|trim');
		$this->form_validation->set_rules('status', 'status', 'required');
		$this->form_validation->set_rules('for_sale', 'for_sale', 'required');
		$this->form_validation->set_rules('use_comment', 'use_comment', 'required');
		$this->form_validation->set_rules('tags', 'tags', 'required|trim');

		$user_id = $this->account->get_user_id();
		$status = $this->input->post('status');
		$title = $this->input->post('title');
		$description = $this->input->post('description');
		$for_sale = $this->input->post('for_sale');
		$use_comment = $this->input->post('use_comment');
		$tags = $this->tag->refine_tags($this->input->post('tags'));
		$delete_images = $this->input->post('delete_images');

		// 기존 작품 수정인 경우 작품 정보 미리 입력
		if (!empty($artwork_id)) {
			$artwork = $this->artwork_model->get_by_id($artwork_id);
			$artwork_array = json_decode(json_encode($artwork), true); // StdClass to Array conversion
			$data = array_merge($data, $artwork_array);
		}

		if ($this->input->method() === 'post') {
			if ($this->form_validation->run() === TRUE) {
				// Upload representative image first
				$uploaded_image_name = $this->imageupload->upload_images('image');

				if (!empty($artwork_id)) { // 기존 작품 수정
					if (!empty($uploaded_image_name)) {
						// 이미지 새로 업로드한 경우 기존의 것 삭제
						$this->imageupload->delete_image($uploaded_image_name);
					} else {
						// 새로 업로드한 이미지 없는 경우 기존 이미지 사용
						$artwork = $this->artwork_model->get_bare_by_id($artwork_id);
						$uploaded_image_name = $artwork->image;
					}

					// 추가 이미지 중 삭제 원하는 이미 제거 및 record 삭제
					if (!empty($delete_images)) {
						foreach ($delete_images as $delete_image) {
							$this->imageupload->delete_image($delete_image);
							$this->artwork_model->delete_image($artwork_id, $delete_image);
						}
					}

					$result_id = $this->artwork_model->update(
						$artwork_id,
						$user_id,
						$status,
						$title,
						$description,
						$uploaded_image_name,
						$for_sale,
						$use_comment,
						$tags
					);
				} else { // 작품 신규 등록
					$result_id = $this->artwork_model->insert(
						$user_id,
						$status,
						$title,
						$description,
						$uploaded_image_name,
						$for_sale,
						$use_comment,
						$tags
					);
				}

				if ($result_id !== NULL) {
					// Upload extra images
					$uploaded_image_names = $this->imageupload->upload_bulk_images('extra_images');
					if (!empty($uploaded_image_names)) {
						$this->artwork_model->insert_images($result_id, $uploaded_image_names);
					}
					redirect('/artworks/' . $result_id);
				} else {
					$data['error'] = $this->db->error();
				}
			} else {
				$data['error'] = validation_errors();
			}
			// Return requested values and errors
			$data = array_merge($data, $this->input->post());
		}
		$this->twig->display('artworks/edit', $data);
	}
}
