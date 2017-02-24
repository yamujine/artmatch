<?php

class Apply_model extends CI_Model {
    /**
     * table_name: apply
     */
    const TABLE_NAME = 'apply';

    //public $id; -- Ignore PK
    public $place_id;
    public $artwork_id;
    public $status;
    public $registered_at;

    public function insert($place_id, $artwork_id, $status) {
        $data = [
            'place_id' => $place_id,
            'artwork_id' => $artwork_id,
            'status' => $status
        ];

        if (!$this->db->insert(self::TABLE_NAME, $data)) {
            return false;
        }

        return $this->db->insert_id();
    }

    public function get_by_place_id_and_artwork_id($place_id, $artwork_id) {
        return $this->db
            ->from(self::TABLE_NAME)
            ->where('place_id', $place_id)
            ->where('artwork_id', $artwork_id)
            ->get()->row();
    }
}
