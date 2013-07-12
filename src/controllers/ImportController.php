<?php

class ImportController extends \BaseController {

	public static function wordpress() {

		//set up
		$categories = $tags = array();

		//clear out database
		DB::table('posts')->truncate();
		DB::table('post_tag')->truncate();
		DB::table('tags')->truncate();
		DB::table('categories')->truncate();

		//load XML array
		$file = file_get_contents('/Users/joshreisner/Desktop/joshreisner.wordpress.2013-07-12.xml');
		$file = str_replace('wp:', '', $file);
		$file = str_replace('content:encoded', 'content', $file);
		$file = new SimpleXMLElement($file);

		//loop through categories and add them
		$precedence = 1;
		foreach ($file->channel->category as $category) {
			$instance_id = DB::table('categories')->insertGetId(array(
				'slug'=>$category->category_nicename,
				'title'=>$category->cat_name,
				'updated_at'=>new DateTime,
				'updated_by'=>Session::get('avalon_id'),
				'precedence'=>$precedence++,
			));
			$categories[(string)$category->category_nicename] = $instance_id;
		}

		//loop through tags and add them
		$precedence = 1;
		foreach ($file->channel->tag as $tag) {
			$instance_id = DB::table('tags')->insertGetId(array(
				'slug'=>$tag->tag_slug,
				'title'=>$tag->tag_name,
				'updated_at'=>new DateTime,
				'updated_by'=>Session::get('avalon_id'),
				'precedence'=>$precedence++,
			));
			$tags[(string)$tag->tag_slug] = $instance_id;
		}

		//loop through posts & add them
		$precedence = 1;
		foreach ($file->channel->item as $item) {
			
			if (($item->post_type == 'post') && ($item->status == 'publish')) {

				//get category id
				$category_id = 0;

				foreach ($item->category as $category) {
					if (($category['domain'] == 'category') && isset($categories[(string)$category['nicename']])) {
						$category_id = $categories[(string)$category['nicename']];
					}
				}

				DB::table('posts')->insert(array(
					'title'			=>$item->title,
					'slug'			=>$item->post_name,
					'content'		=>$item->content,
					'published'		=>$item->post_date_gmt,
					'category_id'	=>$category_id,
					'updated_at'	=>new DateTime,
					'updated_by'	=>Session::get('avalon_id'),
					'precedence'	=>$precedence++,
				));
			}
		
		}

	}

}