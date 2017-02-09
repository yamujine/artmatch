<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model
{
    private $message;

    public function __construct()
    {
        $this->load->database();
    }

    public function add($email, $password, $profile_image, $type)
    {
        $user = array(
            'email' => $email,
            'password' => $password,
            'profile_image' => $profile_image,
            'type' => $type);

        if ($this->check_email($user['email'])) {
            $this->set_message('duplicated email');
            return FALSE;
        }

        $this->db->insert('users', $user);
        $id = $this->db->insert_id();
        return $id;
    }

    public function get_by_email($email)
    {
        $this->db->select('id, type, email, is_auth, is_admin');
        $this->db->where('email', $email);
        $query = $this->db->get('users');
        if ($query->num_rows() == 0) {
            $this->set_message('email is not found');
            return FALSE;
        }
        return $query->row_array();
    }

    public function get_by_id($id)
    {
        $this->db->select('id, type, email, is_auth, is_admin');
        $this->db->where('id', $id);
        $query = $this->db->get('users');
        return $query->row_array();
    }

    public function get_password($email)
    {
        $this->db->select('password');
        $this->db->where('email', $email);
        $query = $this->db->get('users');
        return $query->row()->password;
    }

    public function authorize($id)
    {
        return $this->db->update('users', array('is_auth' => 1), array('id' => $id));
    }

    public function check_email($email = '')
    {
        if (empty($email)) {
            return FALSE;
        }
        return $this->db->where('email', $email)
                ->count_all_results('users') > 0;
    }

    public function clear_message()
    {
        $this->message = '';
        return TRUE;
    }

    public function set_message($str)
    {
        $this->message = $str;
        return TRUE;
    }

    public function get_message()
    {
        return $this->message;
    }
}