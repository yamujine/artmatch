<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Places extends MY_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model(['place_model', 'artwork_model', 'exhibition_model', 'artwork_comment_model', 'place_comment_model', 'place_pick_model', 'apply_model']);
        $this->load->library('tag');
        $this->load->helper('url');
    }

    /**
     * /artworks 주소는 메인으로 리다이렉트
     */
    public function index() {
        redirect('/');
    }

    public function detail($place_id) {
        $data = [];
        $user_id = $this->accountlib->get_user_id();

        $place = $this->place_model->get_by_id($place_id, $user_id);
        if ($place === NULL) {
            alert_and_redirect('존재하지 않는 장소입니다.');
        }

        // 태그 정보
        $place->tags_html = $this->tag->render_tag_html($place->tags, TYPE_PLACES, false);

        // 전시 정보
        $place->is_now_exhibiting = $this->exhibition_model->is_now_exhibiting_by_place_id($place->id);
        $place->is_applicable = $this->exhibition_model->is_applicable_by_place_id($place->id);

        // 장소정보
        $data['place'] = $place;

        $exhibitions_now = [];
        // 전시 작품 이력
        $exhibitions = $this->exhibition_model->get_with_artwork_count_by_place_id($place_id);
        foreach ($exhibitions as $exhibition) {
            // 전시 기간 상태
            $today = date('Y-m-d 00:00:00');
            if ($today < $exhibition->start_date) {
                $exhibition->status = EXHIBITION_NOT_STARTED;
            } else if ($today >= $exhibition->start_date && $today <= $exhibition->end_date) {
                $exhibition->status = EXHIBITION_NOW_EXHIBITING;
            } else if ($today > $exhibition->end_date) {
                $exhibition->status = EXHIBITION_END;
            }

            $exhibition_artwork_id_objects = $this->exhibition_model->get_artwork_ids_by_exhibition_id($exhibition->id);
            $exhibition_artwork_ids = array_map(function ($value) {
                return $value->artwork_id;
            }, $exhibition_artwork_id_objects);
            if (!empty($exhibition_artwork_ids)) {
                $exhibition->artworks = $this->artwork_model->get_by_ids($exhibition_artwork_ids);

                // 지금 전시중
                if ($today >= $exhibition->start_date && $today <= $exhibition->end_date) {
                    if (isset($exhibition->artworks)) {
                        shuffle($exhibition->artworks);
                        $exhibition->artists = array_unique(array_map(function ($value) {
                            return $value->user->user_name;
                        }, $exhibition->artworks));
                    }
                    $exhibitions_now[] = $exhibition;
                }
            }

            $data['exhibition_artwork_count'] = count($exhibition_artwork_ids);
        }
        $data['exhibitions'] = $exhibitions;
        $data['exhibitions_now'] = $exhibitions_now;

        // 올린 작품 여부
        $data['has_artworks'] = $this->artwork_model->is_exists_by_user_id($user_id);

        // 댓글
        $comments = $this->place_comment_model->get_comments_by_place_id($place_id, 3, 0);
        $data['comments'] = array_reverse($comments);
        $data['comment_count'] = $this->place_comment_model->get_count_of_comments_by_place_id($place_id);

        // 조회수 증가
        $this->place_model->update_view_count_by_id($place_id);

        $this->twig->display('places/detail', $data);
    }

    public function edit($place_id = null) {
        $this->load->library(['form_validation', 'upload', 'tag', 'imageupload']);

        $user_id = $this->accountlib->get_user_id();
        $name = $this->input->post('name');
        $area = $this->input->post('area');
        $address = $this->input->post('address');
        $description = $this->input->post('description');
        $use_comment = $this->input->post('use_comment');
        $tags = $this->tag->refine_tags($this->input->post('tags'));

        $data = [];

        if (empty($place_id)) {
            // 장소 신규 등록시
            if ($this->accountlib->get_user_type() !== USER_TYPE_PLACE_OWNER) {
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
        }

        if ($this->input->method() === 'post') {
            if ($this->_is_valid_place_form()) {
                // Upload representative image first
                $uploaded_image_name = $this->imageupload->upload_image('image');

                if (!empty($place_id)) { // 기본 작품 수정
                    if (!empty($uploaded_image_name)) {
                        // 이미지 새로 업로드한 경우 기존의 것 삭제
                        $this->imageupload->delete_image($place->image);
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
                        $name,
                        $area,
                        $address,
                        $description,
                        $uploaded_image_name,
                        $use_comment,
                        $tags
                    );
                } else { // 작품 신규 등록
                    $result_id = $this->place_model->insert(
                        $user_id,
                        $name,
                        $area,
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

    public function delete($place_id) {
        $this->load->library('imageupload');

        $place = $this->place_model->get_bare_by_id($place_id);
        if (empty($place)) {
            alert_and_redirect('존재하지 않는 장소입니다.');
        }

        if ($place->user_id !== $this->accountlib->get_user_id()) {
            alert_and_redirect('본인의 장소만 삭제할 수 있습니다.');
        }

        // 장소
        $is_deleted = $this->place_model->delete($place_id);

        if ($is_deleted) {
            // 장소 추가 이미지
            $extra_images = $this->place_model->get_images_by_id($place_id);
            foreach ($extra_images as $extra) {
                $this->imageupload->delete_image($extra->image);
                $this->place_model->delete_image($place_id, $extra->image);
            }

            // 장소 코멘트
            $this->place_comment_model->delete_all_comments_by_place_id($place_id);

            // 장소 Pick
            $this->place_pick_model->delete_all_picks_by_place_id($place_id);

            // 전시
            $this->exhibition_model->delete_all_by_place_id($place_id);

            // 장소 내 작품
            $this->exhibition_model->delete_all_artworks_by_place_id($place_id);

            // 지원 내역
            $exhibitions = $this->exhibition_model->get_by_place_id($place_id);
            $exhibition_ids = array_map(function ($value) {
                return $value->id;
            }, $exhibitions);

            if (!empty($exhibition_ids)) {
                foreach ($exhibition_ids as $exhibition_id) {
                    $this->apply_model->delete_by_exhibition_id($exhibition_id);
                }
            }
        }

        alert_and_redirect('장소가 삭제되었습니다.', '/?type=' . TYPE_PLACES);
    }

    public function exhibitions($place_id) {
        // 장소명 등록된 전시 / 종료된 전시 전시 - 이름 / 일정 / 작품 수 / 상태
        $place = $this->place_model->get_by_id($place_id);
        if ($place === NULL) {
            alert_and_redirect('존재하지 않는 장소입니다.');
        }

        if ($place->user_id !== $this->accountlib->get_user_id()) {
            alert_and_redirect('본인의 장소만 수정할 수 있습니다.');
        }

        $exhibitions = $this->exhibition_model->get_with_artwork_count_by_place_id($place_id);
        $closed_exhibition_count = 0;
        foreach ($exhibitions as $exhibition) {
            $today = date('Y-m-d 00:00:00');
            if ($today < $exhibition->start_date) {
                $exhibition->status = EXHIBITION_NOT_STARTED;
            } else if ($today >= $exhibition->start_date && $today <= $exhibition->end_date) {
                $exhibition->status = EXHIBITION_NOW_EXHIBITING;
            } else if ($today > $exhibition->end_date) {
                $exhibition->status = EXHIBITION_END;
                $closed_exhibition_count++;
            }
        }

        $data = [
            'place' => $place,
            'exhibitions' => $exhibitions,
            'closed_exhibition_count' => $closed_exhibition_count
        ];

        $this->twig->display('places/exhibitions', $data);
    }

    private function _is_valid_place_form() {
        $this->form_validation->set_error_delimiters('', "\r\n");

        $this->form_validation->set_rules('name', '장소명', 'trim|required|max_length[20]', [
            'required' => '장소명이 입력되지 않았습니다.',
            'max_length' => '장소명은 최대 20자(공백 포함)까지 입력이 가능합니다.'
        ]);
        $this->form_validation->set_rules('area', '장소 키워드', 'trim|required|max_length[6]', [
            'required' => '장소 대표 키워드를 입력해주세요.',
            'max_length' => '장소 대표 키워드는 최대 6자(공백 포함)까지 입력이 가능합니다.'
        ]);

        $this->form_validation->set_rules('address', '주소', 'trim|required'); // 다음지도 API로 입력 되므로 검증 불필요

        $this->form_validation->set_rules('tags', '태그', 'trim|required|max_length[60]', [
            'required' => '태그를 입력해주세요.',
            'max_length' => '태그는 최대 60자(공백 포함)까지 입력이 가능합니다.'
        ]);
        $this->form_validation->set_rules('use_comment', '댓글 허용 여부', 'required', [
            'required' => '댓글 허용 여부를 선택해주세요.'
        ]);

        return $this->form_validation->run();
    }
}
