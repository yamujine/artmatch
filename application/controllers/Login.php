<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends MY_Controller
{
    public function index()
    {
        $this->load->library('session');

        $email = $this->session->userdata('email');
        $auth = $this->session->userdata('is_auth');

        if (empty($email) && empty($auth)) {
            $user = NULL;
        } else {
            $user = array(
                'email' => $email,
                'auth' => $auth
            );
        }

        $data = ['user' => $user];

        $this->twig->display('login', $data);
    }
}
