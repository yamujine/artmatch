<?php

class Exhibition_model extends CI_Model
{
	/**
	 * table_name: exhibitions
	 */
	const TABLE_NAME = 'exhibitions';

	//public $id; -- Ignore PK
	public $place_id;
	public $start_date;
	public $end_date;
	public $artwork_count;
	public $is_free;

	/**
	 * table_name: exhibitions
	 */
	const ARTWORK_TABLE_NAME = 'exhibition_artworks';

	//public $id; -- Ignore PK
	public $exhibition_id;
	public $artwork_id;

	public function get_exhibitions_by_artwork_id($artwork_id) {
		return $this->db
			->from(self::TABLE_NAME)
			->join(self::ARTWORK_TABLE_NAME, 'exhibition_artworks.exhibition_id = exhibitions.id')
			->where('exhibition_artworks.artwork_id', $artwork_id)
			->get()->result();
	}

	public function get_exhibitions_by_place_id($place_id) {
		return $this->db
			->from(self::TABLE_NAME)
			->join(self::ARTWORK_TABLE_NAME, 'exhibition_artworks.exhibition_id = exhibitions.id')
			->where('exhibitions.place_id', $place_id)
			->get()->result();
	}
}
