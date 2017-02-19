<?php

class Exhibition_model extends CI_Model {
    /**
     * table_name: exhibitions
     */
    const TABLE_NAME = 'exhibitions';

    //public $id; -- Ignore PK
    public $place_id;
    public $start_date;
    public $end_date;
    public $artwork_count;
    public $is_free;

    /**
     * table_name: exhibitions
     */
    const ARTWORK_TABLE_NAME = 'exhibition_artworks';

    //public $id; -- Ignore PK
    public $exhibition_id;
    public $artwork_id;

    public function insert($place_id, $start_date, $end_date, $artwork_count, $is_free) {
        $data = [
            'place_id' => $place_id,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'artwork_count' => $artwork_count,
            'is_free' => $is_free
        ];

        if ($this->db->insert(self::TABLE_NAME, $data)) {
            return $this->db->insert_id();
        } else {
            return NULL;
        }
    }

    public function update_by_place_id($place_id, $start_date, $end_date, $artwork_count, $is_free) {
        $data = [
            'start_date' => $start_date,
            'end_date' => $end_date,
            'artwork_count' => $artwork_count,
            'is_free' => $is_free
        ];
        if ($this->db->update(self::TABLE_NAME, $data, ['place_id' => $place_id])) {
            return $place_id;
        } else {
            return NULL;
        }
    }

    public function get_by_place_id($place_id) {
        return $this->db
            ->from(self::TABLE_NAME)
            ->where('place_id', $place_id)
            ->order_by('id', 'DESC')
            ->get()->row();
    }

    public function get_artwork_ids_by_exhibition_id($exhibition_id) {
        return $this->db
            ->from(self::ARTWORK_TABLE_NAME)
            ->where('exhibition_id', $exhibition_id)
            ->get()->result();
    }

    public function get_exhibitions_by_artwork_id($artwork_id) {
        return $this->db
            ->from(self::TABLE_NAME)
            ->join(self::ARTWORK_TABLE_NAME, 'exhibition_artworks.exhibition_id = exhibitions.id')
            ->where('exhibition_artworks.artwork_id', $artwork_id)
            ->get()->result();
    }

    public function get_exhibitions_by_place_id($place_id) {
        return $this->db
            ->from(self::TABLE_NAME)
            ->join(self::ARTWORK_TABLE_NAME, 'exhibition_artworks.exhibition_id = exhibitions.id', 'LEFT')
            ->where('exhibitions.place_id', $place_id)
            ->get()->result();
    }
}
