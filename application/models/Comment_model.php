<?php

class Comment_model extends CI_Model {
	const ARTWORK_COMMENTS_TABLE_NAME = 'artwork_comments';
	const PLACE_COMMENTS_TABLE_NAME = 'place_comments';

	//public $id; -- Ignore PK
	public $user_id;
	public $artwork_id;
	public $place_id;
	public $comment;

	public function get_artwork_comments_by_artwork_id($artwork_id) {
		return $this->db
			->from(self::ARTWORK_COMMENTS_TABLE_NAME)
			->where('artwork_id', $artwork_id)
			->get()->result();
	}

    public function insert_comment($type, $user_id, $place_id) {
	    if ($type === "artwork") {
	        return $this->insert_artwork_comment($user_id, $place_id);
        } else if ($type === "place") {
            return $this->insert_place_comment($user_id, $place_id);
        } else {
            throw new Exception("type error. type=".$type);
        }
    }

    public function delete_comment($type, $type_comment_id) {
        if ($type === "artwork") {
            return $this->delete_artwork_comment($type_comment_id);
        } else if ($type === "place") {
            return $this->delete_place_comment($type_comment_id);
        } else {
            throw new Exception("type error. type=".$type);
        }
    }

    public function update_comment($type, $user_id, $place_id) {
        if ($type === "artwork") {
            return $this->update_artwork_comment($user_id, $place_id);
        } else if ($type === "place") {
            return $this->update_place_comment($user_id, $place_id);
        } else {
            throw new Exception("type error. type=".$type);
        }
    }

    public function insert_artwork_comment($user_id, $place_id, $comment) {
	    $data = array(
            'user_id' => $user_id,
            'place_id' => $place_id,
            'comment' => $comment
        );

        if ($this->db->insert(self::ARTWORK_COMMENTS_TABLE_NAME, $data)) {
            return $this->db->insert_id();
        } else {
            return NULL;
        }
    }

    public function delete_artwork_comment($id) {
        return $this->db->delete(self::ARTWORK_COMMENTS_TABLE_NAME, array('id' => $id));
    }

    public function update_artwork_comment($id, $comment) {
        return $this->db
            ->where('id', $id)
            ->update(self::ARTWORK_COMMENTS_TABLE_NAME, array('comment' => $comment));
    }

	public function insert_place_comment($user_id, $artwork_id) {
	    $data = array(
            'user_id' => $user_id,
            'artwork_id' => $artwork_id
        );

        if ($this->db->insert(self::ARTWORK_COMMENTS_TABLE_NAME, $data)) {
            return $this->db->insert_id();
        } else {
            return NULL;
        }
    }

    public function delete_place_comment($place_id) {
        $data = array(
            'place_id' => $place_id
        );

        return $this->db->delete(self::PLACE_COMMENTS_TABLE_NAME, $data);
    }



}
