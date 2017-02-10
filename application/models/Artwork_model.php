<?php

class Artwork_model extends CI_Model
{
	/**
	 * table_name: artworks
	 */
	const TABLE_NAME = 'artworks';

	function __construct() {
			parent::__construct();
	}

	function gets() {
			return $this->db->get('artworks')->result();
	}

	//public $id; -- Ignore PK
	public $user_id;
	public $status;
	public $title;
	public $description;
	public $image;
	public $for_sale;
	public $use_comment;
	public $tags;

	public function insert($user_id, $status, $title, $description, $image, $for_sale, $use_comment, $tags)
	{
		$this->_fill_class_variable_with_params($user_id, $status, $title, $description, $image, $for_sale, $use_comment, $tags);
		if ($this->db->insert(self::TABLE_NAME, $this)) {
			return $this->db->insert_id();
		} else {
			return NULL;
		}
	}

	public function insert_images($artwork_id, array $images)
	{
		foreach ($images as $image) {
			$this->db->insert('artwork_images', ['artwork_id' => $artwork_id, 'image' => $image]);
		}

		return true;
	}

	/**
	public function update($id, $user_id, $status, $title, $description, $image, $for_sale, $use_comment, $tags)
	{
		$this->_fillClassVariableWithParams($user_id, $status, $title, $description, $image, $for_sale, $use_comment, $tags);
		return $this->db->update(self::TABLE_NAME, $this, ['id' => $id]);
	}
	 **/

	private function _fill_class_variable_with_params($user_id, $status, $title, $description, $image, $for_sale, $use_comment, $tags)
	{
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
