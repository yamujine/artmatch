<?php

class User_model extends CI_Model {
	/**
	 * table_name: users
	 */
    const TABLE_NAME = 'users';

    //public $id; -- Ignore PK
    public $email;
    public $password;
    public $type;
    public $name;
    public $profile_image;
    public $registered_at;

	public function get_by_id($id) {
		return $this->db
			->from(self::TABLE_NAME)
			->where('id', $id)
			->get()->row();
	}
}
