<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends Application {

	// main dashboard
	public function index()
	{
		$this->data['pagebody'] = 'homepage';

		// plants gauge
		$total_plants = $this->factories->size();
		$active_plants = 0;
		foreach ($this->factories->all() as $record)
			if ($record->org != null)
				$active_plants++;
		$this->data['plants'] = $this->metric('Active Plants', $active_plants, 'primary', 'industry', '/plants', $total_plants . ' plants licensed');

		// parts gauge
		$total_certs = $this->parts->size();
		$making_plants = 0;
		foreach ($this->stats->all() as $record)
			if (!empty($record->making))
				$making_plants++;
		$this->data['parts'] = $this->metric('Certified parts in stock', $total_certs, 'success', 'cubes', '/production/parts', $making_plants . ' factories involved');

		// builds gauge
		$this->data['builds'] = $this->metric('Bots built', $this->boblog->size(), 'warning', 'child', '/production/bots');

		// greed gauge
		$this->data['bucks'] = $this->metric('Greed meter', $this->stats->profit(), 'danger', 'dollar', '/production/greed');

		// bots breakdown donut chart
		$this->caboose->needed('morris', 'morris-donut-chart');
		$parms = ['donuts' => $this->boblog->breakout(), 'field' => 'morris-donut-chart'];
		$this->data['donutchart'] = $this->parser->parse('donutchart', $parms, true);
		$this->data['zzz'] = $this->parser->parse('_components/morris-data', $parms, true);

		// tasks activity panel
		$parms['activities'] = $this->activity->latest();
		$this->data['tasks'] = $this->parser->parse('tasks', $parms, true);

		// transactions history panel
		$parms['details'] = $this->history->latest();
		$this->data['transactions'] = $this->parser->parse('transactions', $parms, true);

		$this->render();
	}

	function metric($text, $value, $panel, $icon, $link, $subtitle = 'View Details')
	{
		$parms = ['text' => $text, 'value' => $value, 'panel' => $panel,
			'icon' => $icon, 'link' => $link, 'subtitle' => $subtitle];
		return $this->parser->parse('theme/_metric', $parms, true);
	}

}
