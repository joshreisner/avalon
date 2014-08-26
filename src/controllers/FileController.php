<?php

use Intervention\Image\ImageManagerStatic as Image;

class FileController extends \BaseController {

	/**
	 * handle image upload route
	 */
	public function image() {
		if (Input::hasFile('image') && Input::has('field_id')) {
			return json_encode(self::saveImage(
				Input::get('field_id'), 
				file_get_contents(Input::file('image')),
				Input::file('image')->getClientOriginalName()
			));

		} elseif (!Input::hasFile('image')) {
			return 'no image';
		} elseif (!Input::hasFile('field_id')) {
			return 'no field_id';
		} else {
			return 'neither image nor field_id';			
		}
	}

	/**
	 * genericized function to handle upload, available externally via service provider
	 */
	public static function saveImage($field_id, $file, $filename, $instance_id=null) {
		//get field info
		$field 	= DB::table(DB_FIELDS)->where('id', $field_id)->first();
		$object = DB::table(DB_OBJECTS)->where('id', $field->object_id)->first();
		$unique	= Str::random(5);

		//make path
		$path = '/' . implode('/', array(
			'packages',
			'joshreisner',
			'avalon',
			'files',
			$object->name,
			$field->name,
			$unique,
		));

		//also make path in the filesystem
		mkdir(public_path() . $path, 0777, true);

		//get name and extension
		$fileparts = pathinfo($filename);
		$filename	= Str::slug($fileparts['filename'], '-');
		$extension 	= strtolower($fileparts['extension']);
		$file_path 	= $path . '/' . $filename . '.' . $extension;

		//process and save image
		if (!empty($field->width) && !empty($field->height)) {
			Image::make($file)
				->fit((int)$field->width, (int)$field->height)
				->save(public_path() . $file_path);
		} elseif (!empty($field->width)) {
			Image::make($file)
				->widen((int)$field->width)
				->save(public_path() . $file_path);
		} elseif (!empty($field->height)) {
			Image::make($file)
				->heighten(null, (int)$field->height)
				->save(public_path() . $file_path);
		} else {
			Image::make($file)
				->save(public_path() . $file_path);
		}

		//get dimensions
		list($width, $height, $type, $attr) = getimagesize(public_path() . $file_path);

		//get size
		$size = filesize(public_path() . $file_path);

		//insert record for image
		$file_id = DB::table(DB_FILES)->insertGetId(array(
			'field_id' =>		$field->id,
			'instance_id' =>	$instance_id,
			'path' =>			$path,
			'name' =>			$filename,
			'extension' =>		$extension,
			'url' =>			$file_path,
			'width' =>			$width,
			'height' =>			$height,
			'size' =>			$size,
			'writable' =>		1,
			'updated_by' =>		Auth::user()->id,
			'updated_at' =>		new DateTime,
			'precedence' =>		DB::table(DB_FILES)->where('field_id', $field->id)->max('precedence') + 1,
		));

		/*push it over to s3
		$target = Str::random() . '/' . Input::file('image')->getClientOriginalName();
		$bucket = 'josh-reisner-dot-com';
		AWS::get('s3')->putObject(array(
		    'Bucket' => 		$bucket,
		    'Key' => 			$target,
		    'SourceFile' => 	$temp,
		));
		unlink($file);
		return 'https://s3.amazonaws.com/' . $bucket . '/' . $target;
		*/

		list($screenwidth, $screenheight) = self::getImageDimensions($width, $height);

		//perhaps also screenwidth & screenheight?
		return array(
			'file_id' =>		$file_id, 
			'url' =>			$file_path,
			'width' =>			$width,
			'height' =>			$height,
			'screenwidth' =>	$screenwidth,
			'screenheight' =>	$screenheight,
		);
	}

	# Get display size for create and edit views
	public static function getImageDimensions($width=false, $height=false) {

		$max_width  = Config::get('avalon::image_max_width');
		$max_height = Config::get('avalon::image_max_height');
		$max_area   = Config::get('avalon::image_max_area');

		//too wide?
		if ($width && $width > $max_width) {
			if ($height) $height *= $max_width / $width;
			$width = $max_width;
		}

		//too tall?
		if ($height && $height > $max_height) {
			if ($width) $width *= $max_height / $height;
			$height = $max_height;
		}

		//not specified?
		if (!$width) $width = Config::get('avalon::image_default_width');
		if (!$height) $height = Config::get('avalon::image_default_height');

		//too large?
		$area = $width * $height;
		if ($width * $height > $max_area) {
			$width *= $max_area / $area;
			$height *= $max_area / $area;
		}

		return array($width, $height);
	}

	//todo amazon?
	public static function cleanup($files=false) {

		//by default, remove all non-instanced files
		if (!$files) $files = DB::table(DB_FILES)->whereNull('instance_id')->get();
		
		//try to physically remove
		$ids = array();
		foreach ($files as $file) {
			$ids[] = $file->id;
			if ($file->writable) {
				@unlink(public_path() . $file->path . '/' . $file->name . '.' . $file->extension);
				@rmdir(public_path() . $file->path);
			}
		}

		//remove records
		if (!empty($ids)) {
			DB::table(DB_FILES)->whereIn('id', $ids)->delete();
		}
	}
}