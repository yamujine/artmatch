<?php

class Place_model extends CI_Model {
	/**
	 * table_name: places
	 */
    const TABLE_NAME = 'places';

    //public $id; -- Ignore PK
    public $user_id;
    public $status;
    public $name;
    public $address;
    public $description;
    public $image;
    public $use_comment;
    public $tags;

	public function gets() {
		$this->db->order_by('id', 'DESC');
		return $this->db->get(self::TABLE_NAME)->result();
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
            $this->db->insert('place_images', ['place_id' => $place_id, 'image' => $image]);
        }

        return true;
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
