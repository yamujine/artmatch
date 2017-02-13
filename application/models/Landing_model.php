<?php

class Landing_model extends CI_Model {
    /**
     * table_name: landing
     */
    const TABLE_NAME = 'landing';

    //public $id; -- Ignore PK
    public $email;
    public $registered_at;

    public function get_by_email($email) {
        return $this->db
            ->from(self::TABLE_NAME)
            ->where('email', $email)
            ->get()->row();
    }

    public function insert($email) {
        $this->email = $email;
        $this->registered_at = date("Y-m-d H:i:s");

        return $this->db->insert(self::TABLE_NAME, $this);
    }
}
