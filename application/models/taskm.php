<?php

class TaskM extends MY_Model {

	function __construct() {
		$this->table = 'tasks';
		$this->id_field = 'id';

		parent::__construct();
	}




}
