<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// MY_Controller가 로그인 체크를 하여 /account/login route로 redirect를 하므로, 이 컨트롤러는 MY_Controller을 사용하지 않음
class Landing extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->library('twig');
    }

    public function index() {
        $this->twig->display('landing/index');
    }
}
