<?php

/* モデルのトランザクション処理を統括する */
class TransactionComponent extends Component {

	/* トランザクション処理を行なっている最中のモデルを格納 */
	private $models = null;

	/* 渡されたモデルに関してトランザクション処理を開始する */
	public function begin($models){
		if($this->models) throw new Exception('cannot nest transactions in MySQL');
		/* データベースの接続先が異なるとき二相コミットを行わなければいけないが、*/
		/* ここでは必要ないし面倒なので、とりあえずエラー吐くだけで済ませる */
		for($i=0, $len=count($models)-1; $i<$len; $i++){
			if($models[$i]->useDbConfig != $models[$i+1]->useDbConfig){
				throw new Exception('cannot use multiple databases in the same transaction');
			}
		}
		$this->models = $models;
		foreach($models as $model){
			$model->begin();
		}
	}

	/* トランザクション処理を終了（コミットorロールバック）する */
	public function commit(){ $this->finish("commit"); }
	public function rollback(){ $this->finish("rollback"); }
	private function finish($action){
		if(!$this->models) throw new Exception('transaction is not yet started');
		foreach($this->models as $model){
			$model->{$action}();
		}
		$this->models = null;
		
	}
	
}