<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends MY_Controller {
	public function index()
	{
		$data = ['test' => true];
		$this->twig->display('welcome', $data);
	}
}
