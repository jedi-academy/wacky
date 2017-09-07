<?php

class Stats extends MY_Model {

	// constructor
	function __construct()
	{
		parent::__construct('stats', 'id');
	}

	// determine overall profit of the corp
	function profit()
	{
		$total = 0;
		$ante = $this->properties->get('ante');
		foreach ($this->all() as $record)
			if (!empty($record->making))
				$total += $record->balance - $ante;

		return $total;
	}

}
