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
 * Process the given list of files. Combines, minifies, compresses and caches the files
 */
class FileProcessor
{
	/**
	 * The list of possible paramater values
	 *
	 * @type array of string
	 */
	private static $paramsArray = array('minify', 'encoding', 'type');
	
	/**
	 * @type bool
	 */
	private $minify;

	/**
	 * @type string
	 */
	private $encoding;

	/**
	 * @type "css" or "js"
	 */
	private $type;

	/**
	 * @type Folder
	 */
	private $sourceFolder;

	/**
	 * @type Outputter
	 */
	private $output;

	/**
	 * @type Cache
	 */
	private $cache;

	/**
	 * Constructor. Processes the given file string
	 *
	 * @throws Exception If any paramters are invalid
	 * @param $files string The list of files to process
	 * @param $params array of string Object paramaters
	 */
	public function FileProcessor($files, $params) 
	{
		foreach (FileProcessor::$paramsArray as $key)
		{
			$this->$key = $params[$key];
		}

		$this->output = new Outputter();
		$this->cache = new Cache(CACHE_FOLDER);

		if ($this->type == 'js')
		{
			$this->sourceFolder = new Folder(JAVASCRIPT_FOLDER);
		}
		else if ($this->type == 'css')
		{
			$this->sourceFolder = new Folder(CSS_FOLDER);
		}
		else
		{
			throw new Exception('Type must be either \'js\' or \'css\'');
		}

		// Create the regular expression to test for a valid $files paramater
		$type = $this->type;
		$regex = "%\A([-_a-z0-9]+((\.|/)[-_a-z0-9]+)*)\.{$type}(,([-_a-z0-9]+((\.|/)[-_a-z0-9]+)*)\.{$type})*\z%i";
		
		if (preg_match($regex, $files)) // If file string is valid, process the chosen files
		{
			$this->ProcessFileString($files);
		}
		else // Throw exception if invalid string argument is given
		{
			throw new Exception("Invalid files argument given: {$files}");
		}
	}

	/**
	 * Starts the processing of the files string. Calls either ProcessSingleFile or
	 * ProcessMultipleFiles based on the given argument
	 *
	 * @throws Exception If any errors occur trying to process the files within the string
	 * @param $files non empty string
	 */
	private function ProcessFileString($files) 
	{
		if (strpos($files, ',') === FALSE) // If only a single file was given
		{
			$this->ProcessSingleFile($files);
		}
		else // If multiple files were specified
		{
			$fileArray =& splitFiles($files);
			$this->ProcessMultipleFiles($fileArray, $files);
		}
	}

	/**
	 * Combine, minify, compress and output the given files
	 *
	 * @param $lastModified timestamp The last modified date of the files
	 * @param $filesToCombine array of string or string of single file
	 * @param $filestring string The string used to request the files
	 */
	private function CompressAndOutput($lastModified, &$filesToCombine, $filestring)
	{
		$time = gmdate('D, d M Y H:i:s', $lastModified).' GMT';

		// If the browser already has the latest version of the file, send the appropriate header status and exit the
		// application
		if (Browser::HasLatestVersion($time)) 
		{
			header('HTTP/1.0 304 Not Modified');
			exit;
		}
		else //Otherwise send the new content to the browser
		{
			$this->output->SetLastModified($time);
			$this->output->SetEncoding($this->encoding);
			$this->output->SetContentType($this->type);

			$id = getId($lastModified, $filestring, $this->type, $this->encoding, FALSE);

			if ($this->minify)
			{
				$mid = getId($lastModified, $filestring, $this->type, $this->encoding, TRUE);
				
				if ($this->cache->Exists($mid)) // Use the cached file if it exists
				{
					$this->output->SetContent($this->cache->Get($mid));
				}
				else // Otherwise create the new file
				{
					$nid = getId($lastModified, $filestring, $this->type, 'none', FALSE);
					$nmid = getId($lastModified, $filestring, $this->type, 'none', TRUE);

					if ($this->cache->Exists($nid))
					{
						$this->MinifyCompressAndOutput($nid, $mid, $nmid);
					}
					else
					{
						$encoding = $this->encoding;
						$this->encoding = 'none';
						$this->CombineFiles($nid, $filesToCombine, FALSE);
						$this->encoding = $encoding;
						$this->MinifyCompressAndOutput($nid, $mid, $nmid);
					}
				}
			}
			else
			{
				if ($this->cache->Exists($id)) // Use the cached file if it exists
				{
					$this->output->SetContent($this->cache->Get($id));
				}
				else // Otherwise create the file
				{
					$this->CombineFiles($id, $filesToCombine);
				}
			}
		}
	}

	/**
	 * Minifies, compresses and outputs the files
	 *
	 * @param $nid string The id
	 * @param $mid string The minified encoded id
	 * @param $nmid string The minifies non encoded id
	 */
	private function MinifyCompressAndOutput($nid, $mid, $nmid)
	{
		// Minifiy files
		if ($this->type == 'css')
		{
			minify($nid, $nmid, $this->cache);
		}
		else //if ($this->type == 'js')
		{
			closureCompile($nid, $nmid, $this->cache);
		}

		// Compress files
		if ($this->encoding != 'none')
		{
			$content =& gzencode($this->cache->Get($nmid), 9, $this->encoding == 'gzip' ? FORCE_GZIP : FORCE_DEFLATE);
			$this->cache->Save($mid, $content);
		}

		// Output files
		$content =& $this->cache->Get($mid);
		$this->output->SetContent($content);
	}

	/**
	 * Compress and minify a single file
	 *
	 * @throws Exception If the given file does not exist
	 * @param $file non empty string The filename of the file to process
	 */
	private function ProcessSingleFile($file)
	{
		if ($this->sourceFolder->FileExists($file))
		{
			$this->CompressAndOutput($this->sourceFolder->LastModified($file), $file, $file);
		}
		else // Throw an exception if an invalid file was given
		{
			throw new Exception("{$file} does not exist");
		}
	}

	/**
	 * Compress and minify a multiple files
	 *
	 * @throws Exception if any of the files in the array do not exist
	 * @param $elements array of string
	 * @param $file string The string request
	 */
	private function ProcessMultipleFiles(&$elements, $file)
	{
		if (count($elements) == 0)
		{
			throw new Exception('At least one file must be provided');
		}

		$this->CompressAndOutput($this->GetLastModifiedDate($elements), $elements, $file);
	}

	/**
	 * Combine the single given file, and save it to the cache
	 *
	 * @param $id string The id to save the new file as
	 * @param $file string The file to combine or array of files to combine
	 * @param $output bool (optional) default TRUE When set to TRUE, outputs the resulted combined file
	 */
	private function CombineFiles($id, &$file, $output = TRUE)
	{
		$combiner = new Combiner($this->sourceFolder, $file, $this->minify);
		$result = $combiner->GetResult();

		if ($this->encoding != 'none')
		{
			$result = gzencode($result, 9, $this->encoding == 'gzip' ? FORCE_GZIP : FORCE_DEFLATE);
		}

		if ($output)
		{
			$this->output->SetContent($result);
		}

		$this->cache->Save($id, $result);
	}

	/**
	 * Return the most recent last modified date from the array of files
	 *
	 * @throws Exception If any of the files do not exist
	 * @param $elements array of string The list of files
	 */
	private function GetLastModifiedDate($elements)
	{
		$lastModified = 0;
		foreach ($elements as $file)
		{
			if ($this->sourceFolder->FileExists($file))
			{
				$lastModified = max($lastModified, $this->sourceFolder->LastModified($file));
			}
			else
			{
				throw new Exception("{$file} does not exist");
			}
		}

		return $lastModified;
	}
}

/* End of file fileprocessor.php */
/* Location: ./sumloader/classes/fileprocessor.php */