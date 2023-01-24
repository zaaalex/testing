<?php

use PHPUnit\Framework\TestCase;

class MainTest extends TestCase
{
	/**
	 * @dataProvider differentBrackets
	 * @dataProvider forgotOpenBrackets
	 * @dataProvider forgotCloseBrackets
	 * @dataProvider complexImplementation
	 * @throws Exception
	 */
	public function testMainWorksCorrectly(bool $a, string $b): void
	{
		$this->assertEquals($a, stringIsValid($b));
	}

	public function differentBrackets(): array
	{
		return [
			[true, "sdgs[]"],
			[true, "ds(g)"],
			[true, "d<s(g)>"],
			[true, "d[s](g)"],
			[true, "<>ds(g)"],
		];
	}

	public function forgotOpenBrackets(): array
	{
		return [
			[false, "sd<gs]dfg>"],
			[false, "ds>(g)"],
			[false, "<(>dsg"],
			[false, ">ds(g)"],
		];
	}

	public function forgotCloseBrackets(): array
	{
		return [
			[false, "sd<gs[dfg"],
			[false, "<ds(g"],
			[false, "<>ds(g"],
			[false, "<s(g)"],
		];
	}

	public function complexImplementation(): array
	{
		return [
			[true, "sd<gs[d](f)g>"],
			[false, "sd<gs[d(]f)g>"],
			[false, "sd<((g)s[d()]f)g)>"],
			[true, "(s<dfsd(<>)>)"],
		];
	}

}