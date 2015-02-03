<?php

class Dates {

	//take a mysql date string and return a relative date from it
	public static function relative($string) {
		if (empty($string)) return '';
		return \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $string)->diffForHumans();
	}

	public static function absolute($string) {
		if (empty($string)) return '';
		return date('M d, Y', strtotime($string));
	}

	public static function time($string) {
		if (empty($string)) return '';
		return date('g:i A', strtotime($string));
	}

}