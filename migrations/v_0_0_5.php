<?php

namespace kemrash\vi\migrations;

class v_0_0_5 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['vi_version']) && version_compare($this->config['vi_version'], '0.0.5', '>=');
	}
	
	static public function depends_on()
	{
		return array('\kemrash\vi\migrations\v_0_0_4');
	}

	public function update_data()
	{
		return array(
			array('config.update', array('vi_version', '0.0.5')),
			array('config.update', array('vi_gd_status', 0)),
		);
	}
}