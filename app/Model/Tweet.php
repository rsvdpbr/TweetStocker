<?php

class Tweet extends AppModel {

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
	

}
