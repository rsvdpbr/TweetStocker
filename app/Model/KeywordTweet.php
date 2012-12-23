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
	
	/* キーワードIDは違うがツイートIDが一致するデータを取得 */
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

	/* キーワード毎のツイート数を取得 */
	public function getCountByKeyword($kid){
		$data = $this->find('all', array(
				'fields' => array('keyword_id', 'count(*) AS count'),
				'conditions' => array('keyword_id' => $kid),
				'group' => array('keyword_id'),
				'recursive' => -1,
			));
		$result = array();
		foreach($data as $i){
			$result[$i['KeywordTweet']['keyword_id']] = $i[0]['count'];
		}
		return $result;
	}
	
}
