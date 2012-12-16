<?php

class TweetController extends AppController {

	public $uses = array('Tweet', 'Hashtag', 'User');

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
			/* 各種キーの取得 */
			$this->consumer_key = $this->Config->getByKey('consumer-key');
			$this->consumer_secret = $this->Config->getByKey('consumer-secret');
			$this->access_token = $this->Config->getByKey('access-token');
			$this->access_token_secret = $this->Config->getByKey('access-token-secret');
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
		if(!isset($param['q'])){
			throw new InvalidArgumentException('the first argument must have q(uery) key');
		}
		$this->initTwitterOAuth();
		$url = 'https://api.twitter.com/1.1/search/tweets.json';
		$obj = array(
			'result_type' => 'recent',
			'include_entities' => '1',
			'count' => '100',
		);
		$obj = array_merge($obj, $param);
		/* 取得整形 */
		$result = $this->twitter->OAuthRequest($url, 'GET', $obj);
		$result = json_decode($result);
		$result = $this->recursiveConvertFromObjectToArray($result);
		return $result;
	}

	/* ツイッターAPIから取得したデータをデータベース保存向けに整形する */
	private function formatForApiData($data){
		$tweets = array();
		$hashtags = array();
		$users = array();
		$result = array(
			'Tweet' => &$tweets,
			'Hashtag' => &$hashtags,
			'User' => &$users,
		);
		$tweetIdCache = array();
		/* retweetを配列の後ろに追加していくので、foreachじゃなくてforで回す */
		for($num=0, $len=count($data['statuses']); $num<$len; $num++){
			$i = &$data['statuses'][$num];
			/* 既に同じツイートがリストにある場合はパス */
			if(!in_array($i['id'], $tweetIdCache)){
				$tweetIdCache[] = $i['id'];
				/* リツイートは通常のツイートと同じ構造なので、ツイート配列に追加する */
				if(isset($i['retweeted_status'])){
					$data['statuses'][] = $i['retweeted_status'];
					$len = count($data['statuses']);
				}
				/* ツイートの重複チェックはループの始めで行なっているので、無条件でプッシュする */
				$tweets[] = $this->Tweet->format($i);
				/* ハッシュタグは同じタグでもツイートID毎に保持するので、無条件でプッシュする*/
				foreach($this->Hashtag->format($i) as $j){
					$hashtags[] = $j;
				}
				/* ユーザーは重複して保存する意義がないので、同じIDは無視する */
				if(!isset($users[$i['user']['id']])){
					$users[$i['user']['id']] = $this->User->format($i);
				}
			}
		}
		return $result;
	}

}

