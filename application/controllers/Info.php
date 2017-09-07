<?php

/**
 * Information service for PRC.
 * 
 * This controller provides access to all sorts of useful information
 * about goings on inside the PRC.
 * 
 * @package		Panda Research Center
 * @author		J.L. Parry
 * @link		https://umbrella.jlparry.com/help
 */
class Info extends CI_Controller {

	// constructor, for prep
	function __construct()
	{
		parent::__construct();
		$this->load->model('factories');
	}

	/**
	 *  Normal entry point ... should never get here
	 */
	public function index()
	{
		redirect('/');
	}

	/**
	 * Ask for the balance that a factory has
	 * 
	 * @param	string	$team	Factory (team) name
	 * @return	 "Ok AMOUNT" (where AMOUNT is the current balance for that factory) or an error message
	 */
	function balance($team = NULL)
	{
		if (empty($team))
		{
			echo 'Oops: no team specified.';
			return;
		}

		$factory = $this->factories->get($team);
		if (empty($factory))
		{
			echo 'Oops: invalid team name given.';
			return;
		}

		$stat = $this->stats->get($team);
		if (empty($stat) || empty($stat->making))
		{
			echo 'Oops: this team is not playing';
			return;
		}

		echo $stat->balance;
	}

	/**
	 * Get the scoop on a factory
	 * 
	 * @param	string	$team	Factory (team) name
	 * @return	  the public data known about a factory, or else an error message
	 */
	function scoop($team = NULL)
	{
		if (empty($team))
		{
			echo 'Oops: no team specified.';
			return;
		}

		$factory = $this->factories->get($team);
		if (empty($factory))
		{
			echo 'Oops: invalid team name given.';
			return;
		}

		$stat = $this->stats->get($team);
		if (empty($stat) || empty($stat->making))
		{
			echo 'Oops: this team is not playing';
			return;
		}

		echo json_encode($stat);
	}

	/**
	 * Identify a part
	 * 
	 * @param	string	$cacode	Certificate authentication code
	 * @return	  the data known about a part
	 */
	function verify($cacode = NULL)
	{
		if (empty($cacode))
		{
			echo 'Oops: no part CA code specified.';
			return;
		}
		
		$part = $this->parts->get($cacode);
		if (empty($part))
		{
			echo 'Oops: CA code not recognized.';
			return;
		}
	
		echo json_encode($part);
	}

	/**
	 * Identify the factories building a specific part
	 * 
	 * @param	string	$parttype	Two-letter part type (model and piece)
	 * @return	  A list of the factories making the designated part
	 */
	function whomakes($parttype = NULL)
	{
		if (empty($parttype))
		{
			echo 'Oops: no part type specified.';
			return;
		}

		$parttype = strtolower($parttype);

		$stats = $this->stats->all();
		$results = array();
		foreach ($stats as $stat)
			if (strtolower($stat->making) == $parttype)
				$results[] = $stat->id;

		if (empty($results))
		{
			echo 'Oops: no factory is making the specified part.';
		} else
			echo json_encode($results);
	}

	/**
	 * Test if you have a PRC session
	 * 
	 * @param	string	$key	Factory's API key (passed as query parameter)
	 * @return	  The factory name the PRC associates with your API key
	 */
	function whoami()
	{
		$key = $this->input->get('key');

		if (empty($key))
		{
			echo 'Oops: no API key specified.';
			return;
		}

		$trader = $this->trading->get($key);
		if (empty($trader))
		{
			echo 'Oops: invalid API key.';
			return;
		}

		echo $trader->factory;
	}

	/**
	 * Identify a factory's job
	 * 
	 * @param	string	$team	Factory (team) name
	 * @return	  The specific part that a factory is manufacturing during the current trading session, or an error message
	 */
	function job($team = NULL)
	{
		if (empty($team))
		{
			echo 'Oops: no team specified.';
			return;
		}

		$factory = $this->factories->get($team);
		if (empty($factory))
		{
			echo 'Oops: invalid team name given.';
			return;
		}

		$stat = $this->stats->get($team);
		if (empty($stat) || empty($stat->making))
		{
			echo 'Oops: this team is not playing';
			return;
		}

		echo $stat->making;
	}

	/**
	 * Identify a factory's job
	 * 
	 * @return	  A list of active factories (those with an API key), or else an error message
	 */
	function teams()
	{
		$teams = $this->trading->all();
		ksort($teams);
		foreach ($teams as $team)
		{
			$factory = $this->factories->get($team->factory);
			if (!empty($factory))
			{
				// assume my server deployment if they don't specify
				$website = (empty($factory->website)) ? 'https://' . $factory->id . '.jlparry.com' : $factory->website;
				$team_result = ['factory' => $team->factory, 'server' => $website];
				//$results[] = $team_result;
				$results[] = $team->factory;
			}
		}
		echo json_encode($results);
	}

}
