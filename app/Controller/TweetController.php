<?php

class TweetController extends AppController {

	public $uses = array('Tweet', 'MemberKeyword', 'KeywordTweet');


	public function beforeFilter(){
		parent::beforeFilter();
	}

	public function beforeRender(){
		parent::beforeRender();
	}
	
	public function index(){
		$this->DataHash['id'] = 0;
		if(isset($_GET['id']) && is_numeric($_GET['id'])){
			$this->DataHash['id'] = $_GET['id'];
		}
		/* キーワード取得 */
		$memberId = $this->DataHash['member']['id'];
		$this->DataHash['keywords'] = $this->MemberKeyword->getByMemberId($memberId);
		/* ツイート取得 */
		if(isset($this->DataHash['keywords'][$this->DataHash['id']])){
			$this->DataHash['keyword'] = $this->DataHash['keywords'][$this->DataHash['id']]['keyword'];
			$this->DataHash['tweets'] = $this->KeywordTweet->getTweetByKeyword($this->DataHash['id']);
		}
	}

}

