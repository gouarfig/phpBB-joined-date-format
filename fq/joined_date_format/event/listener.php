<?php
/**
*
* Joined Date Format
*
* @version 1.0.0
* @copyright (c) 2015 Fred Quointeau
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/


namespace fq\joined_date_format\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class listener implements EventSubscriberInterface
{
	protected $config = null;
	protected $user = null;

	static public function getSubscribedEvents()
	{
		return array(
			'core.viewtopic_cache_user_data'	=> 'viewtopic_change_joined_date_format',
			'core.acp_board_config_edit_add'	=> 'config_edit_add',
		);
	}

	/**
	 * Constructor
	 *
	 * @param \phpbb\config\config $config
	 * @param \phpbb\user $user
	 */
	public function __construct(\phpbb\config\config $config, \phpbb\user $user)
	{
		$this->config = $config;
		$this->user = $user;
	}

	/**
	 * Change the format of the joined date
	 *
	 * @param \phpbb\event\data $event
	 */
	public function viewtopic_change_joined_date_format(\phpbb\event\data $event)
	{
		$dateformat = $this->config['joindateformat_topicdateformat'] ? $this->config['joindateformat_topicdateformat'] : false;

		$user_cache_data = $event['user_cache_data'];
		$row = $event['row'];

		$user_cache_data['joined'] = $this->user->format_date($row['user_regdate'], $dateformat);

		$event['user_cache_data'] = $user_cache_data;
	}

	/**
	 * Add the date format configuration to the "Board settings"
	 *
	 * @param \phpbb\event\data $event
	 */
	public function config_edit_add($event)
	{
		// Add a config to the settings mode, after board_timezone
		if ($event['mode'] == 'settings' && isset($event['display_vars']['vars']['board_timezone']))
		{
			// Load language file
			$this->user->add_lang_ext('fq/joined_date_format', 'joined_date_format_acp');

			// Store display_vars event in a local variable
			$display_vars = $event['display_vars'];

			// Define the new config vars
			$ga_config_vars = array(
				'joindateformat_topicdateformat' => array(
					'lang' => 'ACP_TOPICJOINDATEFORMAT_ID',
					'validate' => 'string',
					'type' => 'custom',
					'method' => 'dateformat_select',
					'explain' => true,
				),
			);

			// Add the new config vars after board_timezone in the display_vars config array
			$insert_after = array('after' => 'board_timezone');
			$display_vars['vars'] = phpbb_insert_config_array($display_vars['vars'], $ga_config_vars, $insert_after);

			// Update the display_vars event with the new array
			$event['display_vars'] = $display_vars;
		}
	}
}
