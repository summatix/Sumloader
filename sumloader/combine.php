<?php
/**
 * Sumloader
 *
 * @author		ShiverCube
 * @copyright	Copyright (c) 2009, ShiverCube
 * @license		http://shivercube.com/sumloader/license/
 * @link		http://shivercube.com/sumloader/
 */

$basepath = dirname(__FILE__).'/';
require "{$basepath}functions.php";
require "{$basepath}classes/folder.php";
require "{$basepath}classes/combiner.php";
require "{$basepath}classes/cache.php";
require "{$basepath}classes/outputter.php";
require "{$basepath}classes/fileprocessor.php";
require "{$basepath}classes/browser.php";

try
{  
	$minify = (ALLOW_MINIFY && get('min'));

	// Determine whether or not to add a header to the processed files
	define('ADD_HEADER', $minify ? TRUE : !get('debug'));

	if (($files = get('files')) === FALSE)
	{
		throw new Exception('No files are given');
	}

	if (($type = get('type')) != 'js' && $type != 'css')
	{
		throw new Exception('Invalid file type given');
	}

	// Start the process of combining the chosen files
	new FileProcessor($files, array(
		'minify' => $minify,
		'encoding' => Browser::EncodingType(),
		'type' => $type
	));
}
catch (Exception $e) //  If any errors occur, send 404 header and output the error message to the browser
{
	header('HTTP/1.0 404 Not Found');
	echo $e->getMessage();
}

/* End of file combine.php */
/* Location: ./sumloader/combine.php */