<?php

namespace Foo;

class E
{
	public $config;

	public function __construct(A $a, B $b, $config = array())
	{
		$this->config = $config;
	}
}
