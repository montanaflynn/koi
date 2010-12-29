<?php
require_once __DIR__ . '/../../helper.php';

use Waffles\Test as Test;

Test::group("Test the APC cache", function()
{
	Test::add("Create a new instance of the APC cache", function($test)
	{
		$cache = new Koi\Cache\Cache('apc');
		
		$test->expects($cache)->to()->be_type_of('object');
	});
	
	Test::add("Write some data to the cache", function($test)
	{
		$cache   = new Koi\Cache\Cache('apc');
		$results = $cache->write('name', 'Yorick Peterse');
		
		$test->expects($results)->to()->be_type_of('boolean');
		$test->expects($results)->to()->equal(TRUE);
	});
	
	Test::add("Retrieve data from the cache", function($test)
	{
		$cache   = new Koi\Cache\Cache('apc');
		$results = $cache->read('name');
	
		$test->expects($results)->to()->be_type_of('string');
		$test->expects($results)->to()->equal('Yorick Peterse');
	});
	
	Test::add("Test the expiration of a cache record", function($test)
	{
		$cache   = new Koi\Cache\Cache('apc');
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
	
	Test::add("Remove a cache record", function($test)
	{
		$cache  = new Koi\Cache\Cache('apc');
		$cache->write('name', 'Chuck Norris');
		$result = $cache->destroy('name');
		
		$test->expects($result)->to()->be_type_of('boolean');
		$test->expects($result)->to()->equal(TRUE);
	});
	
	Test::add("Remove all cache records", function($test)
	{
		$cache = new Koi\Cache\Cache('apc');
		
		$cache->write('name' , 'Red Foreman');
		$cache->write('hobby', 'Kicking people in the ass');
		
		$result = $cache->destroy_all();
		
		$test->expects($result)->to()->be_type_of('boolean');
		$test->expects($result)->to()->equal(TRUE);
	});
});

Test::run_all();