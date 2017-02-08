<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Places extends MY_Controller {
    function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model('places_model');
    }

    public function index() {
//        $places = $this->places_model->gets();
//        print("test");
        $data = ['test' => true];
        $this->twig->display('places', $data);
    }

	public function detail($place_number)
	{
		echo $place_number . ' place detail';
	}
}
