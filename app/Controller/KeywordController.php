<?php

class KeywordController extends AppController {

	public $uses = array('Keyword', 'MemberKeyword', 'KeywordTweet');

	public function beforeFilter(){
		parent::beforeFilter();
	}

	public function beforeRender(){
		parent::beforeRender();
	}
	
	public function index(){
		$memberId = $this->DataHash['member']['id'];
		/* keywordデータを取得 */
		$keywords = $this->MemberKeyword->getByMemberId($memberId);
		$ids = array();
		foreach($keywords as $i){
			$ids[] = $i['id'];
		}
		foreach($this->KeywordTweet->getCountByKeyword($ids) as $id => $count){
			$keywords[$id]['count'] = $count;
		}
		$this->DataHash['keyword'] = $keywords;
	}

}
