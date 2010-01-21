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
 * Represents a cache folder. Provides ability to read and write to the cache
 */
class Cache  {
	/**
	 * @type Folder
	 */
	private $folder;

	/**
	 * Initializes the Cache instance
	 *
	 * @param $cacheDir string The directory of the cache
	 */
	public function Cache($cacheDir)
	{
		$this->folder = new Folder($cacheDir);
	}

	/**
	 * Return whether a file with the given id exists within the cache
	 *
	 * @param $id non empty string
	 * @return bool
	 */
	public function Exists($id)
	{
		return $this->folder->FileExists($id);
	}

	/**
	 * Get the file within the cache with the given id
	 *
	 * @pre A file with id $id exists within the cache
	 * @param $id non empty string
	 * @return string The contents of the cached file
	 */
	public function Get($id)
	{
		return $this->folder->GetFile($id);
	}

	/**
	 * Saves the given content to the cache with the given id. If the cached file already
	 * exists, it is overridden
	 *
	 * @param $id string non empty string
	 * @param $contents string The contents of the file
	 */
	public function Save($id, &$contents)
	{
		$fp = fopen($this->folder->GetLocation().$id, 'wb');
		fwrite($fp, $contents);
		fclose($fp);
	}

	/**
	 * Get the cache folder location
	 *
	 * @return string
	 */
	public function GetLocation()
	{
		return $this->folder->GetLocation();
	}
}

/* End of file cache.php */
/* Location: ./sumloader/classes/cache.php */