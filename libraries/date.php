<?php
namespace Avalon;

class Date {
	public static function relative($date) {
		//return relative-formatted date like gmail
		//todo gmt and time zone offsets

		if (empty($date)) return ''; //return empty on error
		
		$date = strtotime($date);

		if (date('Y') == date('Y', $date)) {
			if ((date('n') == date('n', $date)) && (date('j') == date('j', $date))) {
				//today, return time eg 7:04 am	
				return date('g:i a', $date);
			} else {
				//this year, return date eg Jan 12
				return date('M d', $date);
			}
		} else {
			//not this year, return date eg 6/8/11
			return date('n/j/y', $date);
		}
	}

	public static function format($date) {
		//return relative-formatted date like gmail
		//todo gmt and time zone offsets

		if (empty($date)) return ''; //return empty on error
		
		$date = strtotime($date);

		return date('n/j/y', $date);
	}
}