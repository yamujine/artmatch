<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Artworks extends MY_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model(['artwork_model', 'place_model', 'exhibition_model', 'comment_model', 'pick_model', 'user_model']);
        $this->load->library(['tag']);
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

        $artwork = $this->artwork_model->get_by_id($artwork_id, $user_id);
        if ($artwork === NULL) {
            alert_and_redirect('존재하지 않는 작품입니다.');
        }

        // 태그 정보
        $artwork->tags_html = $this->tag->render_tag_html($artwork->tags, TYPE_ARTWORKS, false);

        // 전시중 정보
        $artwork->is_now_exhibiting = $this->exhibition_model->is_now_exhibiting_artwork_by_artwork_id($artwork->id);

        // 작품정보
        $data['artwork'] = $artwork;

        // 전시 이력
        $exhibitions = $this->exhibition_model->get_exhibitions_by_artwork_id($artwork_id);
        foreach ($exhibitions as $exhibition) {
            $exhibition_place = $this->place_model->get_bare_by_id($exhibition->place_id);

            $exhibition->place = $exhibition_place;
        }
        $data['exhibitions'] = $exhibitions;

        // 댓글
        $comments = $this->comment_model->get_comments_by_type_id(TYPE_ARTWORKS, $artwork_id);
        $data['comments'] = $comments;
        $data['comment_count'] = count($comments);

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
        $this->form_validation->set_rules('tags', 'tags', 'required|trim|max_length[60]');

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
                $uploaded_image_name = $this->imageupload->upload_image('image');

                if (!empty($artwork_id)) { // 기존 작품 수정
                    if (!empty($uploaded_image_name)) {
                        // 이미지 새로 업로드한 경우 기존의 것 삭제
                        $this->imageupload->delete_image($artwork->image);
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

    public function delete($artwork_id) {
        $artwork = $this->artwork_model->get_bare_by_id($artwork_id);
        if (empty($artwork)) {
            alert_and_redirect('존재하지 않는 장소입니다.');
        }

        if ($artwork->user_id !== $this->accountlib->get_user_id()) {
            alert_and_redirect('본인의 장소만 삭제할 수 있습니다.');
        }

        // 작품
        $is_deleted = $this->artwork_model->delete($artwork_id);

        if ($is_deleted) {
            // 장소 추가 이미지
            $extra_images = $this->artwork_model->get_images_by_id($artwork_id);
            foreach ($extra_images as $extra) {
                $this->imageupload->delete_image($extra->image);
                $this->artwork_model->delete_image($artwork_id, $extra->image);
            }

            // 작품 코멘트
            $this->comment_model->delete_all_comments_by_artwork_id($artwork_id);

            // 작품 Pick
            $this->pick_model->delete_all_picks_by_artwork_id($artwork_id);

            // 전시 내 작품
            $this->exhibition_model->delete_all_artworks_by_artwork_id($artwork_id);
        }

        alert_and_redirect('작품이 삭제되었습니다.', '/?type=' . TYPE_ARTWORKS);
    }
}
