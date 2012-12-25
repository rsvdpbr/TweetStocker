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
		/* キーワードIDが異なるものを取得 */
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
		/* キーワードIDが同じ物を取得して、結果から取り除く */
		$data = $this->find('all', array(
				'conditions' => array(
					'tweet_id' => $tids,
					'keyword_id' => $kid,
				),
				'recursive' => -1,
			));
		$diff = array();
		foreach($data as $i){
			$diff[] = $i['KeywordTweet']['tweet_id'];
		}
		$result = array_diff($result, $diff);
		$result = array_merge($result);
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

	/* キーワードIDでツイートを取得 */
	public function getTweetByKeyword($kid){
		$this->unbindModel(array('belongsTo' => array('Keyword')));
		$data = $this->find('all', array(
				'fields' => array('Tweet.*'),
				'conditions' => array('keyword_id' => $kid),
				'order' => array('datetime DESC'),
			));
		$result = array();
		foreach($data as $i){
			$result[] = $i['Tweet'];
		}
		return $result;
	}

}
