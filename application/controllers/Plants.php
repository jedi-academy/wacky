<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Plants extends Application {

	private $items_per_page = 10;
	private $theplants = null;
	private $thestats = null;

	// constructor
	public function __construct()
	{
		parent::__construct();
		$this->theplants = $this->factories->all();
		$this->thestats = $this->stats->all();
	}

	public function index()
	{
		$this->grid();
	}

	// Show a single page of todo items
	private function show_page($extract)
	{
		$this->data['pagetitle'] = 'Plant list';

		// build the task presentation output
		$result = ''; // start with an empty array		
		foreach ($extract as $record)
		{
			if (!empty($record->status))
				$task->status = $this->statuses->get($record->status)->name;
//			$record->deployed = date_format($record->deployed,DATE_W3C);
			$stat = $this->stats->get($record->id) ?? $this->stats->create();
			$parms = array_merge((array) $record, (array) $stat);
			$result .= $this->parser->parse('plantone', $parms, true);
		}
		$this->data['display_set'] = $result;

		// and then pass them on
		$this->data['pagebody'] = 'plantlist';
		$this->render();
	}

	// Extract & handle a page of items, defaulting to the beginning
	function page($num = 1)
	{
		$extract = array(); // start with an empty extract
		// use a foreach loop, because the record indices may not be sequential
		$index = 0; // where are we in the tasks list
		$count = 0; // how many items have we added to the extract
		$start = ($num - 1) * $this->items_per_page;
		foreach ($this->theplants as $record)
		{
			if ($index++ >= $start)
			{
				$extract[] = $record;
				$count++;
			}
			if ($count >= $this->items_per_page)
				break;
		}

		$this->data['pagination'] = $this->pagenav($num);
		$this->show_page($extract);
	}

	// Build the pagination navbar
	private function pagenav($num)
	{
		$lastpage = ceil($this->factories->size() / $this->items_per_page);
		$parms = array(
			'first' => 1,
			'previous' => (max($num - 1, 1)),
			'next' => min($num + 1, $lastpage),
			'last' => $lastpage,
			'base' => 'plants'
		);
		return $this->parser->parse('plantnav', $parms, true);
	}

	// take a closer look at one plant
	function inspect($which = null)
	{

		$this->data['previous_view'] = $_SERVER['HTTP_REFERER'];

		$record = null;
		if ($which != null)
			$record = $this->factories->get($which);
		if ($record == null)
		{
			$this->page(1);
			return;
		}

		// avatar
		$frag = empty($record->org) ? 'planticon' : 'plantavatar';
		$record->thumbnail = $this->parser->parse($frag, (array) $record, true);

		// performance
		$performance = $this->stats->get($which) ?? $this->stats->create();
		$this->data = array_merge($this->data, (array) $performance);

		// and away we go
		$this->data = array_merge($this->data, (array) $record);
		$this->data['pagebody'] = 'plant_inspect';
		$this->render();
	}

	// present all the plants in a grid
	function grid()
	{
		$this->data['pagetitle'] = 'Plant Grid';

		// calculate contribution points
		$total = 0;
		foreach ($this->thestats as $record)
		{
			$total += $record->bots_built;
		}
		if ($total == 0)
			$total = 1;
		$average = $total / $this->factories->size();
		$star = $this->parser->parse('plantstar', $this->data, true);

		// build the task presentation output
		$result = ''; // start with an empty array		
		foreach ($this->theplants as $record)
		{
			if (!empty($record->status))
				$task->status = $this->statuses->get($record->status)->name;
			$stat = $this->stats->get($record->id);
			$record->last_made = $stat->last_made ?? 'Unknown';

			// determine the avatar to use
			$frag = empty($record->org) ? 'planticon' : 'plantavatar';
			$record->thumbnail = $this->parser->parse($frag, (array) $record, true);

			// figure out the stars
			$stars = '';
			if ($stat != null)
			{
				if ($stat->bots_built > ($average * 0.25))
					$stars .= $star;
				if ($stat->bots_built > ($average * 0.50))
					$stars .= $star;
				if ($stat->bots_built > ($average * 0.75))
					$stars .= $star;
			}
			$record->stars = $stars;

			$result .= $this->parser->parse('plantcell', (array) $record, true);
		}
		$this->data['display_set'] = $result;

		// and then pass them on
		$this->data['pagebody'] = 'plantgrid';
		$this->render();
	}

}
