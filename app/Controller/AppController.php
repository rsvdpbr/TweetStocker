<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {

	public $uses = array('Config');
	public $components = array('Auth', 'Session');
	protected $DataHash;

	public function beforeFilter(){
		$this->DataHash = array();
		$this->configureAuthComponent();
		$this->setDefaultHeaderMenu();
		$this->setDefaultFooterMenu();
		$this->request->data = $this->recursiveStripNullByte($this->request->data);
		$this->request->params['named'] = $this->recursiveStripNullByte($this->request->params['named']);
	}
	public function beforeRender(){
		$this->DataHash = $this->recursiveSanitize($this->DataHash);
		$this->set('DataHash', $this->DataHash);
	}

	/* AuthComponent周りの設定 */
	private function configureAuthComponent(){
		$this->Auth->authenticate = array('Form' => array('userModel' => 'Member'));
		$this->Auth->loginAction = '/login';
		$this->Auth->loginRedirect = '/';
		$this->Auth->logoutRedirect = '/login';
		$this->Auth->authError = "ユーザー認証が必要です。";
		$this->DataHash['member'] = $this->Auth->user();
		if(!$this->DataHash['member']) $this->DataHash['member'] = array();
	}

	/* ヘッダー・フッター用データ初期化 */
	private function setDefaultFooterMenu(){
		$this->DataHash['header'] = array();
		if($this->DataHash['member']){
			$this->DataHash['header']['ツイート'] = '/';
			$this->DataHash['header']['キーワード'] = '/keyword';
			$this->DataHash['header']['ログアウト'] = '/logout';
		}else{
			$this->DataHash['header']['ログイン'] = '/login';
			$this->DataHash['header']['ユーザー登録'] = '/register';
		}
	}
	private function setDefaultHeaderMenu(){
		$this->DataHash['footer'] = array();
		$this->DataHash['footer']['トップに戻る'] = '#header';
	}

	/* セキュリティ対策関数 */
	private function recursiveSanitize($arr){
		if(is_array($arr)){
			foreach($arr as $key => $value){
				$arr[$key] = $this->recursiveSanitize($value);
			}
		}else{
			if(!is_object($arr)){
				$arr = htmlspecialchars($arr, ENT_QUOTES, 'utf-8');
			}
		}
		return $arr;
	}
	private function recursiveStripNullByte($arr){
		if(is_array($arr)){
			foreach($arr as $key => $value){
				$arr[$key] = $this->recursiveStripNullByte($value);
			}
		}else{
			if(!is_object($arr)){
				$arr = str_replace("\0", '', $arr);
			}
		}
		return $arr;
	}
	
}
