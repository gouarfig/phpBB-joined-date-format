<?php
/**
*
* @package joindateformat
* @copyright (c) 2015 Fred Quointeau
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace fq\joindateformat\migrations;

class default_config extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['joindateformat_version']) && version_compare($this->config['joindateformat_version'], '3.1.1', '>=');
	}

	static public function depends_on()
	{
		return array('\phpbb\db\migration\data\v310\dev');
	}

	public function update_data()
	{
		return array(
			array('config.add', array('joindateformat_version', '1.0.0')),
			array('config.add', array('joindateformat_topicdateformat', '|d M Y|')),
		);
	}
}
