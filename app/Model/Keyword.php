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

}

