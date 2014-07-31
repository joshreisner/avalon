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
				Input::file('image')->getClientOriginalName(),
				Input::file('image')->getClientOriginalExtension()
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
	public static function saveImage($field_id, $file, $filename, $extension, $instance_id=null) {
		//get field info
		$field 	= DB::table(Config::get('avalon::db_fields'))->where('id', $field_id)->first();
		$object = DB::table(Config::get('avalon::db_objects'))->where('id', $field->object_id)->first();
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
		$filename	= Str::slug($filename, '-');
		$extension 	= strtolower($extension);
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
		$file_id = DB::table(Config::get('avalon::db_files'))->insertGetId(array(
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
			'precedence' =>		DB::table(Config::get('avalon::db_files'))->where('field_id', $field->id)->max('precedence') + 1,
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

		return array(
			'file_id' =>		$file_id, 
			'url' =>			$file_path,
			'width' =>			$width,
			'height' =>			$height,
		);
	}

	//todo amazon?
	public static function cleanup($files=false) {

		//by default, remove all non-instanced files
		if (!$files) $files = DB::table(Config::get('avalon::db_files'))->whereNull('instance_id')->get();
		
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
			DB::table(Config::get('avalon::db_files'))->whereIn('id', $ids)->delete();
		}
	}
}