<?php
include_once __DIR__ . '/../classes/UrlGenerator.php';

class Applylib {
    protected $CI;

    public function __construct() {
        $this->CI =& get_instance();
        $this->CI->load->model(['user_model']);
        $this->CI->load->library(['twig', 'email']);
        $twig = $this->CI->twig->getTwig();
        $twig->addFilter(new Twig_SimpleFilter('thumb_url', 'UrlGenerator::generate_thumb_url'));
        $twig->addFilter(new Twig_SimpleFilter('image_url', 'UrlGenerator::generate_original_image_url'));
        $twig->addFilter(new Twig_SimpleFilter('static_url', 'UrlGenerator::generate_static_url'));
    }

    public function send_apply_email($email, $artwork_ids) {
        $this->CI->load->model('artwork_model');
        $artworks = $this->CI->artwork_model->get_by_ids($artwork_ids);

        $email_html = $this->CI->twig->render('email/apply', ['artworks' => $artworks]);

        $this->CI->email->initialize(['mailtype' => 'html']);
        $this->CI->email->from('no-reply@pickartyou.com', 'pickartyou');
        $this->CI->email->subject('[Pick Art You] 전시에 지원한 작가의 작품 리스트 입니다.');
        $this->CI->email->message($email_html);
        $this->CI->email->to($email);
        $this->CI->email->send();
    }
}