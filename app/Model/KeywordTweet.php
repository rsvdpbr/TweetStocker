<?php

class KeywordTweet extends AppModel {
	
	public $belongsTo = array(
		'Keyword' => array(
			'className' => 'Keyword',
			'foreignKey' => 'keyword_id',
		),
		'Tweet' => array(
			'className' => 'Tweet',
			'foreignKey' => 'tweet_id'
		),
	);
	
	/*  */
	public function getByTweetIdsAndOtherKeywordId($tids, $kid){
		$data = $this->find('all', array(
				'conditions' => array(
					'tweet_id' => $tids,
					'not' => array(
						'keyword_id' => $kid,
					),
				),
				'recursive' => -1,
			));
		$result = array();
		foreach($data as $i){
			$result[] = $i['KeywordTweet']['tweet_id'];
		}
		return $result;
	}
	
}
