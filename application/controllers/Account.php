<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once __DIR__ . '/../classes/UrlGenerator.php';

class Account extends MY_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('user_model');
        $this->load->helper('url');

        $this->allow_without_login = ['signup', 'login', 'verify'];
        $this->allow_without_auth = ['not_authenticated', 'verify'];
    }

    public function signup() {
        $data = [];
        if ($this->input->get('is_facebook') === '1') {
            $fb = new Facebook\Facebook([
                'app_id' => $this->config->item('app_id'),
                'app_secret' => $this->config->item('app_secret'),
                'default_graph_version' => $this->config->item('api_version')
            ]);

            $accessToken = $this->accountlib->get_facebook_access_token();

            if ($accessToken !== NULL) {
                $fb->setDefaultAccessToken($accessToken);
                $response = $fb->get('/me?fields=id,name,picture.type(large),email');
                $userNode = $response->getGraphUser();
                $data['email'] = $userNode->getEmail();
                $data['facebook_profile_image_url'] = $userNode->getPicture()->getUrl();
                $data['facebook_id'] = $userNode->getId();
            }

            $is_already_used = $this->user_model->check_email($userNode->getEmail());

            if ($is_already_used) {
                $data['duplicated_email'] = TRUE;
            }
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
        $user = $this->accountlib->get_user(true);
        if ($this->accountlib->is_auth()) {
            redirect('/');
        }

        if ($this->input->method() === 'post' && $this->input->post('resend') === '1') {
            $this->accountlib->send_email_authentication($user->email, $user->id, $user->user_name);
            $data['msg'] = '인증 이메일 재발송이 완료되었습니다.';
        }

        $data['user'] = $user;
        $this->twig->display('account/not_auth', $data);
    }
}
