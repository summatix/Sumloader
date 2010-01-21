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
 * Represents a directory. Provides ability to read file information
 */
class Folder  {
	/**
	 * @type string
	 */
	private $location;

	/**
	 * Initialize the Folder instance
	 *
	 * @pre $location exists on the filesystem
	 * @param $location string The location of this folder to initialize
	 */
	public function Folder($location)
	{
		$this->location = realpath($location).'/';
	}

	/**
	 * Returns whether or not the given file exists
	 *
	 * @param $filename string The filename to search for
	 * @return bool
	 */
	public function FileExists($filename)
	{
		return file_exists($this->location.$filename);
	}

	/**
	 * Returns the contents of the given file
	 *
	 * @pre $filename exists within the folder
	 * @param $filename string The filename of the file to receive contents for
	 * @return string The contents of the file
	 */
	public function GetFile($filename)
	{
		return file_get_contents($this->location.$filename);
	}

	/**
	 * Returns the time the given file was last modified
	 *
	 * @pre $filename exists within the folder
	 * @param $filename string
	 * @return unix timestamp
	 */
	public function LastModified($filename)
	{
		return filemtime($this->location.$filename);
	}

	/**
	 * Return the actual folder location
	 *
	 * @return string
	 */
	public function GetLocation()
	{
		return $this->location;
	}
}

/* End of file folder.php */
/* Location: ./sumloader/classes/folder.php */