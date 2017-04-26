<?php
/**
*
* @package Registration Age Check
* @copyright (c) 2016 david63
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace david63\registrationage\controller;

use Symfony\Component\DependencyInjection\ContainerInterface;
use phpbb\config\config;
use phpbb\request\request;
use phpbb\template\template;
use phpbb\user;
use phpbb\language\language;
use phpbb\log\log;
use david63\registrationage\ext;

/**
* Admin controller
*/
class admin_controller implements admin_interface
{
	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var phpbb\language\language */
	protected $language;

	/** @var \phpbb\log\log */
	protected $log;

	/** @var string Custom form action */
	protected $u_action;

	/**
	* Constructor for admin controller
	*
	* @param \phpbb\config\config		$config		Config object
	* @param \phpbb\request\request		$request	Request object
	* @param \phpbb\template\template	$template	Template object
	* @param \phpbb\user				$user		User object
	* @param phpbb\language\language	$language	Language object
	* @param \phpbb\log\log				$log		Log object
	*
	* @return \david63\registrationage\controller\admin_controller
	* @access public
	*/
	public function __construct(config $config, request $request, template $template, user $user, language $language, log $log)
	{
		$this->config		= $config;
		$this->request		= $request;
		$this->template		= $template;
		$this->user			= $user;
		$this->language		= $language;
		$this->log			= $log;
	}

	/**
	* Display the options a user can configure for this extension
	*
	* @return null
	* @access public
	*/
	public function display_options()
	{
		// Add the language file
		$this->language->add_lang('acp_registrationage', 'david63/registrationage');

		// Create a form key for preventing CSRF attacks
		$form_key = 'registrationage';
		add_form_key($form_key);

		// Is the form being submitted
		if ($this->request->is_set_post('submit'))
		{
			// Is the submitted form is valid
			if (!check_form_key($form_key))
			{
				trigger_error($this->language->lang('FORM_INVALID') . adm_back_link($this->u_action), E_USER_WARNING);
			}

			// If no errors, process the form data
			// Set the options the user configured
			$this->set_options();

			// Add option settings change action to the admin log
			$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'REGISTRATION_AGE_LOG');

			// Option settings have been updated and logged
			// Confirm this to the user and provide link back to previous page
			trigger_error($this->language->lang('CONFIG_UPDATED') . adm_back_link($this->u_action));
		}

		// Create the ban end select options
		$ban_end_options = '';
		foreach ($this->language->lang_raw('BAN_END_TEXT') as $key => $ban_opt)
		{
			$selected = ($this->config['registration_age_ban_length'] == $key) ? ' selected="selected"' : '';
			$ban_end_options .= '<option value="' . $key . '"' . $selected . '>' . $ban_opt . '</option>';
		}

		$ban_opts = '<select name="registration_age_ban_length" id="registration_age_ban_length">' . $ban_end_options . '</select>';

		// Set output vars for display in the template
		$this->template->assign_vars(array(
			'REGISTRATION_AGE'				=> isset($this->config['registration_age']) ? $this->config['registration_age'] : '',
			'REGISTRATION_AGE_BAN_LENGTH'	=> $ban_opts,
			'REGISTRATION_AGE_BAN_REASON'	=> isset($this->config['registration_age_ban_reason']) ? $this->config['registration_age_ban_reason'] : '',
			'REGISTRATION_AGE_COPY'			=> isset($this->config['registration_age_copy']) ? $this->config['registration_age_copy'] : '',
			'REGISTRATION_AGE_IP'			=> isset($this->config['registration_age_ip']) ? $this->config['registration_age_ip'] : '',
			'REGISTRATION_AGE_LOG'			=> isset($this->config['registration_age_log']) ? $this->config['registration_age_log'] : '',
			'REGISTRATION_AGE_STORE'		=> isset($this->config['registration_age_store']) ? $this->config['registration_age_store'] : '',
			'REGISTRATION_AGE_VERSION'		=> ext::REGISTRATION_AGE_VERSION,

			'U_ACTION'						=> $this->u_action,
		));
	}

	/**
	* Set the options a user can configure
	*
	* @return null
	* @access protected
	*/
	protected function set_options()
	{
		$this->config->set('registration_age', $this->request->variable('registration_age', 18));
		$this->config->set('registration_age_ban_length', $this->request->variable('registration_age_ban_length', 0));
		$this->config->set('registration_age_ban_reason', $this->request->variable('registration_age_ban_reason', ''));
		$this->config->set('registration_age_copy', $this->request->variable('registration_age_copy', 0));
		$this->config->set('registration_age_ip', $this->request->variable('registration_age_ip', 0));
		$this->config->set('registration_age_log', $this->request->variable('registration_age_log', 1));
		$this->config->set('registration_age_store', $this->request->variable('registration_age_store', 1));
	}
}
