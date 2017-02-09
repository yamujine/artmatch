<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends CI_Controller {
	public function detail($user_id)
	{
		echo $user_id . ' mypage';
	}

	public function me()
	{
		echo 'personal';
	}
}
