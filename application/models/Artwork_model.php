<?php

class Artwork_model extends CI_Model {
    /**
     * table_name: artworks
     */
    const TABLE_NAME = 'artworks';
    const TABLE_NAME_IMAGES = 'artwork_images';

    //public $id; -- Ignore PK
    public $user_id;
    public $status;
    public $title;
    public $description;
    public $image;
    public $for_sale;
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
            ->from(self::TABLE_NAME)
            ->order_by('id', 'DESC');

        if ($limit !== null) {
            $query = $query->limit($limit, $offset);
        }

        if ($search !== null && !empty($search)) {
            if (is_numeric($search)) {
                $query = $query->where('id', $search);
            }
            // 제목, tags 매치
            $query = $query->or_like('title', $search)
                ->or_like('tags', $search);
        }

        return $query->get()->result();
    }

    public function get_by_id($artwork_id) {
        $artwork = $this->db
            ->select('artworks.*, count(user_artwork_picks.id) as pick_count')
            ->from(self::TABLE_NAME)
            ->join('user_artwork_picks', 'user_artwork_picks.artwork_id = artworks.id', 'left')
            ->where('artworks.id', $artwork_id)
            ->get()->row();

        if ($artwork) {
            $artwork->user = $this->db
                ->from('users')
                ->where('id', $artwork->user_id)
                ->get()->row();
            $artwork->extra_images = $this->db
                ->from(self::TABLE_NAME_IMAGES)
                ->where('artwork_id', $artwork_id)
                ->get()->result();
        }

        return $artwork;
    }

    public function get_bare_by_id($artwork_id) {
        return $this->db
            ->from(self::TABLE_NAME)
            ->where('id', $artwork_id)
            ->get()->row();
    }

    public function get_bare_by_ids(array $artwork_ids) {
        return $this->db
            ->from(self::TABLE_NAME)
            ->where_in('id', $artwork_ids)
            ->get()->result();
    }

    public function get_by_user_id($user_id) {
        return $this->db
            ->from(self::TABLE_NAME)
            ->where('user_id', $user_id)
            ->get()->result();
    }

    public function insert($user_id, $status, $title, $description, $image, $for_sale, $use_comment, $tags) {
        $this->_fill_class_variable_with_params($user_id, $status, $title, $description, $image, $for_sale, $use_comment, $tags);
        if ($this->db->insert(self::TABLE_NAME, $this)) {
            return $this->db->insert_id();
        } else {
            return NULL;
        }
    }

    public function insert_images($artwork_id, array $images) {
        foreach ($images as $image) {
            $this->db->insert(self::TABLE_NAME_IMAGES, ['artwork_id' => $artwork_id, 'image' => $image]);
        }

        return true;
    }

    public function update($id, $user_id, $status, $title, $description, $image, $for_sale, $use_comment, $tags) {
        $this->_fill_class_variable_with_params($user_id, $status, $title, $description, $image, $for_sale, $use_comment, $tags);
        if ($this->db->update(self::TABLE_NAME, $this, ['id' => $id])) {
            return $id;
        } else {
            return NULL;
        }
    }

    public function delete_image($artwork_id, $image) {
        return $this->db->delete(self::TABLE_NAME_IMAGES, ['artwork_id' => $artwork_id, 'image' => $image]);
    }

    private function _fill_class_variable_with_params($user_id, $status, $title, $description, $image, $for_sale, $use_comment, $tags) {
        $this->user_id = $user_id;
        $this->status = $status;
        $this->title = $title;
        $this->description = $description;
        $this->image = $image;
        $this->for_sale = $for_sale;
        $this->use_comment = $use_comment;
        $this->tags = $tags;
    }
}
