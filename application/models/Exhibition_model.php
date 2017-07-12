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
    public $title;
    public $artwork_count;
    public $is_free;
    public $is_applicable;

    /**
     * table_name: exhibitions
     */
    const ARTWORK_TABLE_NAME = 'exhibition_artworks';

    //public $id; -- Ignore PK
    public $exhibition_id;
    public $artwork_id;

    public function insert($place_id, $start_date, $end_date, $title, $artwork_count, $is_free, $is_applicable) {
        $data = [
            'place_id' => $place_id,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'title' => $title,
            'artwork_count' => $artwork_count,
            'is_free' => $is_free,
            'is_applicable' => $is_applicable
        ];

        if ($this->db->insert(self::TABLE_NAME, $data)) {
            return $this->db->insert_id();
        }

        return NULL;
    }

    public function insert_exhibition_artworks($exhibition_id, $artwork_id) {
        $data = [
            'exhibition_id' => $exhibition_id,
            'artwork_id' => $artwork_id
        ];

        if (!$this->db->insert(self::ARTWORK_TABLE_NAME, $data)) {
            return false;
        }

        return $this->db->insert_id();
    }

    public function update($exhibition_id, $place_id, $start_date, $end_date, $title, $artwork_count, $is_free, $is_applicable) {
        $data = [
            'place_id' => $place_id,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'title' => $title,
            'artwork_count' => $artwork_count,
            'is_free' => $is_free,
            'is_applicable' => $is_applicable
        ];

        if ($this->db->update(self::TABLE_NAME, $data, ['id' => $exhibition_id])) {
            return $place_id;
        }

        return $this->db->affected_rows() === 1;
    }

    /**
     * @deprecated 장소별 전시는 여러개 존재하므로 이런 함수는 사용을 하면 안됨
     * @param $place_id
     * @return mixed
     */
    public function get_one_by_place_id($place_id) {
        return $this->db
            ->select('exhibitions.*, COUNT(exhibition_artworks.id) AS real_artwork_count')
            ->from(self::TABLE_NAME)
            ->join(self::ARTWORK_TABLE_NAME, 'exhibition_artworks.exhibition_id = exhibitions.id', 'LEFT')
            ->where('place_id', $place_id)
            ->group_by('exhibition_artworks.exhibition_id')
            ->order_by('id', 'ASC')
            ->get()->row();
    }

    public function get_by_id($exhibition_id) {
        return $this->db
            ->from(self::TABLE_NAME)
            ->where('id', $exhibition_id)
            ->get()->row();
    }

    public function get_by_ids(array $exhibition_ids) {
        if (empty($exhibition_ids)) {
            return NULL;
        }

        return $this->db
            ->from(self::TABLE_NAME)
            ->where_in('id', $exhibition_ids)
            ->get()->result();
    }

    public function get_by_artwork_id($artwork_id) {
        return $this->db
            ->from(self::TABLE_NAME)
            ->join(self::ARTWORK_TABLE_NAME, 'exhibition_artworks.exhibition_id = exhibitions.id')
            ->where('exhibition_artworks.artwork_id', $artwork_id)
            ->order_by('exhibitions.end_date', 'DESC')
            ->get()->result();
    }

    public function get_by_place_id($place_id) {
        return $this->db
            ->from(self::TABLE_NAME)
            ->where('place_id', $place_id)
            ->order_by('start_date', 'ASC')
            ->get()->result();
    }

    public function get_with_artwork_count_by_place_id($place_id) {
        return $this->db
            ->select('exhibitions.*, COUNT(exhibition_artworks.id) AS real_artwork_count')
            ->from(self::TABLE_NAME)
            ->join(self::ARTWORK_TABLE_NAME, 'exhibition_artworks.exhibition_id = exhibitions.id', 'LEFT')
            ->where('exhibitions.place_id', $place_id)
            ->group_by('exhibitions.id')
            ->order_by('exhibitions.id', 'DESC')
            ->get()->result();
    }

    public function get_artwork_ids_by_exhibition_id($exhibition_id) {
        return $this->db
            ->from(self::ARTWORK_TABLE_NAME)
            ->where('exhibition_id', $exhibition_id)
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

    /**
     * 진행 중인 전시 관련
     */

    /**
     * 현재 진행중인 전시를 리턴하는 함수
     * @param $place_id
     */
    public function get_now_exhibiting_by_place_id($place_id) {
        $today = date('Y-m-d');
        return $this->db
            ->from(self::TABLE_NAME)
            ->where('exhibitions.place_id', $place_id)
            ->where('exhibitions.start_date <=', $today)
            ->where('exhibitions.end_date >=', $today)
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

    /**
     * 삭제 관련 함수들
     */

    public function delete_all_artworks_by_artwork_id($artwork_id) {
        return $this->db->delete(self::ARTWORK_TABLE_NAME, ['artwork_id' => $artwork_id]);
    }

    public function delete_all_by_place_id($place_id) {
        $exhibitions = $this->get_by_place_id($place_id);
        $exhibition_ids = array_map(function ($value) {
            return $value->id;
        }, $exhibitions);

        if (!empty($exhibition_ids)) {
            return $this->db->where_in('id', $exhibition_ids)->delete(self::TABLE_NAME);
        }

        return true;
    }

    public function delete_all_artworks_by_place_id($place_id) {
        $exhibitions = $this->get_by_place_id($place_id);
        $exhibition_ids = array_map(function ($value) {
            return $value->id;
        }, $exhibitions);

        if (!empty($exhibition_ids)) {
            return $this->db->where_in('exhibition_id', $exhibition_ids)->delete(self::ARTWORK_TABLE_NAME);
        }

        return true;
    }

    public function delete($exhibition_id): bool {
        $this->db->delete(self::TABLE_NAME, ['id' => $exhibition_id]);

        return ($this->db->affected_rows() === 1);
    }

    public function delete_artworks($exhibition_id): bool {
        return $this->db->delete(self::ARTWORK_TABLE_NAME, ['exhibition_id' => $exhibition_id]);
    }
}
