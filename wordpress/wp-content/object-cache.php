<?php
//This file will be placed to /wp-content/

defined('ABSPATH') or die();

if (defined('WP_PLUGIN_DIR')) {
	$result = @include_once(WP_PLUGIN_DIR . '/em-object-cache/lib/CacheFactory.php');
}
else {
	$result = @include_once(WP_CONTENT_DIR . '/plugins/em-object-cache/lib/CacheFactory.php');
}

if (false === $result) {
	unset($result);
	require_once(ABSPATH . WPINC . '/cache.php');
}
else {
	unset($result);

	EMOCCacheFactory::registerEngine('basecache',      'EMOCBaseCache',         'BaseCache',         'array_merge',           0, 'BaseCache', true);
	EMOCCacheFactory::registerEngine('apc',            'EMOCApcCache',          'ApcCache',          'apc_store',             1, 'APC');
	EMOCCacheFactory::registerEngine('eaccelerator',   'EMOCEAcceleratorCache', 'eAcceleratorCache', 'eaccelerator_put',      1, 'eAccelerator');
	EMOCCacheFactory::registerEngine('filecache',      'EMOCFileCache',         'FileCache',         'file_put_contents',     1, 'FileCache');
	EMOCCacheFactory::registerEngine('xcache',         'EMOCxCache',            'xCache',            'xcache_set',            1, 'xCache');
	EMOCCacheFactory::registerEngine('zend_disk',      'EMOCZendDiskCache',     'ZendDiskCache',     'zend_disk_cache_store', 1, 'ZendDisk');
	EMOCCacheFactory::registerEngine('zend_shm',       'EMOCZendShmCache',      'ZendShmCache',      'zend_shm_cache_store',  1, 'ZendShm');
	EMOCCacheFactory::registerEngine('memcache',       'EMOCMemcache',          'Memcache',          'memcache_connect',      2, 'Memcache');
	EMOCCacheFactory::registerEngine('memcached',      'EMOCMemcached',         'Memcached',         'memcached',             2, 'Memcached');

	$GLOBALS['__emoc_options'] = array();
	@include(defined('WP_PLUGIN_DIR') ? (WP_PLUGIN_DIR . '/em-object-cache/options.php') : (WP_CONTENT_DIR . '/plugins/em-object-cache/options.php'));

	/**
	 * wp_cache_add() - Adds data to the cache, if the cache key doesn't aleady exist
	 *
	 * @uses $wp_object_cache Object Cache Class
	 * @param int|string $key The cache ID to use for retrieval later
	 * @param mixed $data The data to add to the cache store
	 * @param string $group The group to add the cache to
	 * @param int $expire When the cache data should be expired
	 * @return unknown
	 */
	function wp_cache_add($key, $data, $group = '', $expire = 0)
	{
		static $exists = null;
		if (null === $exists) {
			$exists = function_exists('wp_suspend_cache_addition');
		}

		if (!$exists || !wp_suspend_cache_addition()) {
			global $wp_object_cache;
			if (empty($group)) { $group = 'default'; }
			return $wp_object_cache->add($key, $data, $group, $expire);
		}

		return false;
	}

	/**
	 * wp_cache_close() - Closes the cache
	 *
	 * @return bool Always returns True
	 */
	function wp_cache_close()
	{
		global $wp_object_cache;
		$wp_object_cache->close();
		return true;
	}

	/**
	 * Decrement numeric cache item's value
	 *
	 * @uses $wp_object_cache Object Cache Class
	 * @param int|string $key The cache key to increment
	 * @param int $offset The amount by which to decrement the item's value. Default is 1.
	 * @param string $group The group the key is in.
	 * @return false|int False on failure, the item's new value on success.
	 */
	function wp_cache_decr($key, $offset = 1, $group = '')
	{
		global $wp_object_cache;
		if (empty($group)) { $group = 'default'; }
		return $wp_object_cache->decr($key, (int)$offset, $group);
	}

	/**
	 * wp_cache_delete() - Removes the cache contents matching ID and flag
	 *
	 * @uses $wp_object_cache Object Cache Class
	 * @param int|string $id What the contents in the cache are called
	 * @param string $group Where the cache contents are grouped
	 * @return bool True on successful removal, false on failure
	 */
	function wp_cache_delete($id, $group = '')
	{
		global $wp_object_cache;
		if (empty($group)) { $group = 'default'; }
		return $wp_object_cache->delete($id, $group);
	}

	/**
	 * wp_cache_flush() - Removes all cache items
	 *
	 * @uses $wp_object_cache Object Cache Class
	 * @return bool Always returns true
	 */
	function wp_cache_flush()
	{
		global $wp_object_cache;
		$wp_object_cache->flush();
		return true;
	}

	/**
	 * wp_cache_get() - Retrieves the cache contents from the cache by ID and flag
	 *
	 * @uses $wp_object_cache Object Cache Class
	 * @param int|string $id What the contents in the cache are called
	 * @param string $group Where the cache contents are grouped
	 * @param bool $force Whether to force an update of the local cache from the persistent cache (default is false)
	 * @param &bool $found Whether key was found in the cache. Disambiguates a return of false, a storable value.
	 * @return bool|mixed False on failure to retrieve contents or the cache contents on success
	 */
	function wp_cache_get($id, $group = '', $force = false, &$found = null)
	{
		global $wp_object_cache;
		if (empty($group)) { $group = 'default'; }
		return $wp_object_cache->get($id, $group, $force, $found);
	}

	/**
	 * Increment numeric cache item's value
	 *
	 * @uses $wp_object_cache Object Cache Class
	 * @param int|string $key The cache key to increment
	 * @param int $offset The amount by which to increment the item's value. Default is 1.
	 * @param string $group The group the key is in.
	 * @return false|int False on failure, the item's new value on success.
	 */
	function wp_cache_incr($key, $offset = 1, $group = '')
	{
		global $wp_object_cache;
		if (empty($group)) { $group = 'default'; }
		return $wp_object_cache->incr($key, (int)$offset, $group);
	}

	/**
	 * Sets up Object Cache Global and assigns it.
	 *
	 * @global WP_Object_Cache $wp_object_cache WordPress Object Cache
	 */
	function wp_cache_init()
	{
		static $initialized = false;
		if ($initialized) {
			wp_cache_reset();
			return;
		}

		$initialized = true;

		global $__emoc_options;
		if (empty($__emoc_options)) {
			$__emoc_options['engine'] = 'basecache';
		}

		if (!isset($_SERVER['HTTP_HOST'])) {
			$_SERVER['HTTP_HOST'] = null;
			$__emoc_options['persist'] = false;
		}

		$GLOBALS['wp_object_cache'] = EMOCCacheFactory::get($__emoc_options);

		if (!empty($options['nonpersistent'])) {
			$np = explode(',', $options['nonpersistent']);
			if (!empty($np)) {
				wp_cache_add_non_persistent_groups($np);
			}
		}
	}

	/**
	 * @uses $wp_object_cache Object Cache Class
	 *
	 * Reset internal cache keys and structures. If the cache backend uses global blog or site IDs as part of its cache keys,
	 * this function instructs the backend to reset those keys and perform any cleanup since blog or site IDs have changed since cache init.
	 */
	function wp_cache_reset()
	{
		global $wp_object_cache;
		$wp_object_cache->reset();
	}

	/**
	 * wp_cache_replace() - Replaces the contents of the cache with new data
	 *
	 * @uses $wp_object_cache Object Cache Class
	 * @param int|string $id What to call the contents in the cache
	 * @param mixed $data The contents to store in the cache
	 * @param string $group Where to group the cache contents
	 * @param int $expire When to expire the cache contents
	 * @return bool False if cache ID and group already exists, true on success
	 */
	function wp_cache_replace($key, $data, $group = '', $expire = 0)
	{
		global $wp_object_cache;
		if (empty($group)) { $group = 'default'; }
		return $wp_object_cache->replace($key, $data, $group, $expire);
	}

	/**
	 * wp_cache_set() - Saves the data to the cache
	 *
	 * @uses $wp_object_cache Object Cache Class
	 * @param int|string $id What to call the contents in the cache
	 * @param mixed $data The contents to store in the cache
	 * @param string $group Where to group the cache contents
	 * @param int $expire When to expire the cache contents
	 * @return bool False if cache ID and group already exists, true on success
	 */
	function wp_cache_set($key, $data, $group = '', $expire = 0)
	{
		global $wp_object_cache;
		if (empty($group)) { $group = 'default'; }
		return $wp_object_cache->set($key, $data, $group, $expire);
	}

	/**
	 * Switch the interal blog id.
	 *
	 * This changes the blog id used to create keys in blog specific groups.
	 *
	 * @uses $wp_object_cache Object Cache Class
	 * @param int $blog_id Blog ID
	 */
	function wp_cache_switch_to_blog($blog_id)
	{
		global $wp_object_cache;
		return $wp_object_cache->switch_to_blog((int)$blog_id);
	}

	/**
	 * Adds a group or set of groups to the list of global groups.
	 *
	 * @uses $wp_object_cache Object Cache Class
	 * @param string|array $groups A group or an array of groups to add
	 */
	function wp_cache_add_global_groups($groups)
	{
		global $wp_object_cache;
		if (!is_array($groups)) {
			$groups = array($groups);
		}

		$wp_object_cache->add_global_groups($groups);
	}

	/**
	 * Adds a group or set of groups to the list of non-persistent groups.
	 *
	 * @uses $wp_object_cache Object Cache Class
	 * @param string|array $groups A group or an array of groups to add
	 */
	function wp_cache_add_non_persistent_groups($groups)
	{
		global $wp_object_cache;
		if (!is_array($groups)) {
			$groups = array($groups);
		}

		$wp_object_cache->add_non_persistent_groups($groups);
	}
}
