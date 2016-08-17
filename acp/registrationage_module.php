<?php
/**
*
* @package Registration Age Check
* @copyright (c) 2016 david63
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace david63\registrationage\acp;

class registrationage_module
{
	public $u_action;

	function main($id, $mode)
	{
		global $phpbb_container, $user;

		$this->tpl_name		= 'registrationage';
		$this->page_title	= $user->lang('REGISTRATION_AGE');

		// Get an instance of the admin controller
		$admin_controller = $phpbb_container->get('david63.registrationage.admin.controller');

		$admin_controller->display_options();
	}
}
