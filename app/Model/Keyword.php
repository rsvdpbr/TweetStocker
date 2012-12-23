<?php

class Keyword extends AppModel {

	public $hasMany = array(
		'KeywordTweet' => array(
			'className' => 'KeywordTweet',
			'foreignKey' => 'keyword_id',
			'fields' => array('id', 'keyword_id', 'tweet_id'),
		),
		'MemberKeyword' => array(
			'className' => 'MemberKeyword',
			'foreignKey' => 'keyword_id',
			'fields' => array('id', 'member_id', 'keyword_id'),
		),
	);

	public function getOrderByLastUpdate($limit){
		$data = $this->find('all', array(
				'order' => array('last_update ASC'),
				'limit' => $limit,
				'recursive' => -1,
			));
		$result = array();
		foreach($data as $i){
			$result[] = $i['Keyword'];
		}
		return $result;
	}

}

