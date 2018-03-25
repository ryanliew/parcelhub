<?php

namespace App;

class Utilities
{
	public static function convertMeterCubeToCentimeterCube($value)
	{
		return $value * 1000000;
	}

	public static function convertCentimeterCubeToMeterCube($value)	
	{
		return $value / 1000000;
	}
}