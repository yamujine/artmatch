<?php

class Artwork_comment_model extends CI_Model {
    const ARTWORK_COMMENTS_TABLE_NAME = 'artwork_comments';

    //public $id; -- Ignore PK
    public $user_id;
    public $artwork_id;
    public $comment;

    public function get($artwork_comment_id) {
        return $this->db
            ->select('artwork_comments.*, users.user_name, users.profile_image')
            ->from(self::ARTWORK_COMMENTS_TABLE_NAME)
            ->join('users', 'users.id = artwork_comments.user_id')
            ->where('artwork_comments.id', $artwork_comment_id)
            ->get()->row();
    }

    public function get_count_by_artwork_id($artwork_id) {
        return $this->db
            ->from(self::ARTWORK_COMMENTS_TABLE_NAME)
            ->where('artwork_id', $artwork_id)
            ->count_all_results();
    }

    /**
     * 상세페이지 코멘트 가져오기용 쿼리
     * @param $artwork_id
     * @return mixed
     */
    private function get_query_for_comments_by_artwork_id($artwork_id) {
        $query = $this->db
            ->select('artwork_comments.*, users.user_name, users.profile_image')
            ->from(self::ARTWORK_COMMENTS_TABLE_NAME)
            ->join('users', 'users.id = artwork_comments.user_id')
            ->where('artwork_id', $artwork_id);

        return $query;
    }

    /**
     * get_query_for_comments_by_artwork_id을 이용한 코멘트 리턴
     * @param $artwork_id
     * @param null $limit
     * @param null $offset
     * @return mixed
     */
    public function get_comments_by_artwork_id($artwork_id, $limit = null, $offset = null) {
        $query = $this->get_query_for_comments_by_artwork_id($artwork_id);

        if ($limit !== null && $offset !== null) {
            $query = $query->limit($limit, $offset);
        }

        $query = $query->order_by('id', 'DESC');

        return $query->get()->result();
    }

    /**
     * get_query_for_comments_by_artwork_id을 이용한 코멘트 수 리턴
     * @param $artwork_id
     * @return mixed
     */
    public function get_count_of_comments_by_artwork_id($artwork_id) {
        $query = $this->get_query_for_comments_by_artwork_id($artwork_id);

        return $query->get()->num_rows();
    }

    public function insert_comment($user_id, $artwork_id, $comment) {
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

    public function delete_comment($artwork_comment_id) {
        $this->db->delete(self::ARTWORK_COMMENTS_TABLE_NAME, ['id' => $artwork_comment_id]);

        return $this->db->affected_rows();
    }

    public function update_comment($artwork_id, $comment) {
        $result = $this->db->set(['comment' => $comment])
            ->where('id', $artwork_id)
            ->update(self::ARTWORK_COMMENTS_TABLE_NAME);

        if ($result !== NULL) {
            return $artwork_id;
        } else {
            return NULL;
        }
    }

    public function delete_all_comments_by_artwork_id($artwork_id) {
        return $this->db->delete(self::ARTWORK_COMMENTS_TABLE_NAME, ['artwork_id' => $artwork_id]);
    }
}
