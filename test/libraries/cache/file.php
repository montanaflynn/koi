<?php
require_once __DIR__ . '/../../helper.php';

use Waffles\Test as Test;

define('CACHE_DIR', __DIR__ . '/file_cache');

Test::group("Test the File cache", function()
{
	Test::add("Create a new instance of the File cache", function($test)
	{
		$cache = new Koi\Cache\Cache('file', array('directory' => CACHE_DIR));
		
		$test->expects($cache)->to()->be_type_of('object');
	});
	
	Test::add("Write some data to the cache", function($test)
	{
		$cache   = new Koi\Cache\Cache('file', array('directory' => CACHE_DIR));
		$results = $cache->write('name', 'Yorick Peterse');
		
		$test->expects($results)->to()->be_type_of('boolean');
		$test->expects($results)->to()->equal(TRUE);
	});
	
	Test::add("Retrieve data from the cache", function($test)
	{
		$cache   = new Koi\Cache\Cache('file', array('directory' => CACHE_DIR));
		$results = $cache->read('name');
	
		$test->expects($results)->to()->be_type_of('string');
		$test->expects($results)->to()->equal('Yorick Peterse');
	});
	
	Test::add("Test the expiration of a file", function($test)
	{
		$cache   = new Koi\Cache\Cache('file', array('directory' => CACHE_DIR, 'ttl' => 2));
		$wrote   = $cache->write('greeting', 'Hello, world!');
		
		sleep(3);
		
		try
		{
			$cache->read('greeting');
			$read = TRUE;
		}
		catch ( Koi\Exception\CacheException $e )
		{
			$read = FALSE;
		}
		
		$test->expects($wrote)->to()->equal(TRUE);
		$test->expects($read)->to()->equal(FALSE);
	});
	
	Test::add("Remove a cache file", function($test)
	{
		$cache  = new Koi\Cache\Cache('file', array('directory' => CACHE_DIR));
		$cache->write('name', 'Chuck Norris');
		$result = $cache->destroy('name');
		
		$test->expects($result)->to()->be_type_of('boolean');
		$test->expects($result)->to()->equal(TRUE);
	});
	
	Test::add("Remove all cache files", function($test)
	{
		$cache = new Koi\Cache\Cache('file', array('directory' => CACHE_DIR));
		
		$cache->write('name' , 'Red Foreman');
		$cache->write('hobby', 'Kicking people in the ass');
		
		$result = $cache->destroy_all();
		
		$test->expects($result)->to()->be_type_of('boolean');
		$test->expects($result)->to()->equal(TRUE);
	});
});

Test::run_all();