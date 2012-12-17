<?php

class MemberController extends AppController {

	public function beforeFilter(){
		parent::beforeFilter();
		$this->Auth->allow('login', 'register');
	}

	public function login(){
		if($this->Auth->loggedIn()){
			return $this->redirect($this->Auth->loginRedirect);
		}
		if($this->request->is('post')){
			if($this->Auth->login()){
				return $this->redirect($this->Auth->loginRedirect);
			}else{
				$this->Session->setFlash(__('ユーザー名もしくはパスワードが正しくありません。'), 'default', array(), 'auth');
			}
		}
	}

	public function logout($id = null){
		$this->redirect($this->Auth->logout());
	}

	public function register() {
		if($this->request->is('post')){
			$data = $this->request->data;
			$this->Member->create();
			if($data['Member']['password'] == $data['Member']['password_confirm']){
				if($this->Member->save($data)){
					$this->Session->setFlash(__('ユーザーを作成しました。'), 'default', array(), 'auth');
					$this->redirect($this->Auth->loginAction);
				}else{
					$this->Session->setFlash(__('ユーザーの作成に失敗しました。'), 'default', array(), 'auth');
				}
			}else{
				$this->DataHash['password_error'] = true;
				$this->Session->setFlash(__('ユーザーの作成に失敗しました。'), 'default', array(), 'auth');
				$this->Member->set($data);
				$this->Member->validates();
			}
		}
	}

}

