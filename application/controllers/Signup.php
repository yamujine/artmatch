<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Signup extends MY_Controller
{
    public function index()
    {
        $this->twig->display('signup/signup');
    }
}
