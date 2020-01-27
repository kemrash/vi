<?php
namespace kemrash\vi\cron\task;

/**
 * cron task.
 */
class vi extends \phpbb\cron\task\base
{
	/**
	 * How often we run the cron (in seconds).
	 * @var int
	 */
	protected $config;
	protected $phpbb_root_path;
	/**
	 * Constructor
	 *
	 * @param \phpbb\config\config $config Config object
	 */
	public function __construct(\phpbb\config\config $config, $phpbb_root_path)
	{
		$this->config = $config;
		$this->phpbb_root_path = $phpbb_root_path;
	}

	/**
	 * Runs this cron task.
	 *
	 * @return void
	 */
	public function run()
	{		
		// Run your cron actions here...
		$dir = $this->phpbb_root_path . 'ext/kemrash/vi/styles/all/theme/images';
		if ($this->config->offsetGet('vi_gd_status') == 1 and file_exists($dir) and is_writable($dir))
		{
			foreach (glob($dir . '/*') as $file)
			{
				if ($file != $dir . '/index.html') unlink($file); 
			}
		}

		// Update the cron task run time here if it hasn't
		// already been done by your cron actions.
		$this->config->set('vi_cron_last_go', time(), true);
	}

	/**
	 * Returns whether this cron task should run now, because enough time
	 * has passed since it was last run.
	 *
	 * @return bool
	 */
	public function should_run()
	{
		return $this->config['vi_cron_last_go'] < time() - $this->config['vi_cron_go'];
	}
}
