<?php

/**
 * Work service for factory apps.
 * 
 * This controller provides a number of services that a factory
 * app can request.
 * 
 * @package		Panda Research Center
 * @author		J.L. Parry
 * @link		https://umbrella.jlparry.com/help
 */
class Work extends CI_Controller {

	// constructor
	function __construct()
	{
		parent::__construct();
		$this->load->library('engine');

		// was a PRC session token provided?
		$this->trader = NULL;
		$this->token = $this->input->get('key');
		if (!empty($this->token))
		{
			$prc_session = $this->trading->get($this->token);
			$this->trader = empty($prc_session) ? 'Bogus' : $prc_session->factory;
		}
	}

	/**
	 *  Normal entry point ... should never get here
	 */
	public function index()
	{
		redirect('/');
	}

	/**
	 * Register a new trading session for a factory
	 * 
	 * @param	string	$team	Factory (team) name
	 * @param   string  $password Super-secret access token for the factory
	 * @return	The API key
	 */
	function registerme($team = NULL, $password = NULL)
	{
		$oops = '';

		// verify username (group) & password presence
		if (empty($team))
			$oops .= 'Team needed! ';
		if (empty($password))
			$oops .= 'Password needed! ';

		// check user validitity
		$factory = NULL; // assume the worst
		if (empty($oops))
		{
			$factory = $this->factories->get($team);
			if ($factory == NULL)
				$oops .= 'No such team! ';
		}

		// check the password, and its hash if needed
		if (empty($oops))
		{
			$okok = FALSE;
			if (strlen($factory->token) < 8)
			{
				// original token still in place
				$okok = ($password == $factory->token);
				$this->harden($factory);
			} else
			{
				$okok = password_verify($password, $factory->token);
			}
			if (!$okok)
				$oops .= 'Bad password! ';
		}

		// if anything went wrong, redisplay the page
		if (!empty($oops))
		{
			// ignore invalid credentials
			echo 'Oops: ' . $oops;
		} else
		{
			// otherwise, consider them good to go
			$apikey = $this->engine->randomToken();

			$oldTrader = $this->trading->some('factory', $team);
			if (!empty($oldTrader))
				foreach ($oldTrader as $record)
					$this->trading->delete($record->token);

			$trader = $this->trading->create();
			$trader->token = $apikey;
			$trader->factory = $team;
			$this->trading->add($trader);

			$oldStats = $this->stats->get($team);
			if (!empty($oldStats))
				$this->stats->delete($team);

			$stat = $this->stats->create();
			$stat->id = $team;
			$stat->balance = $this->properties->get('ante');
			$stat->making = $this->engine->pickapart();
			$stat->last_made = date('Y-m-d H:i:s.');
			$this->stats->add($stat);

			$this->activity->record($team, 'registered');
			echo 'Ok ' . $apikey;
		}
	}

	// Convert plaintext token into hash, for better security
	private function harden($factory)
	{
		$factory->token = password_hash($factory->token, PASSWORD_DEFAULT);
		$this->factories->update($factory);
	}

	/**
	 * Purchase a box of random parts for your factory to use
	 * 
	 * @param	string	$key	Factory's API key (passed as query parameter)
	 * @return	An array of parts certificates, JSON formatted
	 */
	function buybox()
	{
		// basic fact-checking
		if (empty($this->trader))
		{
			echo "Oops: I don't recognize you!";
			return;
		}

		// make sure they can afford it
		$cost = $this->properties->get('priceperpack');
		$stat = $this->stats->get($this->trader);
		if ($stat->balance < $cost)
		{
			echo "Oops: you can't afford that!";
			return;
		}

		// go get em
		$result = $this->engine->fillabox($this->trader);

		// charge them for it
		$stat->balance -= $cost;
		$stat->boxes_bought++;
		$this->stats->update($stat);

		$this->activity->record($this->trader, 'bought a box of parts');
		$this->history->record($this->trader,'Parts bought',10, -100);
		echo json_encode($result);
	}

	/**
	 * Requests any newly built parts for this factory
	 * 
	 * @param	string	$key	Factory's API key (passed as query parameter)
	 * @return	An array of parts certificates, JSON formatted
	 */
	function mybuilds()
	{
		// basic fact-checking
		if (empty($this->trader))
		{
			echo "Oops: I don't recognize you!";
			return;
		}

		// go get em
		$result = $this->engine->fulfill($this->trader);

		$stat = $this->stats->get($this->trader);
		$stat->last_made = date('Y-m-d H:i:s.');
		$stat->parts_made += count($result);
		$this->stats->update($stat);

		if (count($result) > 0)
			$this->activity->record($this->trader, 'built ' . count($result) . ' parts');

		echo json_encode($result);
	}

	/**
	 * Ask the PRC to recycle up to three parts that you do not want
	 * 
	 * @param	string	$part1	Certificate code for a part to return
	 * @param	string	$part2	Certificate code for a part to return
	 * @param	string	$part3	Certificate code for a part to return
	 * @param	string	$key	Factory's API key (passed as query parameter)
	 * @return	"Ok AMOUNT" (where AMOUNT is the value credited to your account balance) or an error message
	 */
	function recycle($part1 = NULL, $part2 = NULL, $part3 = NULL)
	{
		// basic fact-checking
		if (empty($this->trader))
		{
			echo "Oops: I don't recognize you!";
			return;
		}

		// let's do this
		$count = 0;
		$refund = 0;
		if (!empty($part1))
		{
			$result = $this->engine->recycle($this->trader, $part1);
			if ($result > 0)
			{
				$count++;
				$refund += $result;
			}
		}
		if (!empty($part2))
		{
			$result = $this->engine->recycle($this->trader, $part2);
			if ($result > 0)
			{
				$count++;
				$refund += $result;
			}
		}
		if (!empty($part3))
		{
			$result = $this->engine->recycle($this->trader, $part3);
			if ($result > 0)
			{
				$count++;
				$refund += $result;
			}
		}

		$stat = $this->stats->get($this->trader);
		$stat->last_made = date('Y-m-d H:i:s.');
		$stat->parts_returned += $count;
		$stat->balance += $refund;
		$this->stats->update($stat);

		if ($count > 0)
		{
			$this->activity->record($this->trader, 'returned ' . $count . ' parts');
			$this->history->record($this->trader, 'Parts recycled', $count, $refund);
		}
		echo 'Ok ' . $refund;
//		echo $this->engine->eligible($this->trader);
	}

	/**
	 * Ask the PRC to buy an assembled bot from you
	 * 
	 * @param	string	$part1	Certificate code for the "top" part of your bot
	 * @param	string	$part2	Certificate code for the "torso" part of your bot
	 * @param	string	$part3	Certificate code for the "bottom" part of your bot
	 * @param	string	$key	Factory's API key (passed as query parameter)
	 * @return	"Ok AMOUNT" (where AMOUNT is the value credited to your account balance) or an error message
	 */
	function buymybot($part1 = NULL, $part2 = NULL, $part3 = NULL)
	{
		// basic fact-checking
		if (empty($this->trader))
		{
			echo "Oops: I don't recognize you!";
			return;
		}

		// Did they provide a complete bot?
		if (empty($part1) || empty($part2) || empty($part3))
		{
			echo "Oops: you didn't provide a completed bot.";
			return;
		}

		// Check out the pieces
		$oops = '';
		$oops .= $this->checkit($part1, 1);
		$oops .= $this->checkit($part2, 2);
		$oops .= $this->checkit($part3, 3);

		if (!empty($oops))
		{
			echo "Oops: " . $oops;
			return;
		}

		// let's do this
		$result = $this->engine->buymybot($part1, $part2, $part3);

		$stat = $this->stats->get($this->trader);
		$stat->bots_built++;
		$stat->balance += $result;
		$this->stats->update($stat);

		$this->activity->record($this->trader, 'sold us a bot');
		$this->history->record($this->trader,'Bot sold',1, $result);

		echo 'Ok ' . $result;
	}

	// Vet a bot piece
	private function checkit($part, $piece)
	{
		$result = '';

		// get the part
		$record = $this->parts->get($part);
		if (empty($record))
			return "Hmmm - can't find " . $part . ' ';

		// does this piece belong to the factory claiming it?
		if ($record->plant != $this->trader)
			$result .= $part . ' not your part! ';

		// is it the right kind of piece?
		if ($record->piece != $piece)
			$result .= $part . ' not the right kind of piece! ';

		return $result;
	}

	/**
	 * Restart your bot factory's participation in the current trading session
	 * 
	 * @param	string	$key	Factory's API key (passed as query parameter)
	 * @return	"Ok AMOUNT" (where AMOUNT is the starting balance assigned to you) or an error message
	 */
	function rebootme()
	{
		// basic fact-checking
		if (empty($this->trader))
		{
			echo "Oops: I don't recognize you!";
			return;
		}

		$stat = $this->stats->get($this->trader);
		$stat->balance = $this->properties->get('ante');
		$this->stats->update($stat);

		$records = $this->parts->some('plant', $this->trader);
		if (!empty($records))
			foreach ($records as $record)
				$this->parts->delete($record->id);

		$this->activity->record($this->trader, 'rebooted.');
		echo "Ok";
	}

	/**
	 * Destroy your plants' PRC trading session
	 * 
	 * @param	string	$key	Factory's API key (passed as query parameter)
	 * @return	 "Ok" or an error message
	 */
	function goodbye()
	{
		// basic fact-checking
		if (empty($this->trader))
		{
			echo "Oops: I don't recognize you!";
			return;
		}

		$stat = $this->stats->get($this->trader);
		$stat->balance = $this->properties->get('ante');
		$this->stats->update($stat);

		$records = $this->parts->some('plant', $this->trader);
		if (!empty($records))
			foreach ($records as $record)
				$this->parts->delete($record->id);

		$this->trading->delete($this->token);

		$this->activity->record($this->trader, 'left the game.');
		echo "Ok";
	}

}
