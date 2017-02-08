<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Places extends MY_Controller {
    function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model('place_model');
    }

    public function index() {
        $places = $this->place_model->gets();
//        print("test");
//        var_dump($places);
        $data = ['test' => true, 'places' => $places];
        $this->twig->display('places', $data);
    }

	public function detail($place_number)
	{
		echo $place_number . ' place detail';
	}
}
