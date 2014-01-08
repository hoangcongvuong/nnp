<?php
/* vim: set expandtab tabstop=2 shiftwidth=2 softtabstop=2: */
/**
* class.XiTemplate.php
*
* PHP version 5.X REQUIRED
* 
* This class file runs with error_reporting(E_STRICT) without creating any 
* E_NOTICE's.
*
* XiTemplate is based on Xtemplate by Barnabas Debreceni.
* XiTemplate has been ported to take advantage of the PHP 5 object model, 
* which adds speed and more accurate handling of member variables and methods 
* within the class. Several new features are introduced like the ability to 
* pass a template from the buffer as well as from a file and the ability to 
* dynamically interpolate multidimensional arrays.
* An attempt was also made to reduce the memory foot print and increase 
* performance.  The performance gain comes during higher load situation 
* where there is less free memory.
*
* @package XiTemplate
*/
/**
* XiTemplate is a very fast and very easy to use PHP template engine that 
* enables a developer to completely seperate logic from presentation.
*
* @package XiTemplate
* @version 1.0.1
* @author Travis Miller <tmiller@web-1hosting.net>
* @author Jim Grill <jimgrill@jimgrill.com>
* @copyright Copyright (c) 2004, Travis Miller, Jim Grill
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
* @tutorial XiTemplate.cls
*/
class XiTemplate
{
  /**
  * @desc raw contents of template file
  */
	private $fileContents = '';
	
	/**
	* @desc unparsed blocks
	*/
	private $blocks       = array();
	
	/**
	* @desc parsed blocks
	*/
	private $parsedBlocks = array();
	
	/**
	* @desc store sub-block names for fast resetting
	*/
	private $subBlocks    = array();
	
	/**
	* @desc variables array
	*/
	private $VARS         = array();
	
	/**
	* auto-reset sub blocks
	*/
	private $AUTORESET    = true;
	
	/**
	* @desc what to assign to unassigned template variables. Defaults to ''. 
	* Sometimes a null value is desired, but if you are parsing a table 
	* and a null value populates a cell this can cause problems. In this case 
	* setting $nullValue to &nbsp; might be a better idea.
	*
	* Note: Sometimes it may be desireable to set a null value temporarily. 
	* To achieve this simply reset null values to ''.
	*
	* @var string $value
	* @example output/nullValue.html Resulting HTML after parsing
	* @example templates/nullValue.tpl HTML Template used for this example
	* @example nullValue.php Example usage of $nullValue
	*/
	public $nullValue       = '';
	

	/**
	* @return object $XiTemplate
	* @param string $template
	* @desc XiTemplate constructor, loads the template text from file or buffer 
	* and instantiates the XiTemplate object.
	* @example output/construct.html Resulting HTML after parsing
	* @example templates/construct.tpl HTML template used for this example
	* @example construct.php Example usage of __construct()
	*/
	function __construct($template)
	{
	  /*check to see if the template comes from the buffer*/
	  /*by looking for "<!-- BEGIN tags"*/
    if (preg_match('/\<!--\sBEGIN/', $template))
			$this->fileContents = $template;
		else
			$this->_recursiveReadFile($template);
		
		$this->_makeTree();
	}

/******************************************************************************/
/* private methods                                                            */
/******************************************************************************/

  /**
	* @return void
	* @param string $msg
	* @desc Issues a warning via trigger_error()
	*/
	private function logWarning($msg)
	{
		trigger_error($msg, E_USER_WARNING);
	}
	
	/**
	* @return void
	* @param string $msg
	* @desc Issues a fatal error via trigger_error()
	*/
	private function logError($msg)
	{
		trigger_error($msg, E_USER_ERROR);
		exit;
	}
	
	/**
	* @return string $fileText
	* @param string $fileName
	* @desc Read the contents of $fileName.
	*/
	private function _readFile($fileName)
	{
		if (!is_file($fileName))
			$this->logError($fileName . ' does not exist.');
    
		if (($fileText = file_get_contents($fileName)) === false)
			$this->logError($fileName . ' is not readable.');
		
		return($fileText);
	}
	
	/**
	* @return void
	* @param string $fileName
	* @desc Read the contents of $fileName in a recursive manner such that 
	*	FILE blocks are also included.
	*/
	private function _recursiveReadFile($fileName)
	{
		/*Read main file contents*/
		$this->fileContents = $this->_readFile($fileName);
		$matches = array();
		
		/*Read FILE: includes*/
		while (preg_match('/\{FILE\s*\"([^\"]+)\"\s*\}/m', $this->fileContents, $matches))
		{
			$includedFile = $this->_readFile($matches[1]);
			$this->fileContents = preg_replace('\'' . preg_quote($matches[0]) . '\'',
				$includedFile, $this->fileContents);
		}
	}

	/**
	* @return void
	* @desc Parse all the blocks out of $this->fileContents.
	*/
	private function _makeTree()
	{
		/*Initialize variables.*/
		$level      = 0;
		$blockNames = array();
		$matches    = array();
		
		/*Find all the delimiters*/
		$delimiters = explode('<!-- ', $this->fileContents);
		
		/*Loop through checking for begining or ending delimiter and manipulate*/
		/*the tree as needed.*/
		foreach($delimiters as $v)
		{
			if (preg_match_all('/^(BEGIN:|END:)\s*(\w+)\s*-->(.*)/ims', $v, $matches, PREG_SET_ORDER))
			{
				/* $matches[0][1] = BEGIN or END*/
				/* $matches[0][2] = block name*/
				/* $matches[0][3] = kinda content*/
				if ($matches[0][1] == 'BEGIN:')
				{
					/*If we have a BEGIN: block, add it to blocks list.*/
					$parentName = implode('.', $blockNames);
					$blockNames[++$level] = $matches[0][2];
					$currentBlockName = implode('.', $blockNames);
					$this->blocks[$currentBlockName] = isset($this->blocks[$currentBlockName]) ?
							$this->blocks[$currentBlockName] . $matches[0][3] :
							$matches[0][3];
					$this->blocks[$parentName] .= '{_BLOCK_.' . $currentBlockName . '}';
					$this->subBlocks[$parentName][] = $currentBlockName;
					$this->subBlocks[$currentBlockName][] = "";
				}
				elseif ($matches[0][1] == 'END:' )
				{
					/*If we have an END block, reduce our parent list.*/
					unset($blockNames[$level--]);
					$parentName = implode('.', $blockNames);
					
					/*Add rest of block to parent block*/
					$this->blocks[$parentName] .= $matches[0][3];
				}
			}
			else
			{
				$index = implode('.', $blockNames); 
				$this->blocks[$index] = (isset($this->blocks[$index])) ?
						($this->blocks[$index] . '<!-- ' . $v) : '<!-- ' . $v;
			}
		}
	}
	
	/**
	* @return void
	* @param array $array
	* @param string $templateVariable
	* @desc Integrate an array, regardless of dimension, into the VARS array.
	*/
	private function _assignArray($array, $templateVariable)
	{
		/*Recursively reduce the array until we don't have an array as the value,*/
		/*then store the path of the array key to the value.*/
		if (is_array($array))
		{
			foreach($array as $k=>$v)
				$this->_assignArray($v, $templateVariable. '.' . $k);
		}
		else
		{
			$this->VARS[$templateVariable] = $array;
		}
	}


/******************************************************************************/
/* public methods                                                             */
/******************************************************************************/

	/**
	* @return void
	* @param string $templateVariable
	* @param mixed $value
	* @desc Assign a value to a template variable. $value can be either a 
	* string or a single or multidimensional array.
	* @example output/assign.html Resulting HTML after parsing
	* @example templates/assign.tpl HTML Template used for this example
	* @example assign.php Example usage of assign()
	*/
	public function assign($templateVariable, $value='')
	{
		/*If assigning an array, use special function to*/
		/*allow multidimensional arrays.*/
		if (is_array($value))
			$this->_assignArray($value, $templateVariable);
		/*Otherwise, just stick it in the list.*/
		else 
			$this->VARS[$templateVariable] = $value;
	}
	
	/**
	* @return void
	* @param string $blockName
	* @desc Parse a block of the template replacing parsed blocks and variables.
	* @example output/parse.html Resulting HTML after parsing
	* @example templates/parse.tpl HTML template used in this example
	* @example parse.php Example usage of parse()
	*/
	public function parse($blockName='main')
	{
		if (!isset($this->blocks[$blockName]))
		{
			$this->logError('Template::parse() - block [' . 
				$blockName . '] does not exist.');
		}

		/*Save a copy of the old block so that it can be appended to.*/
		$blockCopy = $this->blocks[$blockName];
		
		/*Find all the variables in this block.*/
		$varArray = array();
		preg_match_all('/\{([A-Za-z0-9\._]+?)}/', 
			$this->blocks[$blockName], $varArray);
		$varArray = $varArray[1];
			
		/*For each variable, determine if it represents a block or an assigned var, then*/
		/*replace it accordingly.*/
		foreach($varArray as $v)
		{
			if (strncmp($v, '_BLOCK_', 7) == 0)
			{
				/*We are dealing with a parsed block.*/
			  $parsedBlockName = substr($v, 8);
			  
			  /*If it has been parsed, replace it with the parsed text, else leave it blank.*/
				$var = (isset($this->parsedBlocks[$parsedBlockName])) ?
					trim(($this->parsedBlocks[$parsedBlockName])) :	'';
				/*str_replace is faster and fixes the old Xtemplate US$ bug #440545*/
				$blockCopy = str_replace('{' . $v . '}', $var, $blockCopy);
			}
			else
			{
				/*If variable has been assigned, replace it, else assign it*/
				/*our default $nullValue.*/
				$var = (isset($this->VARS[$v]) && !empty($this->VARS[$v])) ? 
				  $this->VARS[$v] : $this->nullValue;
				/*str_replace is faster and fixes the old Xtemplate US$ bug #440545*/
				$blockCopy = str_replace('{' . $v . '}', $var, $blockCopy);
			}
		}
		/*Replace the old parsed block with itself plus the new data.*/
		$this->parsedBlocks[$blockName] = (isset($this->parsedBlocks[$blockName])) ?
				$this->parsedBlocks[$blockName] . $blockCopy : $blockCopy;
		
		/*reset sub-blocks*/
		if ($this->AUTORESET && (!empty($this->subBlocks[$blockName])))
		{
			reset($this->subBlocks[$blockName]);
			foreach($this->subBlocks[$blockName] as $subBlock)
				$this->reset($subBlock);
		}
	}

	/**
	* @return void
	* @param string $blockName
	* @desc Parse a block of the template and all sub-blocks under it.
	* @example output/rparse.html Resulting HTML after parsing
	* @example templates/rparse.tpl HTML template used in this example
	* @example rparse.php Example usage of rparse()
	*/
	public function rparse($blockName='main')
	{
		if (!empty($blockName))
		{
			reset($this->subBlocks[$blockName]);
			foreach($this->subBlocks[$blockName] as $block)
				if (!empty($block))	$this->rparse($block);
		}
		$this->parse($blockName);
	}

	/**
	* @return void
	* @param string $blockName
	* @desc Resets a parsed block to empty.
	*/
	public function reset($blockName)
	{
		if (!isset($this->parsedBlocks[$blockName]))
		  return;
		
		$this->parsedBlocks[$blockName] = '';
	}
	
	/**
	* @return string parsedBlock
	* @param string $blockName
	* @desc Returns parsed block of template.
	*/
	public function text($blockName='main')
	{
		if (sizeof($this->parsedBlocks) == 0)
			$this->logError('Cannot get template content, 
				main block has yet to be parsed');
		
	  if (!isset($this->parsedBlocks[$blockName]))
      $this->logError('Block: "'. $blockName .'" does not exist');
    
		return($this->parsedBlocks[$blockName]);
	}
	
	/**
	* @return void
	* @param string $blockName
	* @desc Outputs a parsed block of a template to the browser. 
	* Will generate a fatal error if $blockName does not exist in the 
	* template instance.
	* @example output/out.html Resulting HTML after parsing
	* @example templates/out.tpl HTML template used in this example
	* @example out.php Example usage of out()
	*/
	public function out($blockName='main')
	{
		echo($this->text($blockName));
	}
}
?>