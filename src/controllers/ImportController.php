<?php

class ImportController extends \BaseController {

	public static function wordpress() {

		//set up
		$categories = $tags = $posts = array();

		//clear out database
		DB::table('posts')->truncate();
		DB::table('post_tag')->truncate();
		DB::table('tags')->truncate();
		DB::table('categories')->truncate();
		DB::table('avalon_uploads')->truncate();

		//load XML array & deal with invalid xml characters
		$file = file_get_contents('/Users/joshreisner/Sites/joshreisner.wordpress.2013-07-08.xml');
		$file = str_replace('wp:', '', $file);
		$file = str_replace(':encoded>', '>', $file);
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
				$category_id = null;
				foreach ($item->category as $category) {
					if (($category['domain'] == 'category') && isset($categories[(string)$category['nicename']])) {
						$category_id = $categories[(string)$category['nicename']];
					}
				}

				$item->content = self::strip_shortcodes($item->content);

				//save post
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

				//remember wp post id
				$posts['post' . $item->post_id] = $post_id;

			}
		
		}

		//loop through attachments
		foreach ($file->channel->item as $item) {
			
			if (($item->post_type == 'attachment') && (!empty($posts['post' . $item->post_parent]))) {

				$meta = @unserialize($item->postmeta[1]->meta_value);
				if (!isset($meta['width'])) $meta['width'] = null;
				if (!isset($meta['height'])) $meta['height'] = null;

				DB::table('avalon_uploads')->insert(array(
					'table'			=>'posts',
					'instance_id'	=>$posts['post' . $item->post_parent],
					'title'			=>$item->excerpt,
					'url'			=>$item->attachment_url,
					'extension'		=>pathinfo($item->attachment_url, PATHINFO_EXTENSION),
					'width'			=>$meta['width'],
					'height'		=>$meta['height'],
					'updated_at'	=>new DateTime,
					'updated_by'	=>Session::get('avalon_id'),
					'precedence'	=>$item->menu_order,
				));

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

		echo 'done';

	}


	private static function nl2p($string) {
	    //remove existing HTML formatting to avoid double-wrapping things
	    $nl = "\n";

	    $string = str_replace(array('<p>', '</p>', '<br>', '<br/>', '<br />'), '', trim($string));
	    
        $string = preg_replace(array('/([' . $nl . ']{2,})/i', '/([^>])' . $nl . '([^<])/i'), array('</p>' . $nl . '<p>', '<br>'), $string);

        return '<p>' . $string . '</p>';
	} 

	private static function get_shortcode_regex() {

		$tagregexp = join( '|', array_map('preg_quote', array('caption', 'audio', 'gallery')) );

		// WARNING! Do not change this regex without changing do_shortcode_tag() and strip_shortcode_tag()
		// Also, see shortcode_unautop() and shortcode.js.
		return
			  '\\['                              // Opening bracket
			. '(\\[?)'                           // 1: Optional second opening bracket for escaping shortcodes: [[tag]]
			. "($tagregexp)"                     // 2: Shortcode name
			. '(?![\\w-])'                       // Not followed by word character or hyphen
			. '('                                // 3: Unroll the loop: Inside the opening shortcode tag
			.     '[^\\]\\/]*'                   // Not a closing bracket or forward slash
			.     '(?:'
			.         '\\/(?!\\])'               // A forward slash not followed by a closing bracket
			.         '[^\\]\\/]*'               // Not a closing bracket or forward slash
			.     ')*?'
			. ')'
			. '(?:'
			.     '(\\/)'                        // 4: Self closing tag ...
			.     '\\]'                          // ... and closing bracket
			. '|'
			.     '\\]'                          // Closing bracket
			.     '(?:'
			.         '('                        // 5: Unroll the loop: Optionally, anything between the opening and closing shortcode tags
			.             '[^\\[]*+'             // Not an opening bracket
			.             '(?:'
			.                 '\\[(?!\\/\\2\\])' // An opening bracket not followed by the closing shortcode tag
			.                 '[^\\[]*+'         // Not an opening bracket
			.             ')*+'
			.         ')'
			.         '\\[\\/\\2\\]'             // Closing shortcode tag
			.     ')?'
			. ')'
			. '(\\]?)';                          // 6: Optional second closing brocket for escaping shortcodes: [[tag]]
	}

	private static function strip_shortcodes( $content ) {

		$pattern = self::get_shortcode_regex();

		return preg_replace_callback( "/$pattern/s", array('self', 'strip_shortcode_tag'), $content );
	}

	private static function strip_shortcode_tag( $m ) {
		// allow [[foo]] syntax for escaping a tag
		if ( $m[1] == '[' && $m[6] == ']' ) {
			return substr($m[0], 1, -1);
		}

		return $m[1] . $m[6];
	}



}