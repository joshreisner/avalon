<?php

class Dates {

	//take a mysql date string and return a relative date from it
	public static function relative($string) {
		return \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $string)->diffForHumans();
	}

	public static function absolute($string) {
		return date('M d, Y', strtotime($string));
	}

}