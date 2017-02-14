<?php

class Imageupload {
    protected $CI;

    const UPLOAD_PATH = './uploads/';
    const THUMBNAIL_POSTFIX = '_thumb';
    const THUMBNAIL_SMALL_POSTFIX = '_thumb_small';

    public function __construct() {
        $this->CI =& get_instance();
        $this->CI->load->library(['upload', 'image_lib']);
    }

    public function upload_images($param, $generate_thumbs = true, $image_type = '') {
        $file_name = '';
        if (!empty($image_type)) {
            $upload_path = self::UPLOAD_PATH . $image_type . '/';
        }

        // Load upload library
        $this->CI->upload->initialize([
            'upload_path' => $upload_path,
            'allowed_types' => 'gif|jpg|png|jpeg',
            'file_ext_tolower' => TRUE,
            'max_size' => 2048, // 2MB
            'encrypt_name' => TRUE
        ]);

        // Upload Image
        if ($this->CI->upload->do_upload($param)) {
            // Generate Thumbnails
            if ($generate_thumbs) {
                $this->_generate_thumbnails($this->CI->upload->data('full_path'));
            }
            $file_name = $this->CI->upload->data('file_name');
        }

        return $file_name;
    }

    public function upload_bulk_images($param, $generate_thumbs = true) {
        $uploaded_file_names = [];

        $this->CI->upload->initialize([
            'upload_path' => self::UPLOAD_PATH,
            'allowed_types' => 'gif|jpg|png|jpeg',
            'file_ext_tolower' => TRUE,
            'max_size' => 2048, // 2MB
            'encrypt_name' => TRUE
        ]);

        // Upload Images
        if ($this->CI->upload->do_multi_upload($param)) {
            foreach ($this->CI->upload->get_multi_upload_data() as $uploaded_data) {
                if ($generate_thumbs) {
                    $this->_generate_thumbnails($uploaded_data['full_path']);
                }
                $uploaded_file_names[] = $uploaded_data['file_name'];
            }
        }

        return $uploaded_file_names;
    }

    private function _generate_thumbnails($original_image_path) {
        /*
         * _thumb -> 512x512
         * _thumb_small -> 128x128
         */
        $default_config = [
            'image_library' => 'gd2',
            'source_image' => $original_image_path,
            'create_thumb' => TRUE,
            'maintain_ratio' => TRUE
        ];

        $this->CI->image_lib->initialize(array_merge($default_config, ['width' => 256, 'height' => 256]));
        $this->CI->image_lib->resize();
        $this->CI->image_lib->clear();

        $this->CI->image_lib->initialize(array_merge($default_config, ['width' => 128, 'height' => 128, 'thumb_marker' => self::THUMBNAIL_SMALL_POSTFIX]));
        $this->CI->image_lib->resize();
        $this->CI->image_lib->clear();
    }

    public function delete_image($filename) {
        @unlink(self::UPLOAD_PATH . $filename);

        $filename_meta = explode('.', $filename);
        $filename_only = $filename_meta[0];
        $file_ext = '.' . $filename_meta[1];

        @unlink(self::UPLOAD_PATH . $filename_only . self::THUMBNAIL_POSTFIX . $file_ext);
        @unlink(self::UPLOAD_PATH . $filename_only . self::THUMBNAIL_SMALL_POSTFIX . $file_ext);
    }
}
