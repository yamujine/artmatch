<?php

class Comment_model extends CI_Model {
    const ARTWORK_COMMENTS_TABLE_NAME = 'artwork_comments';
    const PLACE_COMMENTS_TABLE_NAME = 'place_comments';

    //public $id; -- Ignore PK
    public $user_id;
    public $artwork_id;
    public $place_id;
    public $comment;

    public function get_by_type_and_id($type, $comment_id) {
        if ($type === TYPE_ARTWORKS) {
            return $this->db
                ->select('artwork_comments.*, users.user_name, users.profile_image')
                ->from(self::ARTWORK_COMMENTS_TABLE_NAME)
                ->join('users', 'users.id = artwork_comments.user_id')
                ->where('artwork_comments.id', $comment_id)
                ->get()->row();
        } else if ($type === TYPE_PLACES) {
            return $this->db
                ->select('place_comments.*, users.user_name, users.profile_image')
                ->from(self::PLACE_COMMENTS_TABLE_NAME)
                ->join('users', 'users.id = place_comments.user_id')
                ->where('place_comments.id', $comment_id)
                ->get()->row();
        }
    }

    public function get_count_by_type_id($type, $type_id) {
        if ($type === TYPE_ARTWORKS) {
            return $this->db
                ->from(self::ARTWORK_COMMENTS_TABLE_NAME)
                ->where('artwork_id', $type_id)
                ->count_all_results();
        } else if ($type === TYPE_PLACES) {
            return $this->db
                ->from(self::PLACE_COMMENTS_TABLE_NAME)
                ->where('place_id', $type_id)
                ->count_all_results();
        }
    }

    /**
     * 상세페이지 코멘트 가져오기용 쿼리
     * @param $type
     * @param $type_id
     * @return mixed
     */
    private function get_query_for_comments_by_type_id($type, $type_id) {
        if ($type === TYPE_ARTWORKS) {
            $query = $this->db
                ->select('artwork_comments.*, users.user_name, users.profile_image')
                ->from(self::ARTWORK_COMMENTS_TABLE_NAME)
                ->join('users', 'users.id = artwork_comments.user_id')
                ->where('artwork_id', $type_id);
        } else if ($type === TYPE_PLACES) {
            $query = $this->db
                ->select('place_comments.*, users.user_name, users.profile_image')
                ->from(self::PLACE_COMMENTS_TABLE_NAME)
                ->join('users', 'users.id = place_comments.user_id')
                ->where('place_id', $type_id);
        }

        return $query;
    }

    /**
     * get_query_for_comments_by_type_id을 이용한 코멘트 리턴
     * @param $type
     * @param $type_id
     * @param null $limit
     * @param null $offset
     * @return mixed
     */
    public function get_comments_by_type_id($type, $type_id, $limit = null, $offset = null) {
        $query = $this->get_query_for_comments_by_type_id($type, $type_id);

        if ($limit !== null && $offset !== null) {
            $query = $query->limit($limit, $offset);
        }

        $query = $query->order_by('id', 'ASC');

        return $query->get()->result();
    }

    /**
     * get_query_for_comments_by_type_id을 이용한 코멘트 수 리턴
     * @param $type
     * @param $type_id
     * @return mixed
     */
    public function get_count_of_comments_by_type_id($type, $type_id) {
        $query = $this->get_query_for_comments_by_type_id($type, $type_id);

        return $query->get()->num_rows();
    }

    public function insert_comment($type, $user_id, $type_id, $comment) {
        if ($type === TYPE_ARTWORKS) {
            return $this->insert_artwork_comment($user_id, $type_id, $comment);
        } else if ($type === TYPE_PLACES) {
            return $this->insert_place_comment($user_id, $type_id, $comment);
        }
    }

    public function delete_comment($type, $type_comment_id) {
        if ($type === TYPE_ARTWORKS) {
            return $this->delete_artwork_comment($type_comment_id);
        } else if ($type === TYPE_PLACES) {
            return $this->delete_place_comment($type_comment_id);
        }
    }

    public function update_comment($type, $type_comment_id, $comment) {
        if ($type === TYPE_ARTWORKS) {
            return $this->update_artwork_comment($type_comment_id, $comment);
        } else if ($type === TYPE_PLACES) {
            return $this->update_place_comment($type_comment_id, $comment);
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

    public function delete_all_comments_by_artwork_id($artwork_id) {
        return $this->db->delete(self::ARTWORK_COMMENTS_TABLE_NAME, ['artwork_id' => $artwork_id]);
    }

    public function delete_all_comments_by_place_id($place_id) {
        return $this->db->delete(self::PLACE_COMMENTS_TABLE_NAME, ['place_id' => $place_id]);
    }
}
