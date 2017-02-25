<?php

class Place_pick_model extends CI_Model {
    /**
     * table_name: user_place_picks
     */
    const TABLE_NAME = 'user_place_picks';

    //public $id; -- Ignore PK
    public $user_id;
    public $place_id;

    public function get_count_by_place_id($place_id) {
        return $this->db
            ->from(self::TABLE_NAME)
            ->where('place_id', $place_id)
            ->count_all_results();
    }

    public function get_given_place_pick_by_user_id($user_id) {
        return $this->db
            ->from(self::TABLE_NAME)
            ->join('places', 'places.id = user_place_picks.place_id')
            ->where('places.user_id', $user_id)
            ->count_all_results();
    }

    public function check_pick($user_id, $place_id) {
        $data = [
            'user_id' => $user_id,
            'place_id' => $place_id
        ];

        $result = $this->db
            ->from(self::TABLE_NAME)
            ->where($data)
            ->limit(1)
            ->get();

        if ($result->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function insert_pick($user_id, $place_id) {
        $data = [
            'user_id' => $user_id,
            'place_id' => $place_id
        ];

        if ($this->db->insert(self::TABLE_NAME, $data)) {
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

        $this->db->delete(self::TABLE_NAME, $data);

        return $this->db->affected_rows() === 1;
    }

    public function delete_all_picks_by_place_id($place_id) {
        return $this->db->delete(self::PLACE_PICKS_TABLE_NAME, ['place_id' => $place_id]);
    }
}
