<?php
namespace Avalon;

class Date {
	public static function format($value) {

		//adjust GMT offset and return relative-formatted date
		
		$date = strtotime($value);
		return '<span class="date">' . date('M d, Y', $date) . '</span>';
	}
}