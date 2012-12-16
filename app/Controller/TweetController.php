<?php

class TweetController extends AppController {

	private $twitter = null;
	private $consumer_key = null;
	private $consumer_secret = null;
	private $access_token = null;
	private $access_token_secret = null;

	public function beforeFilter(){
		parent::beforeFilter();
	}

	public function beforeRender(){
		parent::beforeRender();
	}
	
	public function index(){
	}

	/* ツイッターAPIにアクセスするためのオブジェクトを必要に応じて初期化する */
	private function initTwitterOAuth(){
		if(!$this->twitter){
			/* 各種キーの設定（後にデータベースに移動かな） */
			$this->consumer_key = 'd625JmYrfaYwHgh4JwKBEg';
			$this->consumer_secret = 'WJcBTHH78mtZRbnZQbwVrvRrJFTatsNaeqO6TDVo';
			$this->access_token = "704377298-qv6PM0NWzrbcaO2mLQg2oC1AMTm2Lhtpg9jcYIkG";
			$this->access_token_secret = "RZ9Du7Jac0NQJwpEajAy6vJdFORDqWJ0ILNmRRipHIc";
			/* TwitterOAuthオブジェクトの生成 */
			App::import('Vendor', 'TwitterOAuth', array('file'=>'TwitterOAuth/twitteroauth.php'));
			$this->twitter = new TwitterOAuth(
				$this->consumer_key, $this->consumer_secret,
				$this->access_token, $this->access_token_secret
			);
		}
	}

	/* オブジェクトと配列からなるデータを再帰的に全て配列にキャストする */
	private function recursiveConvertFromObjectToArray($obj){
		if (!is_object($obj) && !is_array($obj)){
			return $obj;
		}
		$arr = (array)$obj;
		foreach ($arr as &$a){
			$a = $this->recursiveConvertFromObjectToArray($a);
		}
		return $arr;
	}

	/* ツイッターAPIを用いて、キーワードで検索を行う */
	private function twitterSearch($param){
		/* 各項目の設定 */
		$this->initTwitterOAuth();
		$url = 'https://api.twitter.com/1.1/search/tweets.json';
		$obj = array(
			'q' => $param['query'],
			'result_type' => 'recent',
			'include_entities' => '1',
			'count' => '100',
		);
		if(isset($param['max_id'])) $obj['max_id'] = $param['max_id'];
		if(isset($param['since_id'])) $obj['since_id'] = $param['since_id'];
		/* 取得整形 */
		$result = $this->twitter->OAuthRequest($url, 'GET', $obj);
		$result = json_decode($result);
		$result = $this->recursiveConvertFromObjectToArray($result);
		return $result;
	}

}

