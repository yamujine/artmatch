<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TestsComment extends MY_Controller {
    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $this->twig->display('tests/comment');
    }

}
