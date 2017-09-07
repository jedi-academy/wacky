<?php

/**
 * Bot factory game engine
 */
class Engine {

	protected $init = FALSE;

	function __construct()
	{
		$this->CI = &get_instance();
	}

	// constructor
	private function initialize()
	{
		$this->pool = array();

		// build a list of bots produced
		$this->CI->load->helper('directory');
		$bots = directory_map('./images/bots');

		// isolate model identifier
		$found = array();
		foreach ($bots as $botname)
		{
			$found[] = substr($botname, 0, 1);
		}

		// make appropriately scaled pool to pick bots from
		foreach ($this->CI->series->all() as $series)
		{
			$starts = $series->starts;
			$ends = $series->ends;
			$limit = $series->frequency;
			$pos = 0;
			$i = 0;
			while ($i < $limit)
			{
				$candidate = $found[$pos];
				if ($candidate >= $starts)
					if ($candidate <= $ends)
					{
						$this->pool[] = $candidate;
						$i++;
					}
				$pos++;
				if ($pos >= count($found))
					$pos = 0;
			}
		}

		$this->init = TRUE;
	}

	// Make a box of parts
	function fillabox($factory)
	{
		$result = array();
		if (!$this->init)
			$this->initialize();

		for ($i = 0; $i < 10; $i++)
		{
			$result[] = $this->buildapart($factory);
		}

		return $result;
	}

	// Fulfill entitlement
	function fulfill($factory)
	{
		$timePerPart = 10;
		$result = array();
		if (!$this->init)
			$this->initialize();

		// how many are they entitled to?
		$stat = $this->CI->stats->get($factory);
		$then = $stat->last_made;
		$now = new DateTime;
		$ago = new DateTime($then);
		$diff = $now->diff($ago);

		$elapsed = $diff->s + (60 * $diff->i);
		$eligible = min(floor($elapsed / $timePerPart), 10);

		for ($i = 0; $i < $eligible; $i++)
		{
			$result[] = $this->buildapart($factory, $stat->making);
		}

		return $result;
	}

	// Calculate entitlement
	function eligible($factory)
	{
		$timePerPart = 10;

		// how many are they entitled to?
		$stat = $this->CI->stats->get($factory);
		$then = $stat->last_made;
		$now = new DateTime;
		$ago = new DateTime($then);
		$diff = $now->diff($ago);

		$elapsed = $diff->s + (60 * $diff->i);
		$eligible = min(floor($elapsed / $timePerPart), 10);

		return $eligible;
	}

	// Make a new part
	private function buildapart($factory = 'Bogus', $making = NULL)
	{
		$pieces = [1, 2, 3];
		$part = $this->CI->parts->create();
		//fixme check for duplicates
		$part->id = $this->randomToken();
		$part->plant = $factory;
		if ($making == NULL)
		{
			$part->model = $this->pool[array_rand($this->pool)];
			$part->piece = $pieces[array_rand($pieces)];
		} else
		{
			$part->model = substr($making, 0, 1);
			$part->piece = substr($making, 1, 1);
		}
		$part->stamp = date('Y-m-d H:i:s.');
		$this->CI->parts->add($part);
		return $part;
	}

	// Recycle a part
	function recycle($factory, $part)
	{
		$refund = 0;
		$record = $this->CI->parts->get($part);
		if (!empty($record) && ($record->plant == $factory))
		{
			$this->CI->parts->delete($part);
			$refund = 5;
		}

		return $refund;
	}

	// Choose a part for a factory to make
	function pickapart()
	{
		if (!$this->init)
			$this->initialize();
		$pieces = [1, 2, 3];
		return $this->pool[array_rand($this->pool)] . $pieces[array_rand($pieces)];
	}

	// come up with a random token
	function randomToken()
	{
		$token = random_int(1000000, 5000000);
		// compute its hex representation
		$hex = dechex($token);
		return $hex;
	}

	// Buy a bot
	function buymybot($part1, $part2, $part3)
	{
		$credit = 0;

		// retrieve the pieces
		$piece1 = $this->CI->parts->get($part1);
		$piece2 = $this->CI->parts->get($part2);
		$piece3 = $this->CI->parts->get($part3);

		$model1 = $piece1->model;
		$model2 = $piece2->model;
		$model3 = $piece3->model;

		$series1 = $this->checkSeries($model1);
		$series2 = $this->checkSeries($model2);
		$series3 = $this->checkSeries($model3);

		// data for logging
		$price = 25;
		$seriescode = 0;
		$bot = $piece1->model . $piece2->model . $piece3->model;
				
		// calculate the bot price
		if (($series1 == $series2) && ($series1 == $series3))
		{
			$seriescode = $series1;
			// they are in the same series
			$series = $this->CI->series->get($series1);
			if (($model1 == $model2) && ($model1 == $model3))
				$price = $series->value;
			else
				$price = $series->value / 2;
		}

		$this->CI->boblog->record($this->CI->trader, $bot, $seriescode, $price);
				
		// recycle the pieces
		$this->CI->parts->delete($part1);
		$this->CI->parts->delete($part2);
		$this->CI->parts->delete($part3);

		return $price;
	}

	// which series is a model from?
	private function checkSeries($model)
	{
		foreach ($this->CI->series->all() as $series)
		{
			if (($model >= $series->starts) && ($model <= $series->ends))
				return $series->code;
		}
		return 0;
	}

}
