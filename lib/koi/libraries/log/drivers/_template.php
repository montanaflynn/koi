<?php
namespace Koi\Log;

/**
 * Log driver description goes in here.
 * 
 * @author     AUTHOR
 * @link       WEBSITE
 * @licence    LICENSE
 * @package    Koi
 * @subpackage SUBPACKAGE
 *
 * Optionally you can include a license file here.
 */
class _Template implements LogInterface
{
	/**
	 * The constructor is used to create a new instance of the logger and
	 * is used to set configuration options.
	 *
	 * @author Yorick Peterse
	 * @param  array $options Optional array of options is optional
	 * @return object
	 */
	public function __construct($options = array())
	{
		
	}
	
	/**
	 * The write method is used to write the specified data to the log.
	 * The first argument is required and should be a string.
	 *
	 * @author Yorick Peterse
	 * @param  string $data The data to log.
	 * @return bool
	 */
	public function write($data)
	{
		
	}
}