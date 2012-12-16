<?php

class TweetController extends AppController {

	public $uses = array('Tweet', 'Hashtag', 'User');
	public $components = array('Transaction');

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

	/* ツイッターAPIから取得したデータの中から、既にデータベースに存在するものを削除し、 */
	/* 削除したidを配列にして返す。引数に与えたデータには副作用がある */
	private function checkDuplicationForApiData(&$data){
		/* 取得データから一番最初と一番最後の日時を取得 */
		$timestamp = array();
		foreach($data['statuses'] as $i){
			$timestamp[] = strtotime($i['created_at']);
		}
		$start = date('Y-m-d H:i:s', min($timestamp));
		$end = date('Y-m-d H:i:s', max($timestamp));
		/* 取得したデータの期間に属するデータベース上のデータのID一覧を取得 */
		$query = array('fields' => array('id'));
		$idList = $this->Tweet->getByPeriod($start, $end, $query);
		foreach($idList as $key => $value){
			$idList[$key] = $value['Tweet']['id'];
		}
		/* 既にデータベースに存在するIDのツイートを取得したデータから削除 */
		$result = array();
		foreach($data['statuses'] as $key => $value){
			if(in_array($value['id'], $idList)){
				unset($data['statuses'][$key]);
				$result[] = $value['id'];
			}
		}
		/* 削除によって添字がとんでいる可能性があるので、添字を振り直す */
		$data['statuses'] = array_merge($data['statuses']);
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

	/* ツイッターAPIから取得し整形関数にかけたデータを、データベースに保存する */
	private function saveForApiData($data){
		$models = array(
			'Tweet' => $this->Tweet,
			'Hashtag' => $this->Hashtag,
			'User' => $this->User
		);
		$this->Transaction->begin(array_values($models));
		/* トランザクション中に例外が出た場合は、即時ロールバックを行い改めて例外を投げる */
		/* ただし、save関数の返り値がfalseの場合に意図的に投げる例外は、ロールバックのみを行う */
		$saveError = "SaveError";
		try {
			foreach($models as $name => $model){
				foreach($data[$name] as $i){
					if($i['id']){
						$model->id = $i['id'];
					}else{
						$model->create();
					}
					if(!$model->save($i)){
						throw new Exception($saveError);
					}
				}
			}
			$this->Transaction->commit();
			return true;
		} catch (Exception $e) {
			$this->Transaction->rollback();
			if($e->getMessage() != $saveError){
				throw $e;
			}
		}
		return false;
	}

}

