<?php

namespace kemrash\vi\migrations;

class v_0_0_3 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['vi_version']) && version_compare($this->config['vi_version'], '0.0.3', '>=');
	}
	
	static public function depends_on()
	{
		return array('\kemrash\vi\migrations\v_0_0_2');
	}

	public function update_data()
	{
		return array(
			array('config.update', array('vi_version', '0.0.3')),
			array('config.add', array('vi_gd_status', 0)),
		);
	}
}