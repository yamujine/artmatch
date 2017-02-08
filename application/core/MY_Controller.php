<?php

class MY_Controller extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->library('twig');
		$this->twig->addGlobal('GLOBAL_VAR', 'It works!');
	}
}
