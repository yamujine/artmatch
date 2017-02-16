<?php

class Comment_model extends CI_Model {
	const ARTWORK_COMMENTS_TABLE_NAME = 'artwork_comments';
	const PLACE_COMMENTS_TABLE_NAME = 'place_comments';

	//public $id; -- Ignore PK
	public $user_id;
	public $artwork_id;
	public $place_id;
	public $comment;

	public function get_comments_by_type_id($type, $type_id) {
		if ($type === "artwork") {
			return $this->db
				->from(self::ARTWORK_COMMENTS_TABLE_NAME)
				->where('artwork_id', $type_id)
				->get()->result();
		} else if ($type === "place") {
			return $this->db
				->from(self::PLACE_COMMENTS_TABLE_NAME)
				->where('place_id', $type_id)
				->get()->result();
		} else {
			throw new Exception("type error. type=".$type);
		}
	}

    public function insert_comment($type, $user_id, $type_id, $comment) {
	    if ($type === "artwork") {
	        return $this->insert_artwork_comment($user_id, $type_id, $comment);
        } else if ($type === "place") {
            return $this->insert_place_comment($user_id, $type_id, $comment);
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

    public function update_comment($type, $type_comment_id, $comment) {
        if ($type === "artwork") {
            return $this->update_artwork_comment($type_comment_id, $comment);
        } else if ($type === "place") {
            return $this->update_place_comment($type_comment_id, $comment);
        } else {
            throw new Exception("type error. type=".$type);
        }
    }

    public function insert_artwork_comment($user_id, $artwork_id, $comment) {
	    $data = [
            'user_id' => $user_id,
            'artwork_id' => $artwork_id,
            'comment' => $comment
        ];

        $result = $this->db->insert(self::ARTWORK_COMMENTS_TABLE_NAME, $data);
        if ($result) {
            return $this->db->insert_id();
        } else {
            return NULL;
        }
    }

    public function delete_artwork_comment($id) {
        $this->db->delete(self::ARTWORK_COMMENTS_TABLE_NAME, ['id' => $id]);
		return $this->db->affected_rows();
    }

	public function update_artwork_comment($id, $comment) {
        $result = $this->db->set(['comment' => $comment])
            ->where('id', $id)
            ->update(self::ARTWORK_COMMENTS_TABLE_NAME);

        if ($result !== NULL) {
			return $id;
		} else {
			return NULL;
		}
    }

	public function insert_place_comment($user_id, $place_id, $comment) {
	    $data = [
            'user_id' => $user_id,
            'place_id' => $place_id,
			'comment' => $comment
        ];

        $result = $this->db->insert(self::PLACE_COMMENTS_TABLE_NAME, $data);
        if ($result) {
            return $this->db->insert_id();
        } else {
            return NULL;
        }
    }

	public function delete_place_comment($id) {
        $this->db->delete(self::PLACE_COMMENTS_TABLE_NAME, ['id' => $id]);
		return $this->db->affected_rows();
    }

    public function update_place_comment($id, $comment) {
        $result = $this->db->set(['comment' => $comment])
            ->where('id', $id)
            ->update(self::PLACE_COMMENTS_TABLE_NAME);

        if ($result !== NULL) {
			return $id;
		} else {
			return NULL;
		}
    }


}
