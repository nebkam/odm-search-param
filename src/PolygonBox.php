<?php

namespace Nebkam\OdmSearchParam;

use GeoJson\Geometry\Polygon;

class PolygonBox
	{
	public static function create(float $minLon, float $minLat, float $maxLon, float $maxLat): Polygon
		{
		return new Polygon([
			[
				[$minLon, $minLat],  // bottom-left
				[$maxLon, $minLat],  // bottom-right
				[$maxLon, $maxLat],  // top-right
				[$minLon, $maxLat],  // top-left
				[$minLon, $minLat],  // close loop
			]
		]);
		}
	}