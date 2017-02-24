<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ContentsApi extends API_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model(['artwork_model', 'place_model']);
    }

    public function delete_extra_images() {
        if ($this->input->method() === 'post') {
            $type = $this->input->post('type') ?: TYPE_ARTWORKS;
            $type_id = $this->input->post('type_id');
            $delete_images = $this->input->post('delete_images');

            if ($type === TYPE_ARTWORKS) {
                $object = $this->artwork_model->get_bare_by_id($type_id);
                $msg = '작품';
            } elseif ($type === TYPE_PLACES) {
                $object = $this->place_model->get_bare_by_id($type_id);
                $msg = '장소';
            }

            if (empty($object)) {
                $this->return_fail_response('404', ['message' => '존재하지 않는 ' . $msg . ' 입니다.']);
            }

            if ($object->user_id !== $this->accountlib->get_user_id()) {
                $this->return_fail_response('103', ['message' => '본인의 ' . $msg . '만 변경할 수 있습니다.']);
            }

            if (empty($delete_images)) {
                $this->return_fail_response('500', ['message' => '선택된 이미지가 없습니다.']);
            }

            if ($type === TYPE_ARTWORKS) {
                foreach ($delete_images as $delete_image) {
                    $this->imageupload->delete_image($delete_image);
                    $this->artwork_model->delete_image($type_id, $delete_image);
                }
            } elseif ($type === TYPE_PLACES) {
                foreach ($delete_images as $delete_image) {
                    $this->imageupload->delete_image($delete_image);
                    $this->place_model->delete_image($type_id, $delete_image);
                }
            }
            $this->return_success_response();
        }
    }
}
