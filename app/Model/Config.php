<?php

class Config extends AppModel {

	public function getByKey($key){
		$result = $this->find('first', array(
				'fields' => array('value'),
				'conditions' => array(
					'key' => $key,
					'user_id' => NULL,
					'keyword_id' => NULL,
				),
			));
		return $result['Config']['value'];
	}

}
?>