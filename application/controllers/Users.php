<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends MY_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->model(['user_model', 'pick_model', 'artwork_model', 'place_model']);
	}

	public function detail($user_name) {
		$data = $this->_get_user_details($user_name);

		$this->twig->display('users/mypage', $data);
	}

	public function me() {
		$user_name = $this->accountlib->get_user_name();
		$pick_type = $this->input->get('type');

		$data = $this->_get_user_details($user_name, true, $pick_type);

		$this->twig->display('users/mypage', $data);
	}

	private function _get_user_details($user_name, $is_my_page = false, $pick_type = '') {
		$user = $this->user_model->get_by_user_name($user_name);

		// 내 작품, 장소 리스트
		if ((int) $user->type === 0) {
			$mine = $this->artwork_model->get_by_user_id($user->id);
		} else if ((int) $user->type === 1) {
			$mine = $this->place_model->get_by_user_id($user->id);
		}
		foreach ($mine as $something) {
			$type = ((int) $user->type === 0) ? 'artworks' : 'places';
			$something->url = $type . $something->id;
			$something->subject = ($type === 'artworks') ? $something->title : $something->name;
		}

		// 받은 pick 카운트
		if ((int) $user->type === 0) {
			$given_pick_count = $this->pick_model->get_given_artwork_pick_by_user_id($user->id);
		} else if ((int) $user->type === 1) {
			$given_pick_count = $this->pick_model->get_given_place_pick_by_user_id($user->id);
		}

		// 내가 pick한 작품 장소 리스트
		$my_picks = [];
		if ($is_my_page && !empty($pick_type)) {
			if ($pick_type === 'artworks') {
				$my_picks = $this->pick_model->get_artwork_picks_by_user_id($user->id);
			} elseif ($pick_type === 'places') {
				$my_picks = $this->pick_model->get_place_picks_by_user_id($user->id);
			}
		}
		foreach ($my_picks as $my_pick) {
			if ($pick_type === 'artworks') {
				$my_pick->url = '/' . $pick_type . '/' . $my_pick->artwork->id;
				$my_pick->subject = $my_pick->artwork->title;
			} elseif ($pick_type === 'places') {
				$my_pick->url = '/' . $pick_type . '/' . $my_pick->place->id;
				$my_pick->subject = $my_pick->place->name;
			}
		}

		return [
			'user' => $user,
			'is_my_page' => $is_my_page,
			'pick_type' => $pick_type,
			'mine' => $mine,
			'given_pick_count' => $given_pick_count,
			'my_picks' => $my_picks
		];
	}
}
