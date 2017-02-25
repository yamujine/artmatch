<?php

class Artwork_pick_model extends CI_Model {
    /**
     * table_name: user_artwork_picks
     */
    const TABLE_NAME = 'user_artwork_picks';

    //public $id; -- Ignore PK
    public $user_id;
    public $artwork_id;

    public function get_count_by_artwork_id($artwork_id) {
        return $this->db
            ->from(self::TABLE_NAME)
            ->where('artwork_id', $artwork_id)
            ->count_all_results();
    }

    public function get_given_artwork_pick_by_user_id($user_id) {
        return $this->db
            ->from(self::TABLE_NAME)
            ->join('artworks', 'artworks.id = user_artwork_picks.artwork_id')
            ->where('artworks.user_id', $user_id)
            ->count_all_results();
    }

    public function check_pick($user_id, $artwork_id) {
        $data = [
            'user_id' => $user_id,
            'artwork_id' => $artwork_id
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

    public function insert_pick($user_id, $artwork_id) {
        $data = [
            'user_id' => $user_id,
            'artwork_id' => $artwork_id
        ];

        if ($this->db->insert(self::TABLE_NAME, $data)) {
            return $this->db->insert_id();
        } else {
            return NULL;
        }
    }

    public function delete_pick($user_id, $artwork_id) {
        $data = [
            'user_id' => $user_id,
            'artwork_id' => $artwork_id
        ];

        $this->db->delete(self::TABLE_NAME, $data);

        return $this->db->affected_rows() === 1;
    }

    public function delete_all_picks_by_artwork_id($artwork_id) {
        return $this->db->delete(self::TABLE_NAME, ['artwork_id' => $artwork_id]);
    }
}
