<?php

class Tweet extends AppModel {

	public $hasMany = array(
		'KeywordTweet' => array(
			'className' => 'KeywordTweet',
			'foreignKey' => 'tweet_id',
		),
	);

	/* 一件のツイートデータ全体を渡すと、Tweetモデルの構造に合わせた配列を作る */
	public function format(&$data){
		if(isset($data['retweeted_status'])){
			$retweetId = $data['retweeted_status']['id'];
		}else{
			$retweetId = 0;
		}
		$result = array(
			'id' => $data['id'],
			'user_id' => $data['user']['id'],
			'retweet_id' => $retweetId,
			'text' => $data['text'],
			'source' => $data['source'],
			'entities' => $this->encodeValue($data['entities']),
			'datetime' => $this->getDatetime(strtotime($data['created_at'])),
		);
		return $result;
	}

	/* 期間を指定して、条件に一致するツイートを返す */
	public function getByPeriod($start, $end, $query){
		$default = array(
			'conditions' => array('Tweet.datetime BETWEEN ? AND ?' => array($start, $end)),
			'order' => array('Tweet.datetime')
		);
		if(isset($query['conditions'])){ /* この辺りのマージ、そのうち切り出そう */
			$default['conditions'] = array_merge($default['conditions'], $query['conditions']);
			unset($query['conditions']);
		}
		$query = array_merge($default, $query);
		$result = $this->find('all', $query);
		return $result;
	}

}
