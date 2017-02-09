<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->load->library('session');
        $user = array(
            'email' => $this->session->userdata('email'),
            'auth' => $this->session->userdata('is_auth')
        );
        if (empty($user)) {
            $user = NULL;
        }

        $this->load->view('login', $user);
    }
}