<?php

class Apply_model extends CI_Model {
    /**
     * table_name: apply
     */
    const TABLE_NAME = 'apply';

    //public $id; -- Ignore PK
    public $exhibition_id;
    public $artwork_id;
    public $status;
    public $reason;
    public $registered_at;

    public function insert($exhibition_id, $artwork_id, $status, $reason) {
        $data = [
            'exhibition_id' => $exhibition_id,
            'artwork_id' => $artwork_id,
            'status' => $status,
            'reason' => $reason
        ];

        if (!$this->db->insert(self::TABLE_NAME, $data)) {
            return false;
        }

        return $this->db->insert_id();
    }

    public function get_by_user_id($user_id) {
        return $this->db
            ->select('apply.exhibition_id')
            ->from('artworks')
            ->join(self::TABLE_NAME, 'artworks.id = apply.artwork_id')
            ->where('artworks.user_id', $user_id)
            ->order_by('apply.id', 'DESC')
            ->get()->result();
    }

    public function get_by_exhibition_id_and_artwork_id($exhibition_id, $artwork_id) {
        return $this->db
            ->from(self::TABLE_NAME)
            ->where('exhibition_id', $exhibition_id)
            ->where('artwork_id', $artwork_id)
            ->get()->row();
    }

    public function update_status($exhibition_id, $artwork_id, $status) {
        $this->db->update(self::TABLE_NAME, ['status' => $status], ['exhibition_id' => $exhibition_id, 'artwork_id' => $artwork_id]);
        if ($this->db->affected_rows() > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function delete_by_exhibition_id($exhibition_id) {
        $this->db->delete(self::TABLE_NAME, ['exhibition_id' => $exhibition_id]);

        return $this->db->affected_rows() > 0;
    }

    public function delete_by_artwork_id($artwork_id) {
        return $this->db->delete(self::TABLE_NAME, ['artwork_id' => $artwork_id]);
    }
}
