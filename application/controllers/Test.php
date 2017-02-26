<?php

/**
 * Created by PhpStorm.
 * User: kimtree
 * Date: 2017. 2. 26.
 * Time: PM 12:11
 */
class Test extends MY_Controller {
    public function index() {
        $emails = ['yjs930915@gmail.com',
            'yangjunghooon@gmail.com',
            'waterboy1010@gmail.com',
            'storiwaterboy@gmail.com',
            'storifam@gmail.com',
            'shj459@hanmail.net',
            'sea_voice@naver.com',
            'sea-wjd@nate.com',
            'polre202@naver.com',
            'msql@naver.com',
            'mintplo21@gmail.com',
            'mattoli@naver.com',
            'leekeejeong@hanmail.net',
            'koj1337@naver.com',
            'iam@kimtree.net',
            'fried5@naver.com',
            'callingsong@gmail.com',
            'bansoonmi@gmail.com',
            'ahbin7@naver.com'];

        foreach ($emails as $email) {
            $email_html = $this->twig->render('email/open', ['email' => $email]);
            $this->email->initialize(['mailtype' => 'html']);
            $this->email->from('no-reply@pickartyou.com', 'PICK ART YOU');
            $this->email->subject('[PICK ART YOU] 서비스가 오픈하였습니다!');
            $this->email->message($email_html);
            $this->email->to($email);
            $this->email->send();
        }

        echo 'done';
    }
}
