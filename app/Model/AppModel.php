<?php
/**
 * Application model for Cake.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
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
 * @package       app.Model
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Model', 'Model');

/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       app.Model
 */
class AppModel extends Model {

	/* トランザクション */
	public function begin() {
		return $this->getDataSource()->begin();
	}
	public function commit() {
		return $this->getDataSource()->commit();
	}
	public function rollback() {
		return $this->getDataSource()->rollback();
	}
	
	/* Datetime型の文字列を生成 */
	protected function getDatetime($time = null){
		if($time){
			return date("Y-m-d H:i:s", $time);
		}else{
			return date("Y-m-d H:i:s");
		}
	}

	/* プリミティブでない値のエンコード・デコード関数 */
	protected function encodeValue($data){
		return base64_encode(gzdeflate(serialize($data), 9));
	}
	protected function decodeValue($data){
		return unserialize(gzinflate(base64_decode($data)));
	}
}
