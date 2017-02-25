<?php

class Place_Comment_model extends CI_Model {
    const PLACE_COMMENTS_TABLE_NAME = 'place_comments';

    //public $id; -- Ignore PK
    public $user_id;
    public $place_id;
    public $comment;

    public function get_by_id($place_comment_id) {
        return $this->db
            ->select('place_comments.*, users.user_name, users.profile_image')
            ->from(self::PLACE_COMMENTS_TABLE_NAME)
            ->join('users', 'users.id = place_comments.user_id')
            ->where('place_comments.id', $place_comment_id)
            ->get()->row();
    }

    public function get_count_by_place_id($place_id) {
        return $this->db
            ->from(self::PLACE_COMMENTS_TABLE_NAME)
            ->where('place_id', $place_id)
            ->count_all_results();
    }

    /**
     * 상세페이지 코멘트 가져오기용 쿼리
     * @param $place_id
     * @return mixed
     */
    private function get_query_for_comments_by_place_id($place_id) {
        $query = $this->db
            ->select('place_comments.*, users.user_name, users.profile_image')
            ->from(self::PLACE_COMMENTS_TABLE_NAME)
            ->join('users', 'users.id = place_comments.user_id')
            ->where('place_id', $place_id);

        return $query;
    }

    /**
     * get_query_for_comments_by_place_id을 이용한 코멘트 리턴
     * @param $place_id
     * @param null $limit
     * @param null $offset
     * @return mixed
     */
    public function get_comments_by_place_id($place_id, $limit = null, $offset = null) {
        $query = $this->get_query_for_comments_by_place_id($place_id);

        if ($limit !== null && $offset !== null) {
            $query = $query->limit($limit, $offset);
        }

        $query = $query->order_by('id', 'ASC');

        return $query->get()->result();
    }

    /**
     * get_query_for_comments_by_place_id을 이용한 코멘트 수 리턴
     * @param $place_id
     * @return mixed
     */
    public function get_count_of_comments_by_place_id($place_id) {
        $query = $this->get_query_for_comments_by_place_id($place_id);

        return $query->get()->num_rows();
    }

    public function insert_comment($user_id, $place_id, $comment) {
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

    public function delete_comment($id) {
        $this->db->delete(self::PLACE_COMMENTS_TABLE_NAME, ['id' => $id]);

        return $this->db->affected_rows();
    }

    public function update_comment($id, $comment) {
        $result = $this->db->set(['comment' => $comment])
            ->where('id', $id)
            ->update(self::PLACE_COMMENTS_TABLE_NAME);

        if ($result !== NULL) {
            return $id;
        } else {
            return NULL;
        }
    }

    public function delete_all_comments_by_place_id($place_id) {
        return $this->db->delete(self::PLACE_COMMENTS_TABLE_NAME, ['place_id' => $place_id]);
    }
}
