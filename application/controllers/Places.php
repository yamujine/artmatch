<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Places extends MY_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('place_model');
    }

    public function index() {
        $places = $this->place_model->gets();
        $data = ['places' => $places];
        $this->twig->display('places/list', $data);
    }

	public function detail($place_id) {
		$data = [];

		$place = $this->place_model->get_by_id($place_id);
		if ($place) {
			$data['place'] = $place;
		}

		$this->twig->display('places/detail', $data);
	}

    public function upload() {
        $this->load->library(['form_validation', 'upload', 'tag', 'imageupload']);
        $this->load->helper('url');

        // Form validation
        $this->form_validation->set_rules('name', 'name', 'required|trim');
        $this->form_validation->set_rules('status', 'status', 'required');
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
                $result_id = $this->place_model->insert(
                    $user_id,
                    $this->input->post('status'),
                    $this->input->post('name'),
                    $this->input->post('address'),
                    $this->input->post('description'),
                    $uploaded_image_name,
                    $this->input->post('use_comment'),
                    $this->tag->refine_tags($this->input->post('tags'))
                );
                if ($result_id !== NULL) {
                    // Upload extra images
                    $uploaded_image_names = $this->imageupload->upload_bulk_images('extra_images');
                    if (!empty($uploaded_image_names)) {
						$this->place_model->insert_images($result_id, $uploaded_image_names);
					}
                    redirect('/places/' . $result_id);
                } else {
                    $data['error'] = 'Failed to insert into DB';
                }

            } else {
                $data['error'] = validation_errors();
            }
            // Return requested values and errors
            $data = array_merge($data, $this->input->post());
        }

        $this->twig->display('places/upload', $data);
    }
}
