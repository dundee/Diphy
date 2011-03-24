<?php

namespace Diphy\Parser;

class ScriptParser
{
	private $tokens;
	private $index = 0;
	private $namespace;
	private $className;
	private $interfaces;
	private $uses;

	private $debug = 0;

	public function run($content)
	{
		$this->tokens = token_get_all($content);
		$this->namespace = $this->className;
		$this->interfaces = array();
		$this->uses = array();
		$this->index = 0;

		while ($this->index < count($this->tokens)) {
			$this->parseStatement();
		}

		$this->postProcess();
	}

	public function getInterfaces()
	{
		return $this->interfaces;
	}

	public function getClassName()
	{
		return $this->namespace . '\\' . $this->className;
	}

	private function postProcess()
	{
		$classes = array();
		foreach ($this->uses as $use) {
			$className = key($use);
			$parts = explode('\\', current($use));
			$name = end($parts);

			$classes[$name] = $className;
		}

		foreach ($this->interfaces as &$interface) {
			if (isset($classes[$interface])) {
				$interface = $classes[$interface];
			} else {
				$interface = $this->namespace . '\\' . $interface;
			}
		}
	}

	private function debug($msg)
	{
		if ($this->debug) echo 'DEBUG: ' . $msg . "\n";
	}

	private function show()
	{
		while ($this->index < count($this->tokens)) {
			if (is_array($this->tokens[$this->index])) {
				switch ($this->tokens[$this->index][0]) {
					case T_WHITESPACE:
					case T_END_HEREDOC:
					case T_DOC_COMMENT:
					case T_COMMENT:
						break;
					default:
						return $this->tokens[$this->index];
				}
			} else {
				return array($this->tokens[$this->index], $this->tokens[$this->index]);
			}
			$this->index++;
		}
	}

	private function showType()
	{
		$cur = $this->show();
		return $cur[0];
	}

	private function showValue()
	{
		$cur = $this->show();
		return $cur[1];
	}

	private function compare($type = NULL, $value = NULL)
	{
		$current = $this->show();
		if (is_array($current)) {
			if ($type && $type != $current[0]) throw new \Exception(sprintf('Expected type %s but (%s, %s) given', $type, $current[0], $current[1]));
			if ($value && $value != $current[1]) throw new \Exception(sprintf('Expected value %s but %s given', $value, $current[1]));
		} else {
			if ($type && $type != $current) throw new \Exception(sprintf('Expected type %s but %s given', $type, $current));
		}
		$this->index++;
	}

	private function parseStatement()
	{
		switch ($this->showType()) {
			case T_NAMESPACE:
				$this->parseNamespace();
				break;
			case T_USE:
				$this->parseUse();
				break;
			case T_CLASS:
			case T_ABSTRACT:
			case T_FINAL:
				$this->parseClass();
				break;
			default:
				$this->compare();
		}
	}

	private function parseNamespace()
	{
		$this->debug('namespace');
		$this->compare(T_NAMESPACE);
		$this->namespace = $this->parseIdentifier();
		$this->compare(';');
		$this->debug('/namespace');
	}

	private function parseUse()
	{
		$this->debug('use');
		$this->compare(T_USE);

		$key = $this->parseIdentifier();

		if ($this->showType() == T_AS) {
			$this->compare(T_AS);
			$value = $this->parseIdentifier();
		} else {
			$value = $key;
		}
		$this->uses[] = array($key => $value);

		$this->compare(';');

		$this->debug('/use');
	}

	private function parseIdentifier()
	{
		$name = '';
		while ($this->showType() == T_STRING ||
		       $this->showType() == T_NS_SEPARATOR) {
			$name .= $this->showValue();
			$this->compare();
		}
		return $name;
	}

	private function parseClass()
	{
		$this->debug('class');
		if ($this->showType() == T_ABSTRACT) $this->compare(T_ABSTRACT);
		if ($this->showType() == T_FINAL) $this->compare(T_FINAL);

		$this->compare(T_CLASS);
		$this->className = $this->showValue();
		$this->compare(T_STRING);

		if ($this->showType() == T_IMPLEMENTS) $this->parseImplements();
		if ($this->showType() == T_EXTENDS) $this->parseExtends();

		$this->compare('{');

		$this->debug('/class');
	}

	private function parseImplements()
	{
		$this->debug('implements');
		$this->compare(T_IMPLEMENTS);

		while (TRUE) {
			$this->interfaces[] = $this->showValue();
			$this->compare(T_STRING);
			if ($this->showType() == ',') {
				$this->compare(',');
				continue;
			}
			break;
		}
		$this->debug('/implements');
	}

	private function parseExtends()
	{
		$this->debug('extends');
		$this->compare(T_EXTENDS);

		while (TRUE) {
			$this->compare(T_STRING);
			if ($this->showType() == ',') {
				$this->compare(',');
				continue;
			}
			break;
		}
		$this->debug('/extends');
	}

}
