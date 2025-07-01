<?php

namespace RipDB;

enum Theme: string
{
	case Light = 'light';
	case Dark = 'dark';
	case Gadget = 'gadget';
	case Voice = 'voice';

	static function getThemes(): array
	{
		$themes = [];
		foreach (self::cases() as $theme) {
			$themes[$theme->name] = $theme->value;
		}

		return $themes;
	}
}
