<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Artworks extends MY_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model(['artwork_model', 'place_model', 'exhibition_model', 'comment_model', 'pick_model', 'user_model']);
        $this->load->library('tag');
        $this->load->helper('url');
    }

    /**
     * /artworks 주소는 메인으로 리다이렉트
     */
    public function index() {
        redirect('/');
    }

    public function detail($artwork_id) {
        $data = [];
        $user_id = $this->accountlib->get_user_id();

        $is_pick = $this->pick_model->is_artwork_pick($user_id, $artwork_id);
        $data['is_pick'] = $is_pick;

        $artwork = $this->artwork_model->get_by_id($artwork_id);
        if ($artwork) {
            // 태그 정보
            $artwork->tags_html = $this->tag->render_tag_html($artwork->tags, TYPE_ARTWORKS, false);

            // 작품정보
            $data['artwork'] = $artwork;

            // 전시 이력
            $exhibitions = $this->exhibition_model->get_exhibitions_by_artwork_id($artwork_id);
            foreach ($exhibitions as $exhibition) {
                $exhibition->place = $this->place_model->get_bare_by_id($exhibition->place_id);
            }
            $data['exhibitions'] = $exhibitions;

            // 댓글
            $comments = $this->comment_model->get_comments_by_type_id(TYPE_ARTWORKS, $artwork_id);
            foreach ($comments as $comment) {
                // TODO join 걸어서 정보 가져오도록
                // 댓글 작성자 정보
                $user = $this->user_model->get_by_id($comment->user_id);
                $comment->user = $user;
            }
            $data['comments'] = $comments;

            // 작가정보
            // TODO join 걸어서 정보 가져오도록
            $user = $this->user_model->get_by_id($artwork->user_id);
            $data['user'] = $user;
        }

        // 조회수 증가
        $this->artwork_model->update_view_count_by_id($artwork_id);

        $this->twig->display('artworks/detail', $data);
    }

    public function edit($artwork_id = null) {
        $this->load->library(['form_validation', 'upload', 'tag', 'imageupload']);

        $data = [];

        // Form validation
        $this->form_validation->set_rules('title', 'title', 'required|trim');
        $this->form_validation->set_rules('status', 'status', 'required');
        $this->form_validation->set_rules('for_sale', 'for_sale', 'required');
        $this->form_validation->set_rules('use_comment', 'use_comment', 'required');
        $this->form_validation->set_rules('tags', 'tags', 'required|trim');

        $user_id = $this->accountlib->get_user_id();
        $status = $this->input->post('status');
        $title = $this->input->post('title');
        $description = $this->input->post('description');
        $for_sale = $this->input->post('for_sale');
        $use_comment = $this->input->post('use_comment');
        $tags = $this->tag->refine_tags($this->input->post('tags'));
        $delete_images = $this->input->post('delete_images');

        if (empty($artwork_id)) {
            // 작품 신규 등록시
            if ($this->accountlib->get_user_type() !== USER_TYPE_ARTIST) {
                alert_and_redirect('창작자 회원만 작품 등록이 가능합니다.');
            }
        } else {
            // 기존 작품 수정시
            $artwork = $this->artwork_model->get_by_id($artwork_id);
            if ($artwork->user_id !== $this->accountlib->get_user_id()) {
                alert_and_redirect('본인의 작품만 수정할 수 있습니다.');
            }

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
