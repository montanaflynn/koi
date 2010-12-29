<?php
namespace Koi\Cache;

/**
 * Cache driver description goes in here.
 * 
 * @author     AUTHOR
 * @link       WEBSITE
 * @licence    LICENSE
 * @package    Koi
 * @subpackage SUBPACKAGE
 *
 * Optionally you can include a license file here.
 */
class _Template implements CacheInterface
{
	/**
	 * Integer containing the TTL (Time To Live) in seconds.
	 *
	 * @access public
	 * @var    integer
	 */
	public $ttl = 3600;
	
	/**
	 * The constructor is used to create a new instance of the cache class
	 * and sets the options.
	 * 
	 * @author Yorick Peterse
	 * @param  array $options Associative array of options for the cache driver.
	 * @return object
	 */
	public function __construct($options = array())
	{
		foreach ( $options as $option => $value )
		{
			if ( isset($this->$option) )
			{
				$this->$option = $value;
			}
		}
	}
	
	/**
	 * The write method is used to create or update a cache record. The first
	 * argument is the name of the key (as cache storages are generally key/value
	 * storage systems) and the second argument the value for that key.
	 *
	 * @author Yorick Peterse
	 * @param  string $key The name of the cache key
	 * @param  mixed $value The value for the cache key
	 * @throws CacheException thrown whenever the data couldn't be written to the cache.
	 * @return bool
	 */
	public function write($key, $value)
	{
		
	}
	
	/**
	 * The read method is used to retrieve the value of the specified key.
	 * If the key does not exist an exception will be triggered.
	 *
	 * @author Yorick Peterse
	 * @param  string $key The name of the cache key
	 * @throws CacheException whenever the cache key doesn't exist or couldn't be read.
	 * @return bool
	 */ 
	public function read($key)
	{
		
	}
	
	/**
	 * The validate method is used to check if the specified cache item exists
	 * and hasn't expired yet. If the cache item is valid TRUE will be returned, otherwise
	 * FALSE.
	 *
	 * @author Yorick Peterse
	 * @param  string $key The cache key to validate
	 * @return bool
	 */
	public function validate($key)
	{
		
	}
	
	/**
	 * The destroy method is used to remove a specific cache record from the storage
	 * engine based on the key's name. This can be useful if you have a bunch of views
	 * cached and you want to remove a particular one. If the row can't be removed
	 * for some reason an exception should be thrown. If the key was removed successfully
	 * TRUE should be returned.
	 *
	 * @author Yorick Peterse
	 * @param  string $key The name of the key to delete.
	 * @throws CacheException thrown whenever the key couldn't be removed.
	 * @return bool
	 */
	public function destroy($key)
	{
		
	}
	
	/**
	 * The destroy_all method is used to clear the entire cache. Note that because
	 * it may be difficult to find out what cache data belongs to your application
	 * it's best to use a single cache record and store all cache items in that
	 * record as sub-items.
	 *
	 * @author Yorick Peterse
	 * @return bool
	 * @throws CacheException thrown whenever the cache couldn't be cleared.
	 */
	public function destroy_all()
	{
		
	}
}