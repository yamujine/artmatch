<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Login extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
    }
    public function index()
    {
        $user = array(
            'email' => $this->session->userdata('email'),
            'auth' => $this->session->userdata('is_auth')
            );

        $this->load->view('login', $user);
    }
}