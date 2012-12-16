<?php

class User extends AppModel {

	/* 一件のツイートデータ全体を渡すと、Userモデルの構造に合わせた配列を作る */
	public function format(&$data){
		$result = array(
			'id' => $data['user']['id'],
			'name' => $data['user']['name'],
			'screen_name' => $data['user']['screen_name'],
			'location' => $data['user']['location'],
			'description' => $data['user']['description'],
			'url' => $data['user']['url'],
			'entities' => $this->encodeValue($data['user']['entities']),
		);
		return $result;
	}
	

}
