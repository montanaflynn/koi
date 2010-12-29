<?php
namespace Koi\Cache;

/**
 * The APC cache stores all data in the APC cache. The advantage of APC
 * is that it's fast, keeps track of the TTL itself and can be used by simply
 * installing the PECL installation. For more information go to the
 * "APC":http://nl2.php.net/apc page.
 *
 * Each key/value combination
 * is stored as a new row in the APC store with a prefix of "koi_" to ensure
 * that existing content isn't overwritten. This prefix can be customized by
 * setting a custom prefix in the "prefix" configuration option.
 *
 * When working with APC you can use the following configuration options:
 *
 * * ttl: the time after an individual record should be removed
 * * prefix: the prefix to use for each record
 * 
 * **IMPORTANT**: if you want to use APC from the CLI you have to set the following
 * configuration item in your php.ini file:
 *
 * @apc.enable_cli = On@
 *
 * Another thing to remember is that APC won't clear the cache during the same request
 * unless you call the apc_clear_cache('user') function or the destroy_all() method.
 *
 * @author     Yorick Peterse
 * @link       http://yorickpeterse.com/
 * @licence    MIT License
 * @package    Koi
 * @subpackage Cache
 * 
 * Copyright (c) 2010, Yorick Peterse
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
class APC implements CacheInterface
{
	/**
	 * Integer containing the TTL (Time To Live) in seconds.
	 *
	 * @access public
	 * @var    integer
	 */
	public $ttl = 3600;
	
	/**
	 * String containing the prefix for each record.
	 *
	 * @access public
	 * @var    string
	 */
	public $prefix = 'koi_';
	
	/**
	 * Static array that keeps track of all the keys that are used.
	 *
	 * @static
	 * @access public
	 * @var    array
	 */
	public static $used_keys = array();
	
	/**
	 * The constructor is used to create a new instance of the cache class
	 * and sets the options.
	 * 
	 * @author Yorick Peterse
	 * @param  array $options Associative array of options for the cache driver.
	 * @throws CacheException thrown whenever the APC extension isn't installed.
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
		
		// Check if all the required APC functions are installed
		$apc_functions = array('apc_store', 'apc_fetch', 'apc_exists', 'apc_delete');
		
		foreach ( $apc_functions as $func )
		{
			if ( !function_exists($func) )
			{
				throw new \Koi\Exception\CacheException("The function $func does not exist, make sure APC is installed");
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
		$key = $this->prefix . $key;
		
		if ( !apc_store($key, $value, $this->ttl) )
		{
			throw new \Koi\Exception\CacheException("The key \"$key\" could not be written");
		}
		
		// Let's keep track of the key
		if ( !array_search($key, self::$used_keys) )
		{
			self::$used_keys[] = $key;
		}
		
		return TRUE;
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
		if ( $this->validate($key) === FALSE )
		{
			throw new \Koi\Exception\CacheException("The key \"$key\" is invalid");
		}
		
		$key = $this->prefix . $key;
		
		return apc_fetch($key);
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
		$key = $this->prefix . $key;
		
		if ( !apc_exists($key) )
		{
			// Remove the corresponding key from the used_keys array			
			if ( $search = array_search($key, self::$used_keys) )
			{
				unset(self::$used_keys[$search]);
			}
			
			return FALSE;
		}
		
		return TRUE;
	}
	
	/**
	 * The destroy method is used to remove a specific cache record from the storage
	 * engine based on the key's name. This can be useful if you have a bunch of views
	 * cached and you want to remove a particular one.
	 *
	 * @author Yorick Peterse
	 * @param  string $key The name of the key to delete.
	 * @throws CacheException thrown whenever the key couldn't be removed.
	 * @return bool
	 */
	public function destroy($key)
	{
		$key = $this->prefix . $key;
		
		if ( apc_exists($key) AND !apc_delete($key) )
		{
			throw new \Koi\Exception\CacheException("The key \"$key\" could not be removed");
		}
		
		if ( $search = array_search($key, self::$used_keys) )
		{
			unset(self::$used_keys[$search]);
		}
		
		return TRUE;
	}
	
	/**
	 * The destroy_all method is used to clear the entire cache.
	 *
	 * @author Yorick Peterse
	 * @throws CacheException thrown whenever the cache couldn't be cleared.
	 * @return bool
	 */
	public function destroy_all()
	{
		foreach ( self::$used_keys as $key )
		{
			if ( apc_exists($key) AND !apc_delete($key) )
			{
				throw new \Koi\Exception\CacheException("The key \"$key\" could not be removed");
			}
		}
		
		return TRUE;
	}
}