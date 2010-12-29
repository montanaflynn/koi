<?php
namespace Koi\Cache;

/**
 * The File cache is a cache driver that serializes the data and writes it to a file.
 * Each key will be converted to a MD5 hash and is used as the filename. In order
 * to use this driver you'll have to make sure your data can be serialized using
 * the serialize() function provided by PHP.
 *
 * The File cache has the following configuration options:
 *
 * * ttl: specifies the amount of seconds after which a cache file will be removed.
 * * directory: the base directory in which all cache files will be stored
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
class File implements CacheInterface
{
	/**
	 * Integer containing the TTL (Time To Live) in seconds.
	 *
	 * @access public
	 * @var    integer
	 */
	public $ttl = 3600;
	
	/**
	 * String containing the full path to the cache directory.
	 *
	 * @access public
	 * @var    string
	 */
	public $directory = '';
	
	/**
	 * Creates a new instance of the File cache and prepares it by creating
	 * the directory (if it doesn't already exist), setting the options, etc.
	 *
	 * @author Yorick Peterse
	 * @param  array $options Array containing configuration options
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
		
		if ( !isset($this->directory) OR empty($this->directory) )
		{
			throw new \Koi\Exception\CacheException("You need to specify a cache directory to use");
		}
		
		if ( substr($this->directory, -1, 1) === '/' )
		{
			$this->directory = substr_replace($this->directory, '', -1, 1);
		}
		
		// Check if the directory exists
		if ( !is_dir($this->directory) OR !file_exists($this->directory) )
		{
			// Look at me molesting those error messages, take that PHP!
			if ( !@mkdir($this->directory) )
			{
				throw new \Koi\Exception\CacheException("The cache directory {$this->directory} does not exist and couldn't be created");
			}
		}
	}
	
	/**
	 * Writes the specified data to the cache file. In order to
	 * retrieve the correct file the key name will be hashed using
	 * the MD5 algorithm (it doesn't need to be secure anyway).
	 *
	 * @author Yorick Peterse
	 * @param  string $key The name of the cache key
	 * @param  mixed $value The value for the cache key
	 * @throws CacheException thrown whenever the data couldn't be written to the cache.
	 * @return bool
	 */
	public function write($key, $value)
	{
		$time  = time();
		$key   = md5($key);
		$cache = array('time' => $time, 'value' => $value);
		$path  = $this->directory . '/' . $key;

		// Let's see if the file already exists
		if ( is_file($path) AND file_exists($path) )
		{
			$handle         = fopen($path, 'r');
			$cache          = unserialize(fread($handle, filesize($path)));
			$cache['value'] = $value;
			
			fclose($handle);
		}
		
		// Time to write the cache data
		$handle = fopen($path, 'w');
		$cache  = serialize($cache);
		
		if ( fwrite($handle, $cache) )
		{
			return TRUE;
		}
		else
		{
			throw new \Koi\Exception\CacheException("Failed to write the cache to $path");
		}
	}
	
	/**
	 * Retrieves the cache data stored in the file for the specified key.
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
			$this->destroy($key);
			
			throw new \Koi\Exception\CacheException("The cache file for the key \"$key\" is no longer valid");
		}
		
		$key  = md5($key);
		$path = $this->directory . '/' . $key;
		
		if ( !is_file($path) OR !file_exists($path) )
		{
			throw new \Koi\Exception\CacheException("The cache file for the key \"$key\" does not exist");
		}
		
		// Open the file and return the content
		$handle  = fopen($path, 'r');
		$content = fread($handle, filesize($path));
		
		if ( $content = unserialize($content) )
		{
			return $content['value'];
		}
		else
		{
			throw new \Koi\Exception\CacheException("The data in the cache file for the key \"$key\" could not be unserialized");
		}
	}
	
	/**
	 * The validate method is used to check if the specified cache file exists
	 * and hasn't expired yet. If the cache item is valid TRUE will be returned, otherwise
	 * FALSE.
	 *
	 * The following conditions have to be met:
	 *
	 * * the file must exist
	 * * the file shouldn't be expired
	 * * the file should be readable
	 *
	 * @author Yorick Peterse
	 * @param  string $key The cache key to validate
	 * @return bool
	 */
	public function validate($key)
	{
		$key  = md5($key);
		$time = time();
		$path = $this->directory . '/' . $key;
		
		// Validation time
		if ( !is_file($path) OR !file_exists($path) OR !is_readable($path) )
		{
			return FALSE;
		}
		
		$handle  = fopen($path, 'r');
		$content = fread($handle, filesize($path));
		
		if ( $content = unserialize($content) )
		{
			if ( ($time - $content['time']) <= $this->ttl )
			{
				return TRUE;
			}
		}
		
		return FALSE;
	}
	
	/**
	 * The destroy method is used to remove a specific cache record from the storage
	 * engine based on the key's name. This can be useful if you have a bunch of views
	 * cached and you want to remove a particular one. If the row can't be removed
	 * for some reason an exception will be thrown. 
	 *
	 * @author Yorick Peterse
	 * @param  string $key The name of the key to delete.
	 * @throws CacheException thrown whenever the key couldn't be removed.
	 * @return bool
	 */
	public function destroy($key)
	{
		$key  = md5($key);
		$path = $this->directory . '/' . $key;
		
		if ( !is_file($path) OR !file_exists($path) )
		{
			return TRUE;
		}
		
		if ( unlink($path) )
		{
			return TRUE;
		}
		else
		{
			throw new \Koi\Exception\CacheException("The cache file $path could not be removed");
		}
	}
	
	/**
	 * Removes all cache files saved in the cache directory as specified in $this->directory.
	 *
	 * @author Yorick Peterse
	 * @return bool
	 * @throws CacheException thrown whenever the cache files couldn't be removed.
	 */
	public function destroy_all()
	{
		$path = $this->directory;
		
		if ( !is_dir($path) OR !file_exists($path) )
		{
			return TRUE;
		}
		
		foreach ( glob($path . '/*') as $file )
		{
			if ( !unlink($file) )
			{
				throw new \Koi\Exception\CacheException("The cache file $file could not be removed");
			}
		}
		
		return TRUE;
	}
}