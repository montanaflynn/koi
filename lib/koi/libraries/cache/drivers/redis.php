<?php
namespace Koi\Cache;

/**
 * Cache driver description
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
class Redis implements CacheInterface
{
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