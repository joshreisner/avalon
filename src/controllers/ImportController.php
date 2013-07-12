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

				$post_id = DB::table('posts')->insertGetId(array(
					'title'			=>$item->title,
					'slug'			=>$item->post_name,
					'content'		=>self::nl2p($item->content),
					'published'		=>$item->post_date_gmt,
					'category_id'	=>$category_id,
					'updated_at'	=>new DateTime,
					'updated_by'	=>Session::get('avalon_id'),
					'precedence'	=>$precedence++,
				));

				//save tags
				foreach ($item->category as $category) {
					if (($category['domain'] == 'post_tag') && isset($tags[(string)$category['nicename']])) {
						$tag_id = $tags[(string)$category['nicename']];

						DB::table('post_tag')->insert(array(
							'post_id'=>$post_id,
							'tag_id'=>$tag_id,
						));
					}
				}

			}
		
		}

		//update post meta
		$objects = DB::table('avalon_objects')->get();
		foreach ($objects as $object) {
			DB::table('avalon_objects')->where('id', $object->id)->update(array(
				'instance_count'=>DB::table($object->name)->where('active', 1)->count(),
				'instance_updated_by'=>Session::get('avalon_id'),
				'instance_updated_at'=>new DateTime,
			));
		}

	}


private static function nl2p($string, $line_breaks = true, $xml = true)
{
    // Remove existing HTML formatting to avoid double-wrapping things
    $string = str_replace(array('<p>', '</p>', '<br>', '<br />'), '', $string);
    
    // It is conceivable that people might still want single line-breaks
    // without breaking into a new paragraph.
    if ($line_breaks == true)
        return '<p>'.preg_replace(array("/([\n]{2,})/i", "/([^>])\n([^<])/i"), array("</p>\n<p>", '<br'.($xml == true ? ' /' : '').'>'), trim($string)).'</p>';
    else 
        return '<p>'.preg_replace("/([\n]{1,})/i", "</p>\n<p>", trim($string)).'</p>';
}  

}