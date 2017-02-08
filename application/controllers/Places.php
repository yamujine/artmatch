<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Places extends CI_Controller {
    public function index() {
        echo "장소 목록 입니다.";
    }

	public function detail($place_number)
	{
		echo $place_number . ' place detail';
	}
}
