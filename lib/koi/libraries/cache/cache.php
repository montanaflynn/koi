<?php
namespace Koi\Cache;

/**
 * Caches can be used to store data in an external storage engine in order to
 * speed things up. For example, configuration files generally don't change on a
 * production server so loading them again and again for every request is a waste
 * of resources. Using one of the caches provided by Koi, such as the APC cache,
 * you can store these in-memory (or somewhere else) and load the cache instead
 * of the regular configuration files.
 *
 * Koi ships with the following caches:
 *
 * * APC (requires the APC extension)
 * * Redis
 * * File
 *
 * Before you choose a cache you'll need to know when to use what cache.
 *
 * h2. File Cache
 *
 * The File cache is probably one of the easiest to use as it has to dependencies
 * such as APC and Redis. It's most useful for caching data such as views, API
 * responses and so on. The only downside of the File cache is that it's slower
 * than the APC cache and probably also slower than the Redis cache (depends on your setup).
 *
 * A new instance of the File cache can be created by doing the following:
 *
 * @$cache = new Koi\Cache\Cache('file');@
 *
 * As you can see the syntax is exactly the same as with views and loggers. Just
 * like loggers you can use the second argument for additional options:
 *
 * @$cache = new Koi\Cache\Cache('file', array('ttl' => 3600));@
 *
 * Once the cache has been created you can read/write/destroy data using the respective
 * methods. The File cache has the following available options:
 *
 * * ttl: the time in seconds after which the cache has to be refreshed
 * * directory: the directory to store the cache files in
 *
 * h2. APC Cache
 *
 * If you're one of those fancy lads that has APC installed you can also use the APC
 * cache. This cache stores all of it's data in memory, this results in _very_ good
 * performance. The only downside is that you'll have to make sure there's enough
 * memory assigned to APC as caching data isn't going to work if it's pruned on every
 * request due to too much data being stored. For more information see the PHP
 * documentation on APC "here":http://nl.php.net/manual/en/book.apc.php
 *
 * Working with the APC cache works exactly the same as working with any other cache.
 * First you'll need to create a new instance of the cache:
 *
 * @$cache = new Koi\Cache\Cache('apc');@
 *
 * Additional options can be set in an associative array as the second argument:
 *
 * @$cache = new Koi\Cache\Cache('apc', array('ttl' => 3600));@
 *
 * The APC cache can be customized using the following options:
 *
 * * ttl: the time after which the cache will be cleared
 * * serialize: boolean that indicates if the data should be serialized
 *
 * h2. Redis Cache
 *
 * The Redis cache stores it's data in a Redis database. When creating a new instance
 * you specify the Redis connection data and you're pretty much good to go.
 *
 * @$cache = new Koi\Cache\Cache('redis', array('host' => 'localhost', 'port' => 6379));@
 *
 * Now that our connection has been set up we can start caching:
 *
 * @$cache->write('username', 'Chuck Norris');@
 *
 * Note that just with all other caches calling the write() method using the same
 * key name will overwrite the existing value.
 * 
 * @author  Yorick Peterse
 * @link    http://yorickpeterse.com/
 * @licence MIT License
 * @package Koi
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
class Cache
{
	/**
	 * Assoaciative array containing our driver names and classes
	 * to use for each driver.
	 *
	 * @static
	 * @access public
	 * @var    array
	 */
	public static $drivers = array(
		'file'  => 'Koi\Cache\File',
		'apc'   => 'Koi\Cache\APC',
		'redis' => 'Koi\Cache\Redis'
	);
	
	/**
	 * String containing the name of the default cache driver to use
	 * in case no driver has been specified in the constructor.
	 *
	 * @static
	 * @access public
	 * @var    string
	 */
	public static $default_driver = 'file';
	
	/**
	 * Object containing a new instance of the cache driver.
	 *
	 * @access public
	 * @var    object
	 */
	public $cache_driver = NULL;
	
	/**
	 * Creates a new instance of the cache class and loads the driver.
	 * Note that in case you're using the default driver you can simply ignore
	 * the first argument and just pass an array to this method. The constructor
	 * will detect this and automatically use the default driver.
	 *
	 * This: @$cache = new Koi\Cache\Cache('file', array('ttl' => 3600));@
	 *
	 * Is exactly the same as this:
	 *
	 * bc. Koi\Cache\Cache::$default_driver = 'file';
	 * $cache = new Koi\Cache\Cache(array('ttl' => 3600));
	 *
	 * @author Yorick Peterse
	 * @param  string $driver The cache driver to use.
	 * @param  array $options Associative array containing additional
	 * configuration options such as the host to connect to when using the Redis cache.
	 * @throws CacheException thrown whenever a driver doesn't exist or couldn't be loaded.
	 * @return object
	 */
	public function __construct($driver, $options = array())
	{
		if ( is_array($driver) )
		{
			$options = $driver;
			$driver  = self::$default_driver;
		}
		
		if ( empty($driver) OR !isset($driver) )
		{
			$driver  = self::$default_driver;
		}
		
		if ( !isset(self::$drivers[$driver]) )
		{
			throw new Koi\Exception\CacheException("The specified driver (\"$driver\") does not exist");
		}
		
		$this->cache_driver = new self::$drivers[$driver]($options);
	}
	
	/**
	 * Stores the key/value pair in the cache. If there already is a row for the given
	 * name the old value will be overwritten with the new value.
	 *
	 * @author Yorick Peterse
	 * @param  string $key The key's name.
	 * @param  mixed $value The key's value.
	 * @return bool
	 */
	public function write($key, $value)
	{
		
	}
	
	/**
	 * Retrieves the value for the specified key. If the key doesn't exist FALSE will
	 * be returned instead.
	 *
	 * @author Yorick Peterse
	 * @param  string $key The key to select
	 * @return mixed
	 */
	public function read($key)
	{
		
	}
	
	/**
	 * Destroys the cache data for the given name.
	 *
	 * @author Yorick Peterse
	 * @param  string $key The key for which to destroy the data.
	 * @return bool
	 */
	public function destroy($key)
	{
		
	}
	
	/**
	 * Destroys all cache data.
	 *
	 * @author Yorick Peterse
	 * @return bool
	 */
	public function destroy_all()
	{
	
	}
}