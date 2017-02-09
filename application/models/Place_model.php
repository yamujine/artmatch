<?php

class Place_model extends CI_Model {
	/**
	 * table_name: places
	 */
    function __construct() {
        parent::__construct();
    }

    function gets() {
        return $this->db->get('places')->result();
    }

}
