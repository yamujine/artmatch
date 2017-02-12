<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {
    /**
     * table_name: users
     */
    const TABLE_NAME = 'users';

    //public $id; -- Ignore PK
    public $email;
    public $password;
    public $type;
    public $user_id;
    public $profile_image;
    public $registered_at;

    public function add($email, $password, $user_id, $profile_image, $type) {

        $user = array(
            'email' => $email,
            'password' => $password,
            'user_id' => $user_id,
            'profile_image' => $profile_image,
            'type' => $type);

        if ($this->check_email($user['email'])) {
            return FALSE;
        }

        $this->db->insert(self::TABLE_NAME, $user);
        $id = $this->db->insert_id();
        return $id;
    }

    public function get_by_email($email) {

        $this->db->select('id, type, email, user_id, is_auth, is_admin');
        $this->db->where('email', $email);
        $query = $this->db->get(self::TABLE_NAME);
        if ($query->num_rows() == 0) {
            return FALSE;
        }
        return $query->row_array();
    }

    public function get_password($email) {

        $this->db->select('password');
        $this->db->where('email', $email);
        $query = $this->db->get(self::TABLE_NAME);
        return $query->row()->password;
    }

    public function authorize($id) {
        return $this->db->update(self::TABLE_NAME, array('is_auth' => 1), array('id' => $id));
    }

    public function check_email($email = '') {

        if (empty($email)) {
            return FALSE;
        }
        return $this->db->where('email', $email)
                ->count_all_results(self::TABLE_NAME) > 0;
    }
    public function get_by_id($id) {
        return $this->db
            ->from(self::TABLE_NAME)
            ->where('id', $id)
            ->get()->row();
    }
}
