<?php

namespace Diphy\Cache;

class NullCache implements ICache
{

	public function offsetGet($key) {}
	public function offsetSet($key, $data) {}
	public function offsetExists($key) { return FALSE; }
	public function offsetUnset($key) {}

}
