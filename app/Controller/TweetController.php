<?php

class TweetController extends AppController {

	public $uses = array('Tweet');


	public function beforeFilter(){
		parent::beforeFilter();
	}

	public function beforeRender(){
		parent::beforeRender();
	}
	
	public function index(){
	}

}

