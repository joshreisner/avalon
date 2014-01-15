<?php

class UploadController extends \BaseController {

	public function image() {
		if (Input::hasFile('image')) {
			//get field info & manipulate image using Intervention
			$temp = public_path() . '/temp.dat';
			$field = DB::table('avalon_fields')->where('id', Input::get('field_id'))->first();
			$image = Image::make(file_get_contents(Input::file('image')))
				->resize($field->width, $field->height, true)
				->save($temp);

			//push it over to s3
			$target = Str::random() . '/' . Input::file('image')->getClientOriginalName();
			$bucket = 'josh-reisner-dot-com';
			AWS::get('s3')->putObject(array(
			    'Bucket'     => $bucket,
			    'Key'        => $target,
			    'SourceFile' => $temp,
			));
			unlink($temp);

			//return filename
			return 'https://s3.amazonaws.com/' . $bucket . '/' . $target;
		} else {
			return 'no image';
		}
	}

}