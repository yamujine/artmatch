<?php

class Pick_model extends CI_Model {
	const ARTWORK_PICKS_TABLE_NAME = 'user_artwork_picks';
	const PLACE_PICKS_TABLE_NAME = 'user_place_picks';

	//public $id; -- Ignore PK
	public $user_id;
	public $artwork_id;
	public $place_id;

	public function get_given_artwork_pick_by_user_id($user_id) {
		return $this->db
			->from(self::ARTWORK_PICKS_TABLE_NAME)
			->join('artworks', 'artworks.id = user_artwork_picks.artwork_id')
			->where('artworks.user_id', $user_id)
			->count_all_results();
	}

	public function get_given_place_pick_by_user_id($user_id) {
		return $this->db
			->from(self::PLACE_PICKS_TABLE_NAME)
			->join('places', 'places.id = user_place_picks.place_id')
			->where('places.user_id', $user_id)
			->count_all_results();
	}

	public function get_artwork_picks_by_user_id($user_id) {
		$this->load->model('artwork_model');
		$picks = $this->db
			->from(self::ARTWORK_PICKS_TABLE_NAME)
			->where('user_id', $user_id)
			->get()->result();

		foreach ($picks as $pick) {
			$pick->artwork = $this->artwork_model->get_bare_by_id($pick->artwork_id);
		}

		return $picks;
	}

	public function get_place_picks_by_user_id($user_id) {
		$this->load->model('place_model');
		$picks = $this->db
			->from(self::PLACE_PICKS_TABLE_NAME)
			->where('user_id', $user_id)
			->get()->result();

		foreach ($picks as $pick) {
			$pick->place = $this->place_model->get_bare_by_id($pick->place_id);
		}

		return $picks;
	}
}
