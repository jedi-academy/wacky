<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Webhook endpoint to trigger deployment
 */
class Secret extends CI_Controller {

	public function index()
	{
		// generate a token
		$token = random_int(1000000,5000000);
		echo 'Token: '.$token.'<br/>';
		// compute its hex representation
		$hex = dechex($token);
		echo 'Hex: '.$hex.'<br/>';
		// and then its password hash
		$hash = password_hash($hex,PASSWORD_DEFAULT);
		echo 'Hash: '.$hash.'<br/>';		
	}

}
