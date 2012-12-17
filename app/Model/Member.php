<?php

class Member extends AppModel {

	public $hasMany = array(
		'Keyword' => array(
			'className' => 'Keyword',
			'foreignKey' => 'member_id',
		),
	);
	public $validate = array(
		'username' => array(
			'required' => array(
				'rule' => array('notEmpty'),
				'message' => 'ユーザー名を入力してください。'
			),
			'alphaNumeric' => array(
				'rule' => array('custom', '/^[a-zA-Z0-9\-_]*$/'),
				'message' => '半角英数字とハイフン（-）、アンダーバー（_）で入力してください'
			),
			'between' => array(
				'rule' => array('between', 5, 25),
				'message' => '５〜２５文字の範囲で入力してください'
			),
			'isUnique' => array(
				'rule' => array('isUnique'),
				'message' => 'このユーザ名はすでに使用されています。'
			),
		),
		'password' => array(
			'required' => array(
				'rule' => array('notEmpty'),
				'message' => 'パスワードを入力してください。'
			),
			'alphaNumeric' => array(
				'rule' => array('custom', '/^[a-zA-Z0-9\-_]*$/'),
				'message' => '半角英数字とハイフン（-）、アンダーバー（_）で入力してください'
			),
			'between' => array(
				'rule' => array('between', 5, 25),
				'message' => '５〜２５文字の範囲で入力してください'
			),
		),
	);
		
	public function beforeSave() {
		if (isset($this->data[$this->alias]['password'])) {
			$this->data[$this->alias]['password'] = AuthComponent::password($this->data[$this->alias]['password']);
		}
		return true;
	}

}
