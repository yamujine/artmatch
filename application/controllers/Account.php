<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// MY_Controller가 로그인 체크를 하여 /account/login route로 redirect를 하므로, 로그인 컨트롤러는 MY_Controller을 사용하지 않음
class Account extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('user_model');
        $this->load->library('twig');
        $this->load->helper('url');
    }

    public function signup() {
        $data = [];
        if ($this->input->get('isFacebook') === '1') {
            $data['email'] = $this->input->get('email');
            $data['picture'] = $this->input->get('picture');
            $data['facebook_id'] = $this->input->get('facebook_id');
        }
        $this->twig->display('account/signup', $data);
    }

    public function login() {
        // 이미 로그인된 경우 메인으로 돌리기
        if ($this->accountlib->is_login()) {
            redirect('/');
        }

        $this->twig->display('account/login');
    }

    public function logout() {
        $this->session->unset_userdata(['id', 'email', 'password', 'type', 'username', 'profile_image', 'registered_at']);
        $this->session->sess_destroy();

        redirect('/');
    }

    public function verify() {
        if ($this->accountlib->is_auth()) {
            redirect('/');
        }

        $code = preg_split('#/#', $this->encryption->decrypt($this->input->get('key', FALSE)));
        list($email, $id) = $code;

        $user = $this->user_model->get_by_id($id);
        if ($user->email === $email && $user->id === $id) {
            $result = $this->user_model->authorize($id);
            if ($result) {
                $this->accountlib->generate_user_session($user->id);
                redirect('/');
            }
        }

        $data = [
            'user' => $user,
            'msg' => '인증에 실패하였습니다.'
        ];
        $this->twig->display('account/not_auth', $data);
    }

    public function not_authenticated() {
        if ($this->accountlib->is_auth()) {
            redirect('/');
        }

        $user = $this->accountlib->get_user();

        if ($this->input->method() === 'post' && $this->input->post('resend') === '1') {
            $this->accountlib->send_email_authentication($user->email, $user->id);
            $data['msg'] = '인증 이메일 재발송이 완료되었습니다.';
        }

        $data['user'] = $user;
        $this->twig->display('account/not_auth', $data);
    }
}
