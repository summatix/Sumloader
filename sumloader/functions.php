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
 * Returns an id from the given attributes
 *
 * @param $lastModified timestamp
 * @param $files string
 * @param $type string Either "css" or "js"
 * @param $encoding string default ""
 * @param $minify bool default FALSE
 * @return string
 */
function getId($lastModified, $files, $type, $encoding = '', $minify = FALSE)
{
	$md5 = md5($files);
	$minify = $minify ? 'min.' : '';

	return "{$lastModified}.{$md5}.{$encoding}.{$minify}{$type}";
}

/**
 * Get a value from the $_GET global var
 *
 * @param $name string The non empty variable name to retrieve
 * @return mixed or FALSE if the value is not set
 */
function get($name)
{
	return isset($_GET[$name]) ? $_GET[$name] : FALSE;
}

/**
 * Returns an array of strings which were separated by commas from the given string
 *
 * @param $files non empty string
 * @return array of string
 */
function splitFiles($files)
{
	return explode(',', $files);
}

/**
 * Minifies the given file and saves the result to the specified location
 *
 * @param $file string The file location to minify
 * @param $output string The file location to save the file
 * @param $cache Cache The cache object of the application
 */
function minify($file, $output, &$cache)
{
	exec(JAVA.' -jar '.YUICOMP_JAR.' -o '.$cache->GetLocation().$output.' '.$cache->GetLocation().$file);

	if ( ! file_exists($cache->GetLocation().$output))
	{
		throw new Exception('Could not minify file');
	}
}

/**
 * Compiles the given JavaScript file using Google's Colsure Compiler and saves the result to the specified location
 *
 * @param $file string The file location to minify
 * @param $output string The file location to save the file
 * @param $cache Cache The cache object of the application
 */
function closureCompile($file, $output, &$cache)
{
	exec(JAVA.' -jar '.GCC_JAR.' --js '.$cache->GetLocation().$file.' --js_output_file '
		.$cache->GetLocation().$output);

	if ( ! file_exists($cache->GetLocation().$output))
	{
		throw new Exception('Could not minify file');
	}
}

/* End of file functions.php */
/* Location: ./sumloader/functions.php */