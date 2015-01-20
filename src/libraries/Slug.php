<?php

class Slug {

	//adapted from wordpress's list
	private static $stop_words = ['', 'a', 'about', 'above', 'after', 'again', 'against', 'all', 'am', 'an', 
		'and', 'any', 'are', 'arent', 'as', 'at', 'be', 'because', 'been', 'before', 'being', 'below', 
		'between', 'both', 'but', 'by', 'cant', 'cannot', 'could', 'couldnt', 'did', 'didnt', 'do', 'does', 
		'doesnt', 'dont', 'down', 'during', 'each', 'few', 'for', 'from', 'further', 'had', 'hadnt', 'has', 
		'hasnt', 'have', 'havent', 'having', 'he', 'hed', /*'hell',*/ 'hes', 'her', 'here', 'heres', 'hers', 
		'herself', 'him', 'himself', 'his', 'how', 'hows', 'i', 'id', /*'ill',*/ 'im', 'ive', 'if', 'in', 
		'into', 'is', 'isnt', 'it', 'its', 'itself', 'lets', 'me', 'more', 'most', 'mustnt', 'my', 'myself', 
		'no', 'nor', 'not', 'of', 'off', 'on', 'once', 'only', 'or', 'other', 'ought', 'our', 'ours', 
		'ourselves', 'out', 'over', 'own', 'same', 'shant', 'she', /*'shed', 'shell',*/ 'shes', 'should', 
		'shouldnt', 'so', 'some', 'such', 'than', 'that', 'thats', 'the', 'their', 'theirs', 'them', 
		'themselves', 'then', 'there', 'theres', 'these', 'they', 'theyd', 'theyll', 'theyre', 'theyve', 
		'this', 'those', 'through', 'to', 'too', 'under', 'until', 'up', 'very', 'was', 'wasnt', 'we', 
		/*'wed', 'well',*/ 'were', 'weve', 'werent', 'what', 'whats', 'when', 'whens', 'where', 'wheres', 
		'which', 'while', 'who', 'whod', 'whos', 'whom', 'why', 'whys', 'with', 'wont', 'would', 'wouldnt', 
		'you', 'youd', 'youll', 'youre', 'youve', 'your', 'yours', 'yourself', 'yourselves'];

	private static $limit = 50;

	private static $separator = '-';

	public static function make($string, $uniques=[]) {

		# Convert string into array of url-friendly words
		$words = explode('-', Str::slug($string, '-'));

		# Words should be unique
		$words = array_unique($words);

		# Remove stop words
		$words = array_diff($words, self::$stop_words);

		# Reset indexes
		$words = array_values($words);

		# Limit length of slug down to fewer than 50 characters
		while (self::checkLength($words) === false) array_pop($words);

		# Check uniqueness
		while (self::checkUniqueness($words, $uniques) === false) self::increment($words);

		return self::makeFromWords($words);

	}

	public static function source($object_id) {
		return DB::table(DB_FIELDS)
			->where('object_id', $object_id)
			->whereIn('type', ['string', 'text'])
			->orderBy('precedence')
			->pluck('name');
	}

	private static function makeFromWords($words) {
		return implode(self::$separator, $words);
	}

	private static function checkLength($words) {
		$counts = array_map('strlen', $words);
		return ((array_sum($counts) + count($words) - 1) <= self::$limit);
	}

	private static function increment(&$words) {
		if (empty($words)) {
			//if whole array is empty
			$words = [0];
		} elseif (empty($words[count($words) - 1])) {
			//if final element is empty
			$words[count($words) - 1] = 0;
		} elseif (!is_integer($words[count($words) - 1])) {
			//todo check to see if goes over length limit
			$words[] = 0;
		}
		$words[count($words) - 1]++;
	}

	private static function checkUniqueness($words, $uniques) {
		return !in_array(self::makeFromWords($words), $uniques);
	}

	public static function setForObject($object) {
		$slug_source = Slug::source($object->id);
		$instances = DB::table($object->name)->get();
		$slugs = [];
		foreach ($instances as $instance) {
			if ($slug_source === null) {
				$slug = Slug::make($instance->created_at->format('Y-m-d'), $slugs);
			} else {
				$slug = Slug::make($instance->{$slug_source}, $slugs);
			}
			DB::table($object->name)->where('id', $instance->id)->update(['slug'=>$slug]);
			$slugs[] = $slug;
		}
	}

}