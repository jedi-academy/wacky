<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends Application {

	// main dashboard
	public function index()
	{
		$this->data['pagebody'] = 'homepage';

		// airlines gauge
		$total_airlines = $this->teams->size();
		$active_airlines = 0;
		foreach ($this->teams->all() as $record)
			if ($record->org != null)
				$active_airlines++;
		$this->data['airlines'] = $this->metric('Active Airlines', $active_airlines, 'primary', 'plane', '/members', $total_airlines . ' airlines licensed');

		// airports gauge
		$total_airports = $this->airports->size();
		$active_airports = 0;
//		foreach ($this->stats->all() as $record)
//			if (!empty($record->making))
//				$active_airlines++;
		$this->data['airports'] = $this->metric('Active Airports', $active_airports, 'success', 'send', '/#', $total_airports . ' airports operational');

		// flights gauge
		$this->data['flights'] = $this->metric('Flights scheduled', 0, 'warning', 'suitcase', '/#');

		// greed gauge
		$this->data['bucks'] = $this->metric('Greed meter', 0, 'danger', 'dollar', '/#');

//		// bots breakdown donut chart
//		$this->caboose->needed('morris', 'morris-donut-chart');
//		$parms = ['donuts' => $this->boblog->breakout(), 'field' => 'morris-donut-chart'];
//		$this->data['donutchart'] = $this->parser->parse('donutchart', $parms, true);
//		$this->data['zzz'] = $this->parser->parse('_components/morris-data', $parms, true);
//
//		// tasks activity panel
//		$parms['activities'] = $this->activity->latest();
//		$this->data['tasks'] = $this->parser->parse('tasks', $parms, true);
//
//		// transactions history panel
//		$parms['details'] = $this->history->latest();
//		$this->data['transactions'] = $this->parser->parse('transactions', $parms, true);

		$this->render();
	}

	function metric($text, $value, $panel, $icon, $link, $subtitle = 'View Details')
	{
		$parms = ['text' => $text, 'value' => $value, 'panel' => $panel,
			'icon' => $icon, 'link' => $link, 'subtitle' => $subtitle];
		return $this->parser->parse('theme/_metric', $parms, true);
	}

}
