<?php
/**
 * Sumloader
 *
 * @author		ShiverCube
 * @copyright	Copyright (c) 2009, ShiverCube
 * @license		http://shivercube.com/sumloader/license/
 * @link		http://shivercube.com/sumloader/
 */

// Turn off all error reporting
error_reporting(0);

// Define constants
define('ALLOW_MINIFY', TRUE); // Whether or not to allow ?min=true to be passed
define('JAVASCRIPT_FOLDER', './scripts'); // Path to where JavaScript files are located
define('CSS_FOLDER', './css'); // Path to where CSS files are located

// Path to the location of where to save the cache. This directory must have write permissions
define('CACHE_FOLDER', './cache');
define('JAVA', 'java'); // The path to the JVM
define('YUICOMP_JAR', './yuicompressor-2.4.2.jar'); // The path to the YUI Compressor JAR file
define('GCC_JAR', './compiler.jar'); // The path to the Google Closure Compiler JAR file

// Load the system
include('../sumloader/combine.php');

/* End of file __combine.php */
/* Location: ./__combine.php */