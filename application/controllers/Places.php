<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Places extends MY_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model(['place_model', 'artwork_model', 'exhibition_model']);
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
            // 전시 작품 이력
            $exhibitions = $this->exhibition_model->get_exhibitions_by_place_id($place_id);
            foreach ($exhibitions as $exhibition) {
                $exhibition->artwork = $this->artwork_model->get_bare_by_id($exhibition->artwork_id);
            }
            $data['exhibitions'] = $exhibitions;
        }

        $this->twig->display('places/detail', $data);
    }

    public function edit($place_id = null) {
        $this->load->library(['form_validation', 'upload', 'tag', 'imageupload']);
        $this->load->helper('url');

        // Form validation
        $this->form_validation->set_rules('name', 'name', 'required|trim');
        $this->form_validation->set_rules('status', 'status', 'required');
        $this->form_validation->set_rules('use_comment', 'use_comment', 'required');
        $this->form_validation->set_rules('tags', 'tags', 'required|trim');

        $user_id = $this->accountlib->get_user_id();
        $status = $this->input->post('status');
        $name = $this->input->post('name');
        $address = $this->input->post('address');
        $description = $this->input->post('description');
        $use_comment = $this->input->post('use_comment');
        $tags = $this->tag->refine_tags($this->input->post('tags'));

        $data = [];

        if (!empty($place_id)) {
            $place = $this->place_model->get_by_id($place_id);
            $place_array = json_decode(json_encode($place), true); // StdClass to Array conversion
            $data = array_merge($data, $place_array);
        }

        if ($this->input->method() === 'post') {
            if ($this->form_validation->run() === TRUE) {
                // Upload representative image first
                $uploaded_image_name = $this->imageupload->upload_images('image');

                if (!empty($place_id)) { // 기본 작품 수정
                    if (!empty($uploaded_image_name)) {
                        // 이미지 새로 업로드한 경우 기존의 것 삭제
                        $this->imageupload->delete_image($uploaded_image_name);
                    } else {
                        // 새로 업로드한 이미지 없는 경우 기존 이미지 사용
                        $place = $this->place_model->get_bare_by_id($place_id);
                        $uploaded_image_name = $place->image;
                    }

                    // 추가 이미지 중 삭제 원하는 이미지 제거 및 record 삭제
                    if (!empty($delete_images)) {
                        foreach ($delete_images as $delete_image) {
                            $this->imageupload->delete_image($delete_image);
                            $this->place_model->delete_image($place_id, $delete_image);
                        }
                    }

                    $result_id = $this->place_model->update(
                        $place_id,
                        $user_id,
                        $status,
                        $name,
                        $address,
                        $description,
                        $uploaded_image_name,
                        $use_comment,
                        $tags
                    );
                } else { // 작품 신규 등록
                    $result_id = $this->place_model->insert(
                        $user_id,
                        $status,
                        $name,
                        $address,
                        $description,
                        $uploaded_image_name,
                        $use_comment,
                        $tags
                    );
                }

                if ($result_id !== NULL) {
                    // Upload extra images
                    $uploaded_image_names = $this->imageupload->upload_bulk_images('extra_images');
                    if (!empty($uploaded_image_names)) {
                        $this->place_model->insert_images($result_id, $uploaded_image_names);
                    }
                    redirect('/places/' . $result_id);
                } else {
                    $data['error'] = $this->db->error();
                }
            } else {
                $data['error'] = validation_errors();
            }
            // Return requested values and errors
            $data = array_merge($data, $this->input->post());
        }
        $this->twig->display('places/edit', $data);
    }
}
