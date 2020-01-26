<?php

namespace kemrash\vi\migrations;

class v_0_0_1 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['vi_version']) && version_compare($this->config['vi_version'], '0.0.1', '>=');
	}
	
	public static function depends_on()
	{
		return array('\phpbb\db\migration\data\v320\v320');
	}

	public function update_data()
	{
		return array(
			array('config.add', array('vi_version', '0.0.1')),
			array('permission.add', array('u_kemrash_vi')),
		);
	}
}