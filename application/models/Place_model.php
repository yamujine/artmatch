<?php

class Place_model extends CI_Model {
    /**
     * table_name: places
     */
    const TABLE_NAME = 'places';
    const TABLE_NAME_IMAGES = 'place_images';

    //public $id; -- Ignore PK
    public $user_id;
    public $name;
    public $area;
    public $address;
    public $description;
    public $image;
    public $use_comment;
    public $tags;

    public function gets($limit = null, $offset = null, $search = null, $user_id = null) {
        $query = $this->db
            ->select('places.*, users.user_name, count(user_place_picks.id) as pick_count')
            ->from(self::TABLE_NAME)
            ->join('users', 'users.id = places.user_id')
            ->join('user_place_picks', 'user_place_picks.place_id = places.id', 'left')
            ->group_by('places.id')
            ->order_by('places.id', 'DESC');

        if ($limit !== null) {
            $query = $query->limit($limit, $offset);
        }

        if ($search !== null && !empty($search)) {
            if (is_numeric($search)) {
                $query = $query->where('places.id', $search);
            }
            // 이름, 주소, tags 매치
            $query = $query->or_like('places.name', $search)
                ->or_like('places.address', $search)
                ->or_like('places.tags', $search);
        }

        if ($user_id !== null) {
            $query = $query->select('IF(P2.id IS NULL, 0, 1) AS is_picked')
                ->join('user_place_picks AS P2', "P2.place_id = places.id AND P2.user_id = '{$user_id}'", 'left');
        }

        return $query->get()->result();
    }

    public function get_total_count($search = null) {
        $query = $this->db
            ->select('places.*, users.user_name, count(user_place_picks.id) as pick_count')
            ->from(self::TABLE_NAME)
            ->join('users', 'users.id = places.user_id')
            ->join('user_place_picks', 'user_place_picks.place_id = places.id', 'left')
            ->group_by('places.id');

        if ($search !== null && !empty($search)) {
            if (is_numeric($search)) {
                $query = $query->where('places.id', $search);
            }
            // 이름, 주소, tags 매치
            $query = $query->or_like('places.name', $search)
                ->or_like('places.address', $search)
                ->or_like('places.tags', $search);
        }

        return $query->get()->num_rows();
    }

    public function get_by_id($place_id, $user_id = null) {
        $query = $this->db
            ->select('places.*, count(user_place_picks.id) as pick_count')
            ->from(self::TABLE_NAME)
            ->join('user_place_picks', 'user_place_picks.place_id = places.id', 'left')
            ->where('places.id', $place_id);

        if ($user_id !== null) {
            $query = $query->select('IF(P2.id IS NULL, 0, 1) AS is_picked')
                ->join('user_place_picks AS P2', "P2.place_id = places.id AND P2.user_id = '{$user_id}'", 'left');
        }

        $place = $query->get()->row();
        // COUNT 함수가 추가되어 있어서, $place->id에 빈 값이 포함된 row가 리턴이 되므로 property를 직접 체크
        if (empty($place->id)) {
            return NULL;
        }

        $place->user = $this->db
            ->from('users')
            ->where('id', $place->user_id)
            ->get()->row();
        $place->extra_images = $this->db
            ->from(self::TABLE_NAME_IMAGES)
            ->where('place_id', $place_id)
            ->get()->result();

        return $place;
    }

    public function get_bare_by_id($place_id) {
        return $this->db
            ->from(self::TABLE_NAME)
            ->where('id', $place_id)
            ->get()->row();
    }

    public function get_count_by_user_id($user_id) {
        return $this->db
            ->from(self::TABLE_NAME)
            ->where('user_id', $user_id)
            ->count_all_results();
    }

    /**
     * Heavy한 함수이므로 가급적 사용 자제 (장소, pick 수, 추가 이미지 등 전부 불러옴)
     * @param $user_id
     * @return mixed
     */
    public function get_all_by_user_id($user_id) {
        $places = $this->db
            ->select('places.*, count(user_place_picks.id) as pick_count, IF(P2.id IS NULL, 0, 1) AS is_picked')
            ->from(self::TABLE_NAME)
            ->join('user_place_picks', 'user_place_picks.place_id = places.id', 'left')
            ->join('user_place_picks AS P2', "P2.place_id = places.id AND P2.user_id = '{$user_id}'", 'left')
            ->where('places.user_id', $user_id)
            ->group_by('places.id')
            ->order_by('places.id', 'DESC')
            ->get()->result();

        foreach ($places as $place) {
            $place->extra_images = $this->db
                ->from(self::TABLE_NAME_IMAGES)
                ->where('place_id', $place->id)
                ->get()->result();
        }

        return $places;
    }

    public function get_picked_by_user_id($user_id) {
        $picks = $this->db
            ->select('places.*, count(P2.id) as pick_count')
            ->from(self::TABLE_NAME)
            ->join('user_place_picks AS P2', 'P2.place_id = places.id', 'left')
            ->join('user_place_picks', 'user_place_picks.place_id = places.id')
            ->where('user_place_picks.user_id', $user_id)
            ->group_by('places.id')
            ->order_by('user_place_picks.id', 'DESC')
            ->get()->result();

        return $picks;
    }

    public function get_images_by_id($place_id) {
        return $this->db
            ->from(self::TABLE_NAME_IMAGES)
            ->where('place_id', $place_id)
            ->get()->result();
    }

    public function insert($user_id, $name, $area, $address, $description, $image, $use_comment, $tags) {
        $this->_fill_class_variable_with_params($user_id, $name, $area, $address, $description, $image, $use_comment, $tags);
        if (!$this->db->insert(self::TABLE_NAME, $this)) {
            return NULL;
        }

        return $this->db->insert_id();
    }

    public function insert_images($place_id, array $images) {
        foreach ($images as $image) {
            $this->db->insert(self::TABLE_NAME_IMAGES, ['place_id' => $place_id, 'image' => $image]);
        }

        return true;
    }

    public function update($id, $user_id, $name, $area, $address, $description, $image, $use_comment, $tags) {
        $this->_fill_class_variable_with_params($user_id, $name, $area, $address, $description, $image, $use_comment, $tags);
        if (!$this->db->update(self::TABLE_NAME, $this, ['id' => $id])) {
            return NULL;
        }

        return $id;
    }

    public function update_view_count_by_id($id) {
        return $this->db
            ->set('views', 'views+1', FALSE)
            ->where('id', $id)
            ->update(self::TABLE_NAME);
    }

    public function change_comment_option($place_id, $is_use_comment) {
        $this->db->update(self::TABLE_NAME, ['use_comment' => $is_use_comment], ['id' => $place_id]);

        return $this->db->affected_rows() === 1;
    }

    public function delete($place_id) {
        $this->db->delete(self::TABLE_NAME, ['id' => $place_id]);

        return $this->db->affected_rows() === 1;
    }

    public function delete_image($place_id, $image) {
        $this->db->delete(self::TABLE_NAME_IMAGES, ['place_id' => $place_id, 'image' => $image]);

        return $this->db->affected_rows() === 1;
    }

    private function _fill_class_variable_with_params($user_id, $name, $area, $address, $description, $image, $use_comment, $tags) {
        $this->user_id = $user_id;
        $this->name = $name;
        $this->area = $area;
        $this->address = $address;
        $this->description = $description;
        $this->image = $image;
        $this->use_comment = $use_comment;
        $this->tags = $tags;
    }
}
