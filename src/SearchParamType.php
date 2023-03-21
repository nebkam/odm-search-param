<?php

namespace Nebkam\OdmSearchParam;

enum SearchParamType
	{
	case Bool;
	case Exists;
	case Int;
	case IntArray;
	case IntEnum;
	case IntEnumArray;
	case IntGt;
	case Range;
	case RangeFloat;
	case RangeInt;
	case RangeIntEnum;
	case String;
	case StringArray;
	case StringEnum;
	case StringEnumArray;
	case VirtualBool;
	}
