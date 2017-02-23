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
            ->select('exhibitions.*, COUNT(exhibition_artworks.id) AS real_artwork_count')
            ->from(self::TABLE_NAME)
            ->join(self::ARTWORK_TABLE_NAME, 'exhibition_artworks.exhibition_id = exhibitions.id', 'LEFT')
            ->where('place_id', $place_id)
            ->group_by('exhibition_artworks.exhibition_id')
            ->order_by('id', 'DESC')
            ->get()->row();
    }

    public function get_artwork_ids_by_exhibition_id($exhibition_id) {
        return $this->db
            ->from(self::ARTWORK_TABLE_NAME)
            ->where('exhibition_id', $exhibition_id)
            ->get()->result();
    }

    /**
     * 해당 작품이 전시중인지 리턴하는 함수
     * @param $artwork_id
     * @return bool
     */
    public function is_now_exhibiting_artwork_by_artwork_id($artwork_id) {
        $today = date('Y-m-d');
        $query = $this->db
            ->from(self::TABLE_NAME)
            ->join(self::ARTWORK_TABLE_NAME, 'exhibition_artworks.exhibition_id = exhibitions.id')
            ->where('exhibition_artworks.artwork_id', $artwork_id)
            ->where('exhibitions.start_date <=', $today)
            ->where('exhibitions.end_date >=', $today)
            ->get();

        return $query->num_rows() > 0;
    }

    /**
     * 해당 장소에 전시중인 전시가 있는지 리턴하는 함수
     * @param $place_id
     * @return bool
     */
    public function is_now_exhibiting_by_place_id($place_id) {
        $today = date('Y-m-d');
        $query = $this->db
            ->from(self::TABLE_NAME)
            ->where('place_id', $place_id)
            ->where('start_date <=', $today)
            ->where('end_date >=', $today)
            ->get();

        return $query->num_rows() > 0;
    }

    public function get_exhibitions_by_artwork_id($artwork_id) {
        return $this->db
            ->from(self::TABLE_NAME)
            ->join(self::ARTWORK_TABLE_NAME, 'exhibition_artworks.exhibition_id = exhibitions.id')
            ->where('exhibition_artworks.artwork_id', $artwork_id)
            ->order_by('exhibitions.end_date', 'DESC')
            ->get()->result();
    }

    public function get_exhibitions_by_place_id($place_id) {
        return $this->db
            ->from(self::TABLE_NAME)
            ->where('place_id', $place_id)
            ->order_by('start_date', 'ASC')
            ->get()->result();
    }

    /**
     * 해당 장소에 전시된 모든 작품 갯수
     */
    public function get_exhibit_artwork_count_by_place_id($place_id) {
        return $this->db
            ->from(self::TABLE_NAME)
            ->join(self::ARTWORK_TABLE_NAME, 'exhibition_artworks.exhibition_id = exhibitions.id')
            ->where('exhibitions.place_id', $place_id)
            ->get()->num_rows();
    }

    public function delete_all_artworks_by_artwork_id($artwork_id) {
        return $this->db->delete(self::ARTWORK_TABLE_NAME, ['artwork_id' => $artwork_id]);
    }

    public function delete_all_by_place_id($place_id) {
        $exhibitions = $this->get_exhibitions_by_place_id($place_id);
        $exhibition_ids = array_map(function ($value) {
            return $value->id;
        }, $exhibitions);

        if (!empty($exhibition_ids)) {
            return $this->db->where_in('id', $exhibition_ids)->delete(self::TABLE_NAME);
        }

        return true;
    }

    public function delete_all_artworks_by_place_id($place_id) {
        $exhibitions = $this->get_exhibitions_by_place_id($place_id);
        $exhibition_ids = array_map(function ($value) {
            return $value->id;
        }, $exhibitions);

        if (!empty($exhibition_ids)) {
            return $this->db->where_in('exhibition_id', $exhibition_ids)->delete(self::ARTWORK_TABLE_NAME);
        }

        return true;
    }
}
