<?php

namespace kemrash\vi\migrations;

class v_0_0_2 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['vi_version']) && version_compare($this->config['vi_version'], '0.0.2', '>=');
	}
	
	static public function depends_on()
	{
		return array('\kemrash\vi\migrations\v_0_0_1');
	}

	public function update_data()
	{
		return array(
			array('config.update', array('vi_version', '0.0.2')),
			array('config.add', array('vi_cron_last_go', 0)),
			array('config.add', array('vi_cron_go', (60 * 60 * 24 * 90))),
		);
	}
}