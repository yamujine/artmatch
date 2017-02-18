<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Places extends MY_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model(['place_model', 'artwork_model', 'exhibition_model', 'comment_model', 'pick_model']);
    }

    public function index() {
        $places = $this->place_model->gets();
        $data = ['places' => $places];
        $this->twig->display('places/list', $data);
    }

    public function detail($place_id) {
        $data = [];
        $user_id = $this->accountlib->get_user_id();

        $is_pick = $this->pick_model->is_place_pick($user_id, $place_id);
        $data['is_pick'] = $is_pick;

        $place = $this->place_model->get_by_id($place_id);
        if ($place) {
            // 장소정보
            $data['place'] = $place;

            // 전시 작품 이력
            $exhibitions = $this->exhibition_model->get_exhibitions_by_place_id($place_id);
            foreach ($exhibitions as $exhibition) {
                $exhibition_artwork_ids = $this->exhibition_model->get_artwork_ids_by_exhibition_id($exhibition->id);
                if (!empty($exhibition_artwork_ids)) {
                    $exhibition->artworks = $this->artwork_model->get_bare_by_ids($exhibition_artwork_ids);
                }
            }
            $data['exhibitions'] = $exhibitions;

            // 댓글
            $comments = $this->comment_model->get_comments_by_type_id('place', $place_id);
            $data['comments'] = $comments;
        }

        // 조회수 증가
        $this->artwork_model->update_view_count_by_id($place_id);

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

        $this->form_validation->set_rules('exhibition_start_date', 'exhibition_start_date', 'required|exact_length[8]|trim');
        $this->form_validation->set_rules('exhibition_end_date', 'exhibition_end_date', 'required|exact_length[8]|trim');
        $this->form_validation->set_rules('exhibition_artwork_count', 'exhibition_artwork_count', 'required|numeric|trim');
        $this->form_validation->set_rules('exhibition_is_free', 'exhibition_is_free', 'required|trim');

        $user_id = $this->accountlib->get_user_id();
        $status = $this->input->post('status');
        $name = $this->input->post('name');
        $address = $this->input->post('address');
        $description = $this->input->post('description');
        $use_comment = $this->input->post('use_comment');
        $tags = $this->tag->refine_tags($this->input->post('tags'));

        $exhibition_start_date = $this->input->post('exhibition_start_date');
        $exhibition_end_date = $this->input->post('exhibition_end_date');
        $exhibition_artwork_count = $this->input->post('exhibition_artwork_count');
        $exhibition_is_free = $this->input->post('exhibition_is_free');

        $data = [];

        if (empty($place_id)) {
            // 장소 신규 등록시
            if ($this->accountlib->get_user_type() !== '1') {
                alert_and_redirect('공간소유자 회원만 장소 등록이 가능합니다.');
            }
        } else {
            // 기존 장소 수정시
            $place = $this->place_model->get_by_id($place_id);
            if ($place->user_id !== $this->accountlib->get_user_id()) {
                alert_and_redirect('본인의 장소만 수정할 수 있습니다.');
            }

            $place_array = json_decode(json_encode($place), true); // StdClass to Array conversion
            $data = array_merge($data, $place_array);

            // 전시 데이터 수정
            $exhibition = $this->exhibition_model->get_by_place_id($place->id);
            $exhibition_array = json_decode(json_encode($exhibition), true);
            $exhibition_data = [];
            foreach (array_keys($exhibition_array) as $key) {
                $new_key = 'exhibition_' . $key;
                $exhibition_data[$new_key] = $exhibition_array[$key];
            }
            $data = array_merge($data, $exhibition_data);
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

                    $result_exhibition_id = $this->exhibition_model->update_by_place_id(
                        $result_id,
                        $exhibition_start_date,
                        $exhibition_end_date,
                        $exhibition_artwork_count,
                        $exhibition_is_free
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

                    $result_exhibition_id = $this->exhibition_model->insert(
                        $result_id,
                        $exhibition_start_date,
                        $exhibition_end_date,
                        $exhibition_artwork_count,
                        $exhibition_is_free
                    );
                }

                if ($result_id !== NULL && $result_exhibition_id !== NULL) {
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
