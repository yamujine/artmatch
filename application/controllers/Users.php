<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends MY_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->model(['user_model', 'pick_model', 'artwork_model', 'place_model']);
	}

	public function detail($user_id) {
		echo $user_id . ' mypage';
	}

	public function me() {
		// TODO: 세션에서 유저 아이디 / 타입 가져오기
		$user_id = 1;
		$user_type = 1;

		$type = $this->input->get('type') ?: 'artworks';
		$user = $this->user_model->get_by_id($user_id);

		if ($type === 'artworks') {
			$my_picks = $this->pick_model->get_artwork_picks_by_user_id($user_id);
		} elseif ($type === 'places') {
			$my_picks = $this->pick_model->get_place_picks_by_user_id($user_id);
		}

		if ($user_type === 1) {
			$given_pick_count = $this->pick_model->get_given_artwork_pick_by_user_id($user_id);
		} else if ($user_type === 2) {
			$given_pick_count = $this->pick_model->get_given_place_pick_by_user_id($user_id);
		}

		$data = [
			'user' => $user,
			'given_pick_count' => $given_pick_count,
			'my_picks' => $my_picks,
			'type' => $type,
		];
		$this->twig->display('users/mypage', $data);
	}
}
