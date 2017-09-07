<?php

class Boblog extends MY_Model {

	// constructor
	function __construct()
	{
		parent::__construct('boblog', 'seq');
	}

	// record a new activity
	function record($factory, $bot, $series, $price)
	{

		// create & add new log record
		$record = $this->create();
		$record->plant = $factory;
		$record->bot = $bot;
		$record->series = $series;
		$record->price = $price;
		$record->timestamp = date('Y-m-d H:i:s.');
		$this->add($record);
	}

	// construct view parameters for bot build panel
	function latest()
	{
		$records = $this->tail(10);
		$result = [];
		foreach ($records as $record)
			array_unshift($result, ['factory' => $record->plant, 'bot' => $record->bot,
				'price' => $record->price, 'series' => $record->series,
				'delta' => time_ago($record->stamp)]);
		return $result;
	}

	// provide a breakout for the donut chart
	function breakout()
	{
		$count = array();
		// basic breakout
		foreach ($this->all() as $record)
		{
			if (empty($count[$record->series]))
			{
				$count[$record->series] = 1;
			} else
				$count[$record->series] ++;
		}

		// and summarize for presentation
		$result = array();
		foreach ($this->series->all() as $record)
		{
			$index = $record->code;
			$result[] = ['label' => $this->series->get($index)->description, 'value' => $count[$index] ?? 0];
		}
		return $result;
	}

}
