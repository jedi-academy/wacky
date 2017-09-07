<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Master control program :-/
 */
class Mcp extends Application {

	// default entry point
	public function index()
	{
		echo 'Go away.';
	}

	// restart everything
	function bankrupt($token = 'bogus')
	{
		// make sure this request is authorized
		$required = $this->properties->get('potd');
		if ($token != $required)
			redirect('/');

		// establish zulu time
		$zulu = date('Y-m-d H:i:s.');
		$this->properties->put('zulu', $zulu);

		// reset stats
		$records = $this->stats->all();
		foreach ($records as $record)
		{
			$record->balance = $this->properties->get('ante');
			$record->boxes_bought = 0;
			$record->parts_returned = 0;
			$record->parts_made = 0;
			$record->bots_built = 0;
			$record->last_made = $zulu;
			$this->stats->update($record);
		}
		// clear history, parts
		$this->parts->truncate();
		$this->history->truncate();
		$this->activity->truncate();
		$this->boblog->truncate();
		$this->trading->truncate();

		// and finally the sessions
		$this->db->truncate('ci_sessions');
		redirect('/');
	}

}
