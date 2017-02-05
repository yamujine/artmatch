<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Place extends CI_Controller {
	public function detail($place_number)
	{
		echo $place_number . ' place detail';
	}
}
