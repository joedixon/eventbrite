<?php

/**
 * Eventbrite for EE config file
 *
 * @package        eventbrite
 * @author         Graze Media <hello@grazemedia.com>
 * @copyright      Copyright (c) 2013, Graze Media
 */

if ( ! defined('EVENTBRITE_NAME'))
{
	define('EVENTBRITE_NAME',    'Eventbrite for EE');
	define('EVENTBRITE_PACKAGE', 'eventbrite_ee');
	define('EVENTBRITE_VERSION', '0.0.1');
	define('EVENTBRITE_DOCS',    'http://grazemedia.com/');
	define('EVENTBRITE_APP_KEY', 'KO4EXY7NGWQLBA5QNT');
}

/**
 * < EE 2.6.0 backward compat
 */
if ( ! function_exists('ee'))
{
	function ee()
	{
		static $EE;
		if ( ! $EE) $EE = get_instance();
		return $EE;
	}
}