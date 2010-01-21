<?php
/**
 * Sumloader
 *
 * @author		ShiverCube
 * @copyright	Copyright (c) 2009, ShiverCube
 * @license		http://shivercube.com/sumloader/license/
 * @link		http://shivercube.com/sumloader/
 */

/**
 * Represents a static inferface to retreive common browser attributes
 */
class Browser
{
	/**
	 * Return the encoding type supported by the browser
	 *
	 * @return "gzip", "deflate", or "none"
	 */
	public static function EncodingType()
	{
		return isset($_SERVER['HTTP_ACCEPT_ENCODING']) ? (strstr($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') ?
			'gzip' : (strstr($_SERVER['HTTP_ACCEPT_ENCODING'], 'deflate') ? 'deflate' : 'none')) : 'none';
	}

	/**
	 * Return the whether the browser has the latest version with the given last modified date
	 *
	 * @param $lastModified non empty string
	 * @return bool
	 */
	public static function HasLatestVersion($lastModified)
	{
		return (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && $_SERVER['HTTP_IF_MODIFIED_SINCE'] == $lastModified);
	}
}

/* End of file browser.php */
/* Location: ./sumloader/classes/browser.php */