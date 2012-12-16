<?php

class Hashtag extends AppModel {

	/* 一件のツイートデータ全体を渡すと、Hashtagモデルの構造に合わせた配列を作る */
	public function format(&$data){
		$result = array();
		foreach($data['entities']['hashtags'] as $hashtag){
			$result[] = array(
				'id' => null,
				'tweet_id' => $data['id'],
				'text' => $hashtag['text'],
				'index_start' => $hashtag['indices'][0],
				'index_end' => $hashtag['indices'][1],
			);
		}
		return $result;
	}
	

}
