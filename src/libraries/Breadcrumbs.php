<?php

class Breadcrumbs {

	public static function leave($breadcrumbs) {
		$return = array();
		
		//prepend home
		$breadcrumbs = array_merge(array('/'=>'<i class="icon-home"></i>'), $breadcrumbs);
		
		//build breadcrumbs
		foreach ($breadcrumbs as $link=>$text) {
			$return[] = (is_string($link)) ? '<a href="' . $link . '">' . $text . '</a>' : $text;
		}
		
		return '<h1>' . implode(Config::get('avalon::breadcrumbs_separator'), $return) . '</h1>';
	}
	
}

