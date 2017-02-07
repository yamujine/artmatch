<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class User extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->library('email');
        $this->load->helper('security');
        $this->load->model('user_model');
        header('Content-Type: application/json');
    }

    public function register()
    {
        /* validation 체크 필요 */
        $hash = password_hash($this->input->post('password'), PASSWORD_BCRYPT);
        $id = $this->user_model->add(array(
            'email' => $this->input->post('email'),
            'password' => $hash,
            'profile_image' => $this->input->post('profile_image'),
            'type' => $this->input->post('type')
        ));

        if ($id){
            $user = $this->user_model->get_by_id($id);
            $this->remember($user);
            $this->send_mail($user);
            /* response 정의 필요 */
            $this->output->set_output('signup success');
        } else {
            $this->output->set_status_header('500');
            $this->output->set_output($this->user_model->get_message());
            $this->user_model->clear_message();
        }
    }

    public function login()
    {
        $email = $this->input->post('email');
        $user = $this->user_model->get($email);

        if (!$user) {
            $this->output->set_status_header('500');
            $this->output->set_output($this->user_model->get_message());
            $this->user_model->clear_message();
        } else {
            $password = $this->user_model->get_password($email);
            if(password_verify($this->input->post('password'), $password))
            {
                $this->remember($user);
                $this->output->set_output('login success');
            } else {
                $this->output->set_status_header('500');
                $this->output->set_output('password incorrect');
            }
        }
    }

    public function authorize($id, $code)
    {
        $user = $this->user_model->get_by_id($id);
        if (hash_equals($code, hash('ripemd160',$user['email'].$user['idx']))){
            $result = $this->user_model->authorize($id);
            if($result)
            {
                $this->remember($user);
                $this->output->set_output('auth success');
            } else {
                $this->output->set_output('auth fail');
            }
        } else {
            $this->output->set_output('auth incorrect');
        }
    }

    public function send_mail($user)
    {

        $code = hash('ripemd160',$user['email'].$user['idx']);
        $this->email->initialize(array('mailtype'=>'html'));
        $this->load->helper('url');
        $this->email->from('admin@pickartyou.com', 'pickartyou');
        $this->email->subject('pickartyou 회원가입 링크');
        $this->email->message('<a href='.base_url().'users/'.$user['idx'].'/invite/'.$code.' style="color: white; font-weight: normal; text-decoration: none; word-break: break-word; font-size: 20px; line-height: 26px; border-top: 14px solid; border-bottom: 14px solid; border-right: 32px solid; border-left: 32px solid; background-color: #2ab27b; border-color: #2ab27b; display: inline-block; letter-spacing: 1px; min-width: 80px; text-align: center; border-radius: 4px; text-shadow: 0 1px 1px rgba(0,0,0,0.25);">
					Join
				</a>');
        $this->email->to($user['email']);
        $this->email->send();
    }

    public function remember($user)
    {
        $refresh = $this->user_model->get_by_id($user['idx']);
        $this->session->set_userdata($refresh);
        $this->session->set_userdata(array('logged_in'=> true));
    }

}