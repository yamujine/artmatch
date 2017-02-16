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
    public $username;
    public $profile_image;
    public $registered_at;

    public function add($email, $password, $user_name, $profile_image, $type) {
        $user = [
            'email' => $email,
            'password' => $password,
            'user_name' => $user_name,
            'profile_image' => $profile_image,
            'type' => $type
        ];

        if ($this->check_email($user['email'])) {
            return FALSE;
        }

        $this->db->insert(self::TABLE_NAME, $user);
        $id = $this->db->insert_id();

        return $id;
    }

    public function get_by_email($email) {
        $this->db->where('email', $email);
        $query = $this->db->get(self::TABLE_NAME);
        if ($query->num_rows() == 0) {
            return FALSE;
        }

        return $query->row();
    }

    public function get_password($email) {
        $this->db->select('password');
        $this->db->where('email', $email);
        $query = $this->db->get(self::TABLE_NAME);

        return $query->row()->password;
    }

    public function authorize($id) {
        return $this->db->update(self::TABLE_NAME, ['is_auth' => 1], ['id' => $id]);
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

    public function get_by_user_name($user_name) {
        return $this->db
            ->from(self::TABLE_NAME)
            ->where('user_name', $user_name)
            ->get()->row();
    }

    public function update_password($id, $new_password) {
        if ($this->db->update(self::TABLE_NAME, ['password' => $new_password], ['id' => $id])) {
            return $id;
        } else {
            return NULL;
        }
    }
}
