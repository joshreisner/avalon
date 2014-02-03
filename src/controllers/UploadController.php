<?php

class UploadController extends \BaseController {

	public function image() {
		if (Input::hasFile('image') && Input::has('field_id')) {

			//get field info
			$field 	= DB::table('avalon_fields')->where('id', Input::get('field_id'))->first();
			$object = DB::table('avalon_objects')->where('id', $field->object_id)->first();
			$unique	= Str::random();

			//make file name
			$dir 	= '/' . implode('/', array(
				'packages',
				'joshreisner',
				'avalon',
				'uploads',
				$object->name,
				$field->name,
				$unique,
			));

			mkdir(public_path() . $dir, 0777, true);

			$file = $dir . '/' . Str::slug(Input::file('image')->getClientOriginalName(), '-');

			//handle extension if exists
			if ($ext = strtolower(Input::file('image')->getClientOriginalExtension())) {
				$file_length = strlen($file);
				$ext_length = strlen($ext);
				if (substr($file, $file_length - $ext_length, $ext_length) == $ext) {
					$file = substr($file, 0, $file_length - $ext_length);
				}
				$file .= '.' . $ext;
			}

			Intervention\Image\Image::make(file_get_contents(Input::file('image')))
				->resize($field->width, $field->height, true)
				->save(public_path() . '/' . $file);

			/*push it over to s3
			$target = Str::random() . '/' . Input::file('image')->getClientOriginalName();
			$bucket = 'josh-reisner-dot-com';
			AWS::get('s3')->putObject(array(
			    'Bucket'     => $bucket,
			    'Key'        => $target,
			    'SourceFile' => $temp,
			));
			unlink($file);
			return 'https://s3.amazonaws.com/' . $bucket . '/' . $target;
			*/

			return $file;
		} elseif (!Input::hasFile('image')) {
			return 'no image';
		} elseif (!Input::hasFile('field_id')) {
			return 'no field_id';
		} else {
			return 'neither image nor field_id';			
		}
	}

}