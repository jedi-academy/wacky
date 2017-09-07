<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Vault extends Application {

	public function __construct()
	{
		parent::__construct();
//		$this->restrict([ROLE_PLANT,ROLE_ADMIN]);
		$this->data['pagetitle'] = 'Plant settings';
		$this->load->helper('formfields');
	}

	// show the login page
	public function index()
	{
		// if already logged in, proceed to setup
		if (!empty($this->data['userName']))
			$this->settings();
		else
		{
			$this->data['pagebody'] = 'vault';
			$this->render();
		}
	}

	// Validate a user login attempt
	public function login()
	{
		// verify username (group) & password
		$user = $this->input->post('group');
		if (empty($user))
			$this->alert('Group code needed', 'danger');

		$pw = $this->input->post('password');
		if (empty($pw))
			$this->alert('Access token needed', 'danger');

		// check user validitity
		$factory = NULL; // assume the worst
		if ($this->error_free)
		{
			$factory = $this->factories->get($user);
			if ($factory == NULL)
				$this->alert('Unrecognized group', 'danger');
		}

		// check the password, and its hash if needed
		if ($this->error_free)
		{
			$okok = FALSE;
			if (strlen($factory->token) < 8)
			{
				// original token still in place
				$okok = ($pw == $factory->token);
				$this->harden($factory);
			} else
			{
				$okok = password_verify($pw, $factory->token);
			}
			if (!$okok)
				$this->alert('Invalid token', 'danger');
		}

		// if anything went wrong, redisplay the page
		if (!empty($this->data['alerts']))
		{
			$this->data['pagebody'] = 'vault';
			$this->render();
		} else
		{
			// otherwise, consider them good to go
			$this->session->set_userdata('factory', $factory);
			$this->session->set_userdata('userName', $user);
			$this->session->set_userdata('userID', $user);
			$this->session->set_userdata('userRole', ROLE_PLANT);
			redirect('/');
		}
	}

	// Convert plaintext token into hash, for better security
	private function harden($factory)
	{
		$factory->token = password_hash($factory->token, PASSWORD_DEFAULT);
		$this->factories->update($factory);
	}

	// present the settings maintenance form
	public function settings()
	{
		$factory = $this->session->userdata('factory');
		$fields = array(
			'forg' => makePrefixedField('Your Organization', 'org', $factory->org, 'https://github.com/', "Name of your team's organization on Github."),
			'frepo' => makePrefixedField('Your Repository', 'repo', $factory->repo, 'https://github.com/.../', "Name of your team repository, inside the above organization."),
			'fbranch' => makeRadioButtons('Branch to Deploy', 'branch', $factory->branch, ['master' => 'master', 'develop' => 'develop'], "Which branch would you like automatically deployed for testing?"),
			'fwebsite' => makePrefixedField('External website?', 'website', $factory->website, 'https://', "URL for your team website, if not hosted on Umbrella's server. " .
					"This is important for the final assignment, when other plants will want to buy or sell parts with you."),
			'zsubmit' => makeSubmitButton('Update my settings', "Click on home or <back> if you don't want to change anything!", 'btn-success'),
		);
		$this->data = array_merge($this->data, $fields);

		$this->data['pagebody'] = 'settings';
		$this->render();
	}

	// handle a settings update request
	public function update()
	{
		// setup for validation
		$this->load->library('form_validation');
		$this->form_validation->set_rules($this->factories->rules());

		// retrieve & update data transfer buffer
		$factory = (array) $this->session->userdata('factory');
		$factory = array_merge($factory, $this->input->post());
		$this->session->set_userdata('factory', (object) $factory);

		// validate away
		if ($this->form_validation->run())
		{
			$factory->updated = date('Y-m-d H:i:s.');
			$this->factories->update($factory);
			$this->alert('Settings updated', 'success');
		} else
		{
			$this->alert('<strong>Validation errors!<strong><br>' . validation_errors(), 'danger');
		}
		$this->settings();
	}

}
