<?php

class Pick_model extends CI_Model {
    const ARTWORK_PICKS_TABLE_NAME = 'user_artwork_picks';
    const PLACE_PICKS_TABLE_NAME = 'user_place_picks';

    //public $id; -- Ignore PK
    public $user_id;
    public $artwork_id;
    public $place_id;

    public function get_count_by_place_id($place_id) {
        return $this->db
            ->from(self::PLACE_PICKS_TABLE_NAME)
            ->where('place_id', $place_id)
            ->count_all_results();
    }

    public function get_count_by_artwork_id($artwork_id) {
        return $this->db
            ->from(self::ARTWORK_PICKS_TABLE_NAME)
            ->where('artwork_id', $artwork_id)
            ->count_all_results();
    }

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

    public function insert_pick($type, $user_id, $type_id) {
        if ($type === TYPE_ARTWORKS) {
            $this->load->model('artwork_model');

            if ($this->artwork_model->is_exists($type_id) === false) {
                throw new Exception("artwork is not exists");
            }

            return $this->insert_artwork_pick($user_id, $type_id);
        } else if ($type === TYPE_PLACES) {
            $this->load->model('place_model');

            if ($this->place_model->is_exists($type_id) === false) {
                throw new Exception("place is not exists");
            }

            return $this->insert_place_pick($user_id, $type_id);
        } else {
            throw new Exception("type error. type=" . $type);
        }
    }

    public function delete_pick($type, $user_id, $type_id) {
        if ($type === TYPE_ARTWORKS) {
            return $this->delete_artwork_pick($user_id, $type_id);
        } else if ($type === TYPE_PLACES) {
            return $this->delete_place_pick($user_id, $type_id);
        } else {
            throw new Exception("type error. type=" . $type);
        }
    }

    public function check_pick($type, $user_id, $type_id) {
        if ($type === TYPE_ARTWORKS) {
            return $this->check_artwork_pick($user_id, $type_id);
        } else if ($type === TYPE_PLACES) {
            return $this->check_place_pick($user_id, $type_id);
        } else {
            throw new Exception("type error. type=" . $type);
        }
    }


    public function insert_place_pick($user_id, $place_id) {
        $data = [
            'user_id' => $user_id,
            'place_id' => $place_id
        ];

        if ($this->db->insert(self::PLACE_PICKS_TABLE_NAME, $data)) {
            return $this->db->insert_id();
        } else {
            return NULL;
        }
    }

    public function delete_place_pick($user_id, $place_id) {
        $data = [
            'user_id' => $user_id,
            'place_id' => $place_id
        ];

        return $this->db->delete(self::PLACE_PICKS_TABLE_NAME, $data);
    }

    public function check_place_pick($user_id, $place_id) {
        $data = [
            'user_id' => $user_id,
            'place_id' => $place_id
        ];

        $result = $this->db
            ->from(self::PLACE_PICKS_TABLE_NAME)
            ->where($data)
            ->limit(1)
            ->get();

        if ($result->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function insert_artwork_pick($user_id, $artwork_id) {
        $data = [
            'user_id' => $user_id,
            'artwork_id' => $artwork_id
        ];

        if ($this->db->insert(self::ARTWORK_PICKS_TABLE_NAME, $data)) {
            return $this->db->insert_id();
        } else {
            return NULL;
        }
    }

    public function delete_artwork_pick($user_id, $artwork_id) {
        $data = [
            'user_id' => $user_id,
            'artwork_id' => $artwork_id
        ];

        return $this->db->delete(self::ARTWORK_PICKS_TABLE_NAME, $data);
    }

    public function check_artwork_pick($user_id, $artwork_id) {
        $data = [
            'user_id' => $user_id,
            'artwork_id' => $artwork_id
        ];

        $result = $this->db
            ->from(self::ARTWORK_PICKS_TABLE_NAME)
            ->where($data)
            ->limit(1)
            ->get();

        if ($result->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function delete_all_picks_by_artwork_id($artwork_id) {
        return $this->db->delete(self::ARTWORK_PICKS_TABLE_NAME, ['artwork_id' => $artwork_id]);
    }

    public function delete_all_picks_by_place_id($place_id) {
        return $this->db->delete(self::PLACE_PICKS_TABLE_NAME, ['place_id' => $place_id]);
    }
}
