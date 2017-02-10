<?php

class ImageUpload
{
	protected $CI;

	public function __construct()
	{
		$this->CI =& get_instance();
		$this->CI->load->library(['upload', 'image_lib']);
	}

	public function upload_images($param, $generate_thumbs = true)
	{
		$file_name = '';

		// Load upload library
		$this->CI->upload->initialize([
			'upload_path' => './uploads/',
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

	public function upload_bulk_images($param, $generate_thumbs = true)
	{
		$uploaded_file_names = [];

		$this->CI->upload->initialize([
			'upload_path' => './uploads/',
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

	private function _generate_thumbnails($original_image_path)
	{
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

		$this->CI->image_lib->initialize(array_merge($default_config, ['width' => 512, 'height' => 512]));
		$this->CI->image_lib->resize();

		$this->CI->image_lib->initialize(array_merge($default_config, ['width' => 128, 'height' => 128, 'thumb_marker' => '_thumb_small']));
		$this->CI->image_lib->resize();
	}
}
