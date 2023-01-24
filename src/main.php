<?php

/**
 * @param string $input
 *
 * @return bool
 * @throws Exception for empty input
 */
function stringIsValid(string $input): bool
{
	if (empty($input))
	{
		throw new \http\Exception\InvalidArgumentException("input is empty!");
	}

	$map = new Ds\Map();
	foreach (str_split(allowableBrackets("open")) as $char)
	{
		$map[$char] = new Ds\Set();
	}

	$inputOnlyWithBrackets = leaveOnlyBrackets($input);

	foreach (str_split($inputOnlyWithBrackets) as $key => $bracket)
	{
		//если нашли открывающуюся скобку, то увеличиваем счетчик
		if ($map->hasKey($bracket))
		{
			$map[$bracket]->add($key);
		}
		//если нашли закрывающуюся скобку, то проверяем, есть ли открывающаяся пара
		elseif ($map[getOpenPairForCloseBracket($bracket)]->isEmpty())
		{
			return false;
		}
		else
		{
			$positionOpenPairForCloseBracket = $map[getOpenPairForCloseBracket($bracket)]->last();

			$lengthBetweenOpenAndCloseBracket = $key - $positionOpenPairForCloseBracket;

			if ($lengthBetweenOpenAndCloseBracket !== 1)
			{
				$checkBetweenOpenAndCloseBracket = substr(
					$inputOnlyWithBrackets,
					$positionOpenPairForCloseBracket + 1,
					$lengthBetweenOpenAndCloseBracket - 1
				);
				if (!stringIsValid($checkBetweenOpenAndCloseBracket))
				{
					return false;
				}
			}
			$map[getOpenPairForCloseBracket($bracket)]->remove($positionOpenPairForCloseBracket);
		}
	}

	$map = $map->toArray();
	foreach ($map as $openBracket)
	{
		if (!$openBracket->isEmpty())
		{
			return false;
		}
	}

	return true;
}

function getOpenPairForCloseBracket(string $char): string
{
	$charPosition = (stripos(allowableBrackets(), $char));
	if (!$charPosition)
	{
		throw new \http\Exception\InvalidArgumentException("Not allowable symbol");
	}

	$positionPairOpenBracket = $charPosition - 1;

	return allowableBrackets()[$positionPairOpenBracket];
}

/*
 * Если $option = "open", функция вернет лишь открытые скобки
 * Если $option = "closed", функция вернет лишь закрытые скобки
 */
function allowableBrackets(string $option = ""): string
{
	static $config = null;

	if ($config === null)
	{
		require "config.php";
	}

	$allowableBrackets = "";

	switch ($option)
	{
		case "open":
		{
			foreach ($config["ALLOWABLE_BRACKETS"] as $value)
			{
				$allowableBrackets .= implode($value)[0];
			}
			break;
		}
		case "close":
		{
			foreach ($config["ALLOWABLE_BRACKETS"] as $value)
			{
				$allowableBrackets .= implode($value)[1];
			}
			break;
		}
		default:
		{
			foreach ($config["ALLOWABLE_BRACKETS"] as $value)
			{
				$allowableBrackets .= implode($value);
			}
		}
	}

	return $allowableBrackets;
}

function leaveOnlyBrackets(string $input): string
{
	$inputOnlyWithBrackets = "";
	foreach (str_split($input) as $char)
	{
		if (stripos(allowableBrackets(), $char) !== false)
		{
			$inputOnlyWithBrackets .= $char;
		}
	}

	return $inputOnlyWithBrackets;
}