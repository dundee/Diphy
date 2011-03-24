<?php

namespace DiphyExample;

class Application
{
	private $request;

	public function __construct(IRequest $request)
	{
		$this->request = $request;
	}

	public function run()
	{
		echo '<h1>Hello world!</h1>';
	}
}
