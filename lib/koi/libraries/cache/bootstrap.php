<?php

Koi\Autoloader::add('Koi\Cache\Cache'          , KOI_PATH . '/libraries/cache/cache.php');
Koi\Autoloader::add('Koi\Cache\File'           , KOI_PATH . '/libraries/cache/drivers/file.php');
Koi\Autoloader::add('Koi\Cache\APC'            , KOI_PATH . '/libraries/cache/drivers/apc.php');
Koi\Autoloader::add('Koi\Cache\Redis'          , KOI_PATH . '/libraries/cache/drivers/redis.php');

Koi\Autoloader::add('Koi\Cache\CacheInterface'     , KOI_PATH . '/libraries/cache/interface.php');
Koi\Autoloader::add('Koi\Exception\CacheException' , KOI_PATH . '/libraries/cache/exceptions/cache_exception.php');