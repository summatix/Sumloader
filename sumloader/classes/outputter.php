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
 * Provides an interface to output content to the browser
 */
class Outputter  {
	/**
	 * @type string
	 */
	private $content = '';

	/**
	 * @type "css" or "javascript"
	 */
	private $type = 'javascript';

	/**
	 * @type "none", "gzip" or "deflate"
	 */
	private $encoding = 'none';

	/**
	 * @type string
	 */
	private $lastModified = '';

	/**
	 * Set the time the page was last modified
	 *
	 * @param $time string The GMT string last modified timestamp
	 */
	public function SetLastModified($time)
	{
		$this->lastModified = $time;
	}

	/**
	 * Set the content the page should display, and outputs it to the browser
	 *
	 * @pre This is the first call of the method
	 * @param $content string
	 */
	public function SetContent(&$content)
	{
		$this->content = $content;
		$this->Flush();
	}

	/**
	 * Set the type of content the page will display
	 *
	 * @param $type "css" or "js"
	 */
	public function SetContentType($type)
	{
		if ($type == 'css')
		{
			$this->type = 'css';
		}
	}

	/**
	 * Set the type of compression to use for the page
	 *
	 * @param $encoding "none", "gzip" or "deflate"
	 */
	public function SetEncoding($encoding)
	{
		$this->encoding = $encoding;
	}

	/**
	 * Send the page to the browser
	 */
	private function Flush()
	{
		header('Cache-Control: max-age=2629743,must-revalidate');
		header("Last-Modified: {$this->lastModified}");

		if ($this->encoding != 'none')
		{
			header("Content-Encoding: {$this->encoding}");
		}

		header("Content-Type: text/{$this->type}");
		header('Content-Length: '.strlen($this->content));
		
		echo $this->content;
	}
}

/* End of file outputter.php */
/* Location: ./sumloader/classes/outputter.php */