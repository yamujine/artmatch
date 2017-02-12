<?php

class Pick_model extends CI_Model {
	const ARTWORK_PICKS_TABLE_NAME = 'user_artwork_picks';
	const PLACE_PICKS_TABLE_NAME = 'user_place_picks';

	//public $id; -- Ignore PK
	public $user_id;
	public $artwork_id;
	public $place_id;

	public function get_artwork_pick_count_by_artwork_ids(array $artwork_ids) {
		return $this->db
			->from(self::ARTWORK_PICKS_TABLE_NAME)
			->where_in('artwork_id', $artwork_ids)
			->count_all_results();
	}

	public function get_place_pick_count_by_place_ids(array $place_ids) {
		return $this->db
			->from(self::PLACE_PICKS_TABLE_NAME)
			->where_in('place_id', $place_ids)
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

    public function insert_pick($type, $user_id, $place_id) {
	    if ($type === "artwork") {
	        return $this->insert_artwork_pick_by_user_id_and_place_id($user_id, $place_id);
        } else if ($type === "place") {
            return $this->insert_place_pick_by_user_id_and_place_id($user_id, $place_id);
        } else {
            throw new Exception("type error. type=".$type);
        }
    }

    public function delete_pick($type, $user_id, $place_id) {
        if ($type === "artwork") {
            return $this->delete_artwork_pick_by_user_id_and_place_id($user_id, $place_id);
        } else if ($type === "place") {
            return $this->delete_place_pick_by_user_id_and_place_id($user_id, $place_id);
        } else {
            throw new Exception("type error. type=".$type);
        }
    }

    public function check_pick($type, $user_id, $place_id) {
        if ($type === "artwork") {
            return $this->check_artwork_pick_by_user_id_and_place_id($user_id, $place_id);
        } else if ($type === "place") {
            return $this->check_place_pick_by_user_id_and_place_id($user_id, $place_id);
        } else {
            throw new Exception("type error. type=".$type);
        }
    }


    public function insert_place_pick_by_user_id_and_place_id($user_id, $place_id) {
	    $data = array(
            'user_id' => $user_id,
            'place_id' => $place_id
        );

        if ($this->db->insert(self::PLACE_PICKS_TABLE_NAME, $data)) {
            return $this->db->insert_id();
        } else {
            return NULL;
        }
    }

    public function delete_place_pick_by_user_id_and_place_id($user_id, $place_id) {
        $data = array(
            'user_id' => $user_id,
            'place_id' => $place_id
        );

        return $this->db->delete(self::PLACE_PICKS_TABLE_NAME, $data);
    }

    public function check_place_pick_by_user_id_and_place_id($user_id, $place_id) {
        $data = array(
            'user_id' => $user_id,
            'place_id' => $place_id
        );

        if ($this->db
            ->from(self::PLACE_PICKS_TABLE_NAME)
            ->where($data)
            ->limit(1)
            ->get()->result()) {
            return true;
        } else {
            return false;
        }
    }

	public function insert_artwork_pick_by_user_id_and_place_id($user_id, $artwork_id) {
	    $data = array(
            'user_id' => $user_id,
            'artwork_id' => $artwork_id
        );

        if ($this->db->insert(self::ARTWORK_PICKS_TABLE_NAME, $data)) {
            return $this->db->insert_id();
        } else {
            return NULL;
        }
    }

    public function delete_artwork_pick_by_user_id_and_place_id($user_id, $artwork_id) {
        $data = array(
            'user_id' => $user_id,
            'artwork_id' => $artwork_id
        );

        return $this->db->delete(self::ARTWORK_PICKS_TABLE_NAME, $data);
    }

    public function check_artwork_pick_by_user_id_and_place_id($user_id, $artwork_id) {
        $data = array(
            'user_id' => $user_id,
            'artwork_id' => $artwork_id
        );

        if ($this->db
            ->from(self::ARTWORK_PICKS_TABLE_NAME)
            ->where($data)
            ->limit(1)
            ->get()->result()) {
            return true;
        } else {
            return false;
        }
    }

}
