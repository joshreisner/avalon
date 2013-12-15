<?php

class Breadcrumbs {

	public static function leave($breadcrumbs) {
		$return = array();
		
		//prepend home
		$breadcrumbs = array_merge(array('/'=>'<i class="glyphicon glyphicon-home"></i>'), $breadcrumbs);
		
		//build breadcrumbs
		foreach ($breadcrumbs as $link=>$text) {
			$return[] = (is_string($link)) ? '<a href="' . $link . '">' . $text . '</a>' : $text;
		}
		
		return '<h1>' . implode(' <i class="glyphicon glyphicon-chevron-right"></i> ', $return) . '</h1>';
	}
	
}

