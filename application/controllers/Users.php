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
		// TODO: 세션에서 유저 아이디 가져오기
		$user_id = 1;

		$type = $this->input->get('type') ?: 'artworks';

		$user = $this->user_model->get_by_id($user_id);

		if ($type === 'artworks') {
			$my_picks = $this->pick_model->get_artwork_picks_by_user_id($user_id);
		} elseif ($type === 'places') {
			$my_picks = $this->pick_model->get_place_picks_by_user_id($user_id);
		}

		$data = [
			'user' => $user,
			'given_picks' => $this->_count_all_given_picks($user_id),
			'my_picks' => $my_picks,
			'type' => $type,
		];
		$this->twig->display('users/me', $data);
	}

	private function _count_all_given_picks($user_id) {
		$user_artworks = $this->artwork_model->get_by_user_id($user_id);
		$user_places = $this->place_model->get_by_user_id($user_id);

		$user_artwork_ids = array_map(function($value) {
			return $value->id;
		}, $user_artworks);
		$user_place_ids = array_map(function($value) {
			return $value->id;
		}, $user_places);

		$user_artwork_picks = $this->pick_model->get_artwork_pick_count_by_artwork_ids($user_artwork_ids);
		$user_place_picks = $this->pick_model->get_place_pick_count_by_place_ids($user_place_ids);

		return ($user_artwork_picks + $user_place_picks);
	}

}
