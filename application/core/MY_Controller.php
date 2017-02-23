<?php

/*
 * 이 컨트롤러를 상속받아 구현하는 컨트롤러는 Twig 템플릿 엔진을 기본으로 로드함
 */

class MY_Controller extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->library('twig');
        /** @var Twig_Environment $twig */
        $twig = $this->twig->getTwig();
        /**
         * 썸네일 이미지 URL 리턴하는 함수
         */
        $twig->addFilter(new Twig_SimpleFilter('thumb_url', function ($filename, $type = '', $size = '_thumb_small') {
            if (empty($filename)) {
                return ''; // Fallback error image
            }

            if (ENVIRONMENT === 'production') {
                $host = 'http://img.pickartyou.com/';
            } else {
                $host = '/uploads/';
            }

            if (!empty($type)) {
                $host .= $type . '/';
            }

            $path_parts = pathinfo($filename);

            if (!isset($path_parts['extension'])) {
                return ''; // Fallback error image
            }

            return $host . $path_parts['filename'] . $size . '.' . $path_parts['extension'];
        }));

        /**
         * 일반 이미지 URL 리턴
         */
        $twig->addFilter(new Twig_SimpleFilter('image_url', function ($filename, $type = '') {
            if (empty($filename)) {
                return ''; // Fallback error image
            }

            if (ENVIRONMENT === 'production') {
                $host = 'http://img.pickartyou.com/';
            } else {
                $host = '/uploads/';
            }

            if (!empty($type)) {
                $host .= $type . '/';
            }

            return $host . $filename;
        }));

        /**
         * static URL 리턴
         */
        $twig->addFilter(new Twig_SimpleFilter('static_url', function ($filename) {
            if (ENVIRONMENT === 'production') {
                $host = 'http://static.pickartyou.com/';
            } else {
                $host = '../../static/';
            }

            return $host . $filename;
        }));

        $this->load->helper('url');
        // Twig 관련 글로벌 설정은 이곳 또는 application/libraries/Twig.php 에 작성
        $this->twig->addGlobal('session', $_SESSION);

        // 사용자 정의 상수를 Twig global로 등록
        $defined_constants = get_defined_constants(true)['user'];
        foreach ($defined_constants as $constant => $value) {
            $this->twig->addGlobal($constant, $value);
        }

        if ($this->accountlib->is_login() === false) {
            redirect('/account/login');
        } elseif ($this->accountlib->is_auth() === false) {
            redirect('/account/not_authenticated');
        }
    }
}

if (!class_exists('API_Controller')) {
    require_once APPPATH . 'core/API_Controller.php';
}
