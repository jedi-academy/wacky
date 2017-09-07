<?php

class History extends MY_Model {

	// some limits
	private $history_limit = 100; // how many to retain
	private $history_page = 8; // how many to return for activity panel

	// constructor
	function __construct()
	{
		parent::__construct('history', 'seq');
	}

	// record a transaction
	function record($factory, $action, $quantity=0, $amount=0)
	{
		$record = $this->create();
		$record->plant = $factory;
		$record->action = $action;
		$record->quantity = $quantity;
		$record->amount = $amount;
		$record->stamp = date('Y-m-d H:i:s.');
		$this->add($record);
	}

		// construct view parameters for history panel
	function latest()
	{
		$records = $this->tail($this->history_page);
		$result = [];
		foreach ($records as $record)
			array_unshift($result, ['factory' => $record->plant, 
				'action' => $record->action,
				'amount' => $record->amount,
				'timestamp' => time_ago($record->stamp)]);
		return $result;
	}

}
