<?php
/**
*
* @package Registration Age Check
* @copyright (c) 2016 david63
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace david63\registrationage;

use phpbb\extension\base;

class ext extends base
{
	const REGISTRATION_AGE_VERSION = '2.1.0 RC1';
	const CENTURY = 100;

	/**
	* Enable extension if phpBB version requirement is met
	*
	* @var string Require 3.2.0-a1 due to updated 3.2 syntax
	*
	* @return bool
	* @access public
	*/
	public function is_enableable()
	{
		$is_enableable = phpbb_version_compare(PHPBB_VERSION, '3.2.0', '>=');

		if (!$is_enableable)
		{
			$this->container->get('language')->add_lang('ext_registrationage', 'david63/registrationage');
			trigger_error($this->container->get('language')->lang('VERSION_32') . adm_back_link(append_sid('index.' . $this->container->getParameter('core.php_ext'), 'i=acp_extensions&amp;mode=main')), E_USER_WARNING);
		}

		return $is_enableable;
	}
}
