<?php

class FileController extends \BaseController {

	public function image() {
		if (Input::hasFile('image') && Input::has('field_id')) {

			//get field info
			$field 	= DB::table(Config::get('avalon::db_fields'))->where('id', Input::get('field_id'))->first();
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
			$name = Str::slug(Input::file('image')->getClientOriginalName(), '-');
			if ($extension = strtolower(Input::file('image')->getClientOriginalExtension())) {
				$name = substr($name, 0, strlen($name) - strlen($extension));
			}

			//process and save image
			if (!empty($field->width) || !empty($field->height)) {
				Intervention\Image\Image::make(file_get_contents(Input::file('image')))
					->fit($field->width, $field->height)
					->save(public_path() . $path . '/' . $name . '.' . $extension);
			} else {
				Intervention\Image\Image::make(file_get_contents(Input::file('image')))
					->save(public_path() . $path . '/' . $name . '.' . $extension);
				list($width, $height, $type, $attr) = getimagesize(public_path() . $path . '/' . $name . '.' . $extension);
				$field->width = $width;
				$field->height = $height;
			}

			$size = filesize(public_path() . $path . '/' . $name . '.' . $extension);

			//insert record for image
			$file_id = DB::table(Config::get('avalon::db_files'))->insertGetId(array(
				'field_id'	=>$field->id,
				'path'		=>$path,
				'name'		=>$name,
				'extension'	=>$extension,
				'url'		=>$path . '/' . $name . '.' . $extension,
				'width'		=>$field->width,
				'height'	=>$field->height,
				'size'		=>$size,
				'writable'	=>1,
				'updated_by'=>Auth::user()->id,
				'updated_at'=>new DateTime,
				'precedence'=>DB::table(Config::get('avalon::db_files'))->where('field_id', $field->id)->max('precedence') + 1,
			));

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

			return json_encode(array(
				'file_id'=>$file_id, 
				'url'=>$path . '/' . $name . '.' . $extension,
				'width'=>$field->width,
				'height'=>$field->height,
			));
		} elseif (!Input::hasFile('image')) {
			return 'no image';
		} elseif (!Input::hasFile('field_id')) {
			return 'no field_id';
		} else {
			return 'neither image nor field_id';			
		}
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