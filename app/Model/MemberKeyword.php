<?php

class MemberKeyword extends AppModel {
	
	public $belongsTo = array(
		'Member' => array(
			'className' => 'Member',
			'foreignKey' => 'member_id'
		),
		'Keyword' => array(
			'className' => 'Keyword',
			'foreignKey' => 'keyword_id',
		),
	);

	/* 渡されたMeberIDに関連付けられているキーワードを返す */
	public function getByMemberId($memberId){
		$this->unbindModel(array('belongsTo' => array('Member')));
		$data = $this->find('all', array(
				'conditions' => array(
					'MemberKeyword.member_id' => $memberId,
				),
			));
		$result = array();
		foreach($data as $i){
			$result[$i['Keyword']['id']] = $i['Keyword'];
		}
		return $result;
	}

	/* 渡されたMemberIdとKeywordIdの関連付けを確認する */
	public function checkAuthentication($memberId, $keywordId){
		$this->unbindModel(array('belongsTo' => array('Member')));
		$data = $this->find('first', array(
				'conditions' => array(
					'MemberKeyword.member_id' => $memberId,
					'MemberKeyword.keyword_id' => $keywordId,
				),
			));
		if($data){
			return $data['Keyword'];
		}else{
			return array();
		}
		
	}
	
}
