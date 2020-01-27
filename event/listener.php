<?php
namespace kemrash\vi\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
* Event listener
*/
class listener implements EventSubscriberInterface
{
	static public function getSubscribedEvents()
	{
		return array(
			'core.viewonline_overwrite_location' => array('kem_vi', -2),
			'core.permissions' => 'add_permissions',
		);
	}

	protected $auth;
	protected $config;
	protected $db;
	protected $phpbb_root_path;

	/**
	* Constructor
	*/
	public function __construct(\phpbb\auth\auth $auth, \phpbb\config\config $config, \phpbb\db\driver\driver_interface $db, $phpbb_root_path)
	{
		$this->auth = $auth;
		$this->config = $config;
		$this->db = $db;
		$this->phpbb_root_path = $phpbb_root_path;
	}
	
	public function kem_vi($event)
	{
		$location = $event['location'];
		$row = $event['row'];
		$dir = $this->phpbb_root_path . 'ext/kemrash/vi/styles/all/theme/images';
		if ($this->config->offsetGet('vi_gd_status') == 0)
		{
			if (function_exists('ImageCreateFromJpeg') and function_exists('ImageCreateFromPng') and chmod($dir, 0777))
			{
				$this->config->set('vi_gd_status', 1);
			}
			else
			{
				$this->config->set('vi_gd_status', 2);
			}
		}
		if (preg_match('/^download\/file\.php\?id=/', $row['session_page']) and $this->auth->acl_get('u_kemrash_vi'))
		{
			$clear_id = preg_replace('/(^.*?id=|\D.*)/', '', $row['session_page']);
			$sql = 'SELECT mimetype, physical_filename FROM ' . ATTACHMENTS_TABLE . '
			WHERE attach_id = ' . $clear_id;
			$result = $this->db->sql_query($sql);
			if ($mimetype = $this->db->sql_fetchrow($result) and preg_match('/^image\//', $mimetype['mimetype']))
			{
				if ($this->config->offsetGet('vi_gd_status') == 1 and ($mimetype['mimetype'] == 'image/jpeg' or $mimetype['mimetype'] == 'image/jpg' or $mimetype['mimetype'] == 'image/png') and $mimetype['physical_filename'] != NULL)
				{
					$img_url = 'ext/kemrash/vi/styles/all/theme/images/' . $clear_id;
					$img_url_origen = $this->phpbb_root_path . 'files/' . $mimetype['physical_filename'];
					$img_save_path = $dir . '/' . $clear_id;
					if (file_exists($img_url . '.jpg')) $location =  $location . ' <img src="' . $img_url . '.jpg">';
					elseif (file_exists($img_url . '.png')) $location =  $location . ' <img src="' . $img_url . '.png">';
					elseif (is_writable($dir) and !file_exists($img_url . '.jpg') and ($mimetype['mimetype'] == 'image/jpeg' or $mimetype['mimetype'] == 'image/jpg'))
					{
						$img = @imagecreatefromjpeg($img_url_origen);
						if ($img)
						{
							$img = imagescale($img, 100);
							imagejpeg($img, $img_save_path . '.jpg');
							imagedestroy($img);
							$location =  $location . ' <img src="' . $img_url . '.jpg">';
						}
						else
						{
							$location =  $location . ' <img src="download/file.php?id=' . $clear_id . '&mode=view" style=" max-width: 100px; max-height: 120px; ">';
						}
					}
					elseif (is_writable($dir) and !file_exists($img_url . '.png') and $mimetype['mimetype'] == 'image/png')
					{
						$img = @imagecreatefrompng($img_url_origen);
						if ($img)
						{
							$img = imagescale($img, 100);
							imagealphablending($img, false);
							imagesavealpha($img, true);
							imagepng($img, $img_save_path . '.png');
							imagedestroy($img);
							$location =  $location . ' <img src="' . $img_url . '.png">';
						}
						else
						{
							$location =  $location . ' <img src="download/file.php?id=' . $clear_id . '&mode=view" style=" max-width: 100px; max-height: 120px; ">';
						}
					}
					elseif (!is_writable($dir))
					{
						$this->config->set('vi_gd_status', 2);
						$location =  $location . ' <img src="download/file.php?id=' . $clear_id . '&mode=view" style=" max-width: 100px; max-height: 120px; ">';
					}
				}
				else
				{
					$location =  $location . ' <img src="download/file.php?id=' . $clear_id . '&mode=view" style=" max-width: 100px; max-height: 120px; ">';
				}
			}
		}
		$event['location'] = $location;
	}
	
	public function add_permissions($event)
	{
		$permissions = $event['permissions'];
		$permissions['u_kemrash_vi'] = array('lang' => 'ACL_U_KEMRASH_VI', 'cat' => 'misc');
		$event['permissions'] = $permissions;
	}
}
