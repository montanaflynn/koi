<?php
namespace Koi\View;

/**
 * View driver description goes in here.
 * 
 * @author     AUTHOR
 * @link       WEBSITE
 * @licence    LICENSE
 * @package    Koi
 * @subpackage SUBPACKAGE
 *
 * Optionally you can include a license file here.
 */
class _Template implements ViewInterface
{
	/**
	 * String that will contain the raw view data.
	 *
	 * @access public
	 * @var    string
	 */
	public $raw_view = '';
	
	/**
	 * Variable containing a new instance of the template system.
	 *
	 * @access public
	 * @var    object
	 */
	public $_template = NULL;
	
	/**
	 * Creates a new instance of the driver and stores the raw content
	 * of the view in a variable.
	 *
	 * @author Yorick Peterse
	 * @param  string $raw_view The raw view data or a path to the view file to load.
	 * @param  array  $variables Associative array containing variables that will be sent
	 * @return object
	 */
	public function __construct($raw_view, $variables = array())
	{
		// File or raw data?
		if ( file_exists($raw_view) )
		{
  
		}
		
		$this->raw_view = $raw_view;
	}
	
	/**
	 * Renders the view data and returns the results.
	 *
	 * @author Yorick Peterse
	 * @return string
	 */
	public function render()
	{
		return $this->raw_view;
	}
}