<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends MY_Controller {

    public function index() {

        $this->load->library('logincheck');

        if (!$this->logincheck->is_login()) {
            $user = NULL;
        } else {
            $user = array(
                'email' => $this->logincheck->get_email(),
                'auth' => $this->logincheck->get_is_auth()
            );
        }

        $data = ['user' => $user];

        $this->twig->display('login/login', $data);
    }
}
