<?php

class Place_model extends CI_Model {
    /**
     * table_name: places
     */
    const TABLE_NAME = 'places';
    const TABLE_NAME_IMAGES = 'places_images';

    //public $id; -- Ignore PK
    public $user_id;
    public $status;
    public $name;
    public $address;
    public $description;
    public $image;
    public $use_comment;
    public $tags;

    public function is_exists($id) {
        $result = $this->db
            ->from(self::TABLE_NAME)
            ->where('id', $id)
            ->limit(1)
            ->get();

        if ($result->num_rows() > 0) {
            return true;
        }
        return false;
    }

    public function gets($limit = null, $offset = null, $search = null) {
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

    public function get_pick_count_by_id($place_id) {
        return $this->db
            ->select('count(user_place_picks.id) as pick_count')
            ->from(self::TABLE_NAME)
            ->join('user_place_picks', 'user_place_picks.place_id = places.id', 'left')
            ->where('places.id', $place_id)
            ->get()->row();
    }

    public function get_by_id($place_id) {
        $place = $this->db
            ->select('places.*, count(user_place_picks.id) as pick_count')
            ->from(self::TABLE_NAME)
            ->join('user_place_picks', 'user_place_picks.place_id = places.id', 'left')
            ->where('places.id', $place_id)
            ->get()->row();

        if ($place) {
            $place->user = $this->db
                ->from('users')
                ->where('id', $place->user_id)
                ->get()->row();
            $place->extra_images = $this->db
                ->from('place_images')
                ->where('place_id', $place_id)
                ->get()->result();
        }

        return $place;
    }

    public function get_bare_by_id($place_id) {
        return $this->db
            ->from(self::TABLE_NAME)
            ->where('id', $place_id)
            ->get()->row();
    }

    public function get_by_user_id($user_id) {
        return $this->db
            ->from(self::TABLE_NAME)
            ->where('user_id', $user_id)
            ->get()->result();
    }

    public function insert($user_id, $status, $name, $address, $description, $image, $use_comment, $tags) {
        $this->_fill_class_variable_with_params($user_id, $status, $name, $address, $description, $image, $use_comment, $tags);
        if ($this->db->insert(self::TABLE_NAME, $this)) {
            return $this->db->insert_id();
        } else {
            return NULL;
        }
    }

    public function insert_images($place_id, array $images) {
        foreach ($images as $image) {
            $this->db->insert(self::TABLE_NAME_IMAGES, ['place_id' => $place_id, 'image' => $image]);
        }

        return true;
    }

    public function update($id, $user_id, $status, $name, $address, $description, $image, $use_comment, $tags) {
        $this->_fill_class_variable_with_params($user_id, $status, $name, $address, $description, $image, $use_comment, $tags);
        if ($this->db->update(self::TABLE_NAME, $this, ['id' => $id])) {
            return $id;
        } else {
            return NULL;
        }
    }

    public function update_view_count_by_id($id) {
        return $this->db
            ->set('views', 'views+1', FALSE)
            ->where('id', $id)
            ->update(self::TABLE_NAME);
    }

    public function delete_image($place_id, $image) {
        return $this->db->delete(self::TABLE_NAME_IMAGES, ['place_id' => $place_id, 'image' => $image]);
    }

    private function _fill_class_variable_with_params($user_id, $status, $name, $address, $description, $image, $use_comment, $tags) {
        $this->user_id = $user_id;
        $this->status = $status;
        $this->name = $name;
        $this->address = $address;
        $this->description = $description;
        $this->image = $image;
        $this->use_comment = $use_comment;
        $this->tags = $tags;
    }
}
