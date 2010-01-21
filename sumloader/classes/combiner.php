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
 * Concatenates one or more files together
 */
class Combiner	{
	/**
	 * The base directory to look in for the files
	 *
	 * @type Folder
	 */
	private $folder;
	
	/**
	 * @type string or array of string
	 */
	private $files;
	
	/**
	 * @type bool
	 */
	private $minify;
	
	/**
	 * @throws Exception If the given file does not exist
	 * @param $folder Folder
	 * @param $files string or array of string
	 * @param $minify bool default FALSE Set to true to if the files are also going to be
	 * be minified
	 */
	public function Combiner($folder, $files, $minify = FALSE)
	{
		$this->folder = $folder;
		$this->files = $files;
		$this->minify = $minify;
	}
	
	/**
	 * Gets the combined string
	 *
	 * @return string The concatenated files
	 */
	public function GetResult()
	{
		$contents = $this->GetHeader();
		
		// If only one file to concatenate
		if (is_string($this->files))
		{
			$file =& $this->folder->GetFile($this->files);
			$result = $contents.$file;
		}
		else // If multiple files to concatenate (assume $files is array)
		{
			$noOfFiles = count($this->files);
			
			for ($i = 0; $i < $noOfFiles; ++$i)
			{
				$file =& $this->folder->GetFile($this->files[$i]);
				$contents .= ($i != 0 && $i != $noOfFiles) ? "\n{$file}" : $file;
			}
			
			$result = $contents;
		}
		
		return $result;
	}
	
	/**
	 * Gets the header information for the file
	 *
	 * @return string The header information to add to the top of the file
	 */
	private function GetHeader()
	{
		if (ADD_HEADER)
		{
			$date = gmdate('Y-m-d h:i:s').' GMT';
			
			if ($this->minify)
			{
				$start = '/*!';
				$description = 'This file has been combined, compressed and minified by';
				
			}
			else
			{
				$start = '/*';
				$description = 'This file has been combined and compressed by';
			}
			
			$files = '';
			if (is_array($this->files))
			{
				$l = count($this->files);
				for ($i = 0; $i < $l; ++$i)
				{
					if ($i != 0)
					{
						$files .= "\n *  ";
					}
					
					$files .= $this->files[$i];
				}
			}
			else
			{
				$files .= $this->files;
			}
			
			return
"{$start}
 * {$description}
 * Sumloader (c) ShiverCube (http://shivercube.com/)
 *
 * Date created: {$date}
 *
 * Included files:
 *   {$files}
 */\n\n";
		}
		
		return '';
	}
}

/* End of file combiner.php */
/* Location: ./sumloader/classes/combiner.php */