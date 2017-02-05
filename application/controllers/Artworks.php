<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Artwork extends CI_Controller {
	public function detail($artwork_number)
	{
		echo $artwork_number . ' artwork detail';
	}
}
