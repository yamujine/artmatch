<?php
include_once __DIR__ . '/../classes/UrlGenerator.php';

/*
 * 이 컨트롤러를 상속받아 구현하는 컨트롤러는 Twig 템플릿 엔진을 기본으로 로드함
 */

class MY_Controller extends CI_Controller {
    /* @var array 로그인 없이 사용 가능한 함수 이름을 입력 */
    public $allow_without_login = [];
    /* @var array 이메일 인증이 필요 없이 사용 가능한 함수 이름을 입력 */
    public $allow_without_auth = [];

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->config->load('facebook');

        /** @var Twig_Environment $twig */
        $twig = $this->twig->getTwig();
        $twig->addFilter(new Twig_SimpleFilter('thumb_url', 'UrlGenerator::generate_thumb_url'));
        $twig->addFilter(new Twig_SimpleFilter('image_url', 'UrlGenerator::generate_original_image_url'));
        $twig->addFilter(new Twig_SimpleFilter('static_url', 'UrlGenerator::generate_static_url'));
        // Twig 관련 글로벌 설정은 이곳 또는 application/libraries/Twig.php 에 작성
        $this->twig->addGlobal('session', $_SESSION);
        $this->twig->addGlobal('FACEBOOK_APP_ID', $this->config->item('app_id'));
        $this->twig->addGlobal('FACEBOOK_API_VERSION', $this->config->item('api_version'));
        // 사용자 정의 상수를 Twig global로 등록
        $defined_constants = get_defined_constants(true)['user'];
        foreach ($defined_constants as $constant => $value) {
            $this->twig->addGlobal($constant, $value);
        }

        if (ENVIRONMENT === 'development') {
            $this->output->enable_profiler(TRUE);
        }
    }
}

if (!class_exists('API_Controller')) {
    require_once APPPATH . 'core/API_Controller.php';
}
