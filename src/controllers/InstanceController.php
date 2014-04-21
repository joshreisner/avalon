<?php
use Aws\Common\Enum\Region;
use Aws\Laravel\AwsServiceProvider;
use Illuminate\Foundation\Application;

class InstanceController extends \BaseController {

	//show list of instances for an object
	public function index($object_id) {

		//get more info about the object
		$object = DB::table(Config::get('avalon::db_prefix') . 'objects')->where('id', $object_id)->first();
		$object->nested = false;
		$fields = DB::table(Config::get('avalon::db_prefix') . 'fields')
			->where('object_id', $object_id)
			->where('visibility', 'list')
			->orWhere('id', $object->group_by_field)
			->orderBy('precedence')->get();
		$selects = array($object->name . '.id', $object->name . '.updated_at', $object->name . '.deleted_at');
		$instances = DB::table($object->name);
		foreach ($fields as $field) {
			$selects[] = $object->name . '.' . $field->name;
			if ($field->type == 'image') {
				$instances->leftJoin(Config::get('avalon::db_prefix') . 'files', $object->name . '.' . $field->name, '=', Config::get('avalon::db_prefix') . 'files.id');
				$selects[] = Config::get('avalon::db_prefix') . 'files.url as ' . $field->name . '_url';
			}
		}
		$instances->select($selects);

		//group by field?
		if (!empty($object->group_by_field)) {
			$grouped_field = DB::table(Config::get('avalon::db_prefix') . 'fields')->where('id', $object->group_by_field)->first();
			$grouped_object = DB::table(Config::get('avalon::db_prefix') . 'objects')->where('id', $grouped_field->related_object_id)->first();
			if ($grouped_object->id == $object->id) {
				//nested object
				$object->nested = true;
			} else {
				//pull group_by_field out of the list of fields so it's not a column in the table
				foreach ($fields as $key=>$field) {
					if ($field->id == $object->group_by_field) unset($fields[$key]);
				}

				$instances = $instances->
					leftJoin($grouped_object->name, $object->name . '.' . $grouped_field->name, '=', $grouped_object->name . '.id')
					->orderBy($grouped_object->name . '.' . $grouped_object->order_by, $grouped_object->direction)
					->addSelect($grouped_object->name . '.title as group');
			}
		}

		$instances = $instances->orderBy($object->name . '.' . $object->order_by, $object->direction)->get();
		
		//per-type modifications to table output
		foreach ($instances as &$instance) {
			$instance->link = URL::action('InstanceController@edit', array($object->id, $instance->id));
			$instance->delete = URL::action('InstanceController@delete', array($object->id, $instance->id));
		}
		
		if ($object->nested) {
			$list = array();
			foreach ($instances as &$instance) {
				$instance->children = array();
				if (empty($instance->{$grouped_field->name})) { //$grouped_field->name is for ex parent_id
					$list[] = $instance;
					//echo 'empty';
				} elseif (self::nestedNodeExists($list, $instance->{$grouped_field->name}, $instance)) {
					//attached child to parent node
					//echo 'exists';
				} else {
					//an error occurred, because a parent exists but is not in the tree
					//echo 'error';
				}
			}
			$instances = $list;
		}

		return View::make('avalon::instances.index', array(
			'object'=>$object, 
			'fields'=>$fields, 
			'instances'=>$instances,
		));
	}

	//show create form for an object instance
	public function create($object_id) {
		$object = DB::table(Config::get('avalon::db_prefix') . 'objects')->where('id', $object_id)->first();
		$fields = DB::table(Config::get('avalon::db_prefix') . 'fields')->where('object_id', $object_id)->orderBy('precedence')->get();
		$options = array();
		
		foreach ($fields as $field) {
			if (($field->type == 'checkboxes') || ($field->type == 'select')) {

				//load options for checkboxes or selects
				$related_object = DB::table(Config::get('avalon::db_prefix') . 'objects')->where('id', $field->related_object_id)->first();
				$related_field  = DB::table(Config::get('avalon::db_prefix') . 'fields')->where('object_id', $field->related_object_id)->where('type', 'string')->first();
				$field->options = DB::table($related_object->name)->orderBy($related_object->order_by, $related_object->direction)->lists($related_field->name, 'id');

				//indent nested selects
				if ($field->type == 'select' && !empty($related_object->group_by_field)) {
					$grouped_field = DB::table(Config::get('avalon::db_prefix') . 'fields')->where('id', $related_object->group_by_field)->first();
					if ($grouped_field->object_id == $grouped_field->related_object_id) {
						$field->options = $parents = array();
						$options = DB::table($related_object->name)->orderBy($related_object->order_by, $related_object->direction)->get();
						foreach ($options as $option) {
							if (!empty($option->{$grouped_field->name})) {
								//calculate indent
								if (in_array($option->{$grouped_field->name}, $parents)) {
									$parents = array_slice($parents, 0, array_search($option->{$grouped_field->name}, $parents) + 1);
								} else {
									$parents[] = $option->{$grouped_field->name};
								}
								$option->{$related_field->name} = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', count($parents)) . $option->{$related_field->name};
							} elseif (count($parents)) {
								$parents = array();
							}
							$field->options[$option->id] = $option->{$related_field->name};
						}
					}
				}

				//select might be nullable
				if ($field->type == 'select' && !$field->required) {
					$field->options = array(''=>'') + $field->options;
				}

			} elseif ($field->type == 'image') {
				list($field->screen_width, $field->screen_height) = self::getImageDimensions($field->width, $field->height);
			}
		}

		return View::make('avalon::instances.create', array(
			'object'=>$object, 
			'fields'=>$fields,
		));
	}

	//save a new object instance to the database
	public function store($object_id) {
		$object = DB::table(Config::get('avalon::db_prefix') . 'objects')->where('id', $object_id)->first();
		$fields = DB::table(Config::get('avalon::db_prefix') . 'fields')->where('object_id', $object_id)->get();
		
		//metadata
		$inserts = array(
			'created_at'=>new DateTime,
			'updated_at'=>new DateTime,
			'updated_by'=>Auth::user()->id,
			'precedence'=>DB::table($object->name)->max('precedence') + 1
		);
		
		//run various cleanup processes on the fields
		foreach ($fields as $field) {
			if ($field->type != 'checkboxes') {
				$inserts[$field->name] = self::sanitize($field);
			}
		}

		$instance_id = DB::table($object->name)->insertGetId($inserts);
		
		//update objects table with latest counts
		DB::table(Config::get('avalon::db_prefix') . 'objects')->where('id', $object_id)->update(array(
			'count'=>DB::table($object->name)->whereNull('deleted_at')->count(),
			'updated_at'=>new DateTime,
			'updated_by'=>Auth::user()->id
		));

		//handle any checkboxes, had to wait for instance_id
		foreach ($fields as $field) {
			if ($field->type == 'checkboxes') {
				//figure out schema, loop through and save all the checkboxes
				$object_column = self::getKey($object->name);
				$remote_column = self::getKey($field->related_object_id);
				if (Input::has($field->name)) {
					foreach (Input::get($field->name) as $related_id) {
						DB::table($field->name)->insert(array(
							$object_column=>$instance_id,
							$remote_column=>$related_id,
						));
					}
				}
			} elseif ($field->type == 'image') {
				DB::table(Config::get('avalon::db_prefix') . 'files')->where('id', Input::get($field->name))->update(array('instance_id'=>$instance_id));
			}
		}

		FileController::cleanup();
		
		return Redirect::action('InstanceController@index', $object_id)->with('instance_id', $instance_id);
	}
	
	//show edit form
	public function edit($object_id, $instance_id) {
		$object = DB::table(Config::get('avalon::db_prefix') . 'objects')->where('id', $object_id)->first();
		$fields = DB::table(Config::get('avalon::db_prefix') . 'fields')->where('object_id', $object_id)->orderBy('precedence')->get();
		$instance = DB::table($object->name)->where('id', $instance_id)->first();

		//format instance values for form
		foreach ($fields as &$field) {
			if ($field->type == 'datetime') {
				if (!empty($instance->{$field->name})) $instance->{$field->name} = date('Y-m-d\TH:i:s', strtotime($instance->{$field->name}));
			} elseif (($field->type == 'checkboxes') || ($field->type == 'select')) {

				//load options for checkboxes or selects
				$related_object = DB::table(Config::get('avalon::db_prefix') . 'objects')->where('id', $field->related_object_id)->first();
				$related_field  = DB::table(Config::get('avalon::db_prefix') . 'fields')->where('object_id', $field->related_object_id)->where('type', 'string')->first();
				$field->options = DB::table($related_object->name)->orderBy($related_object->order_by, $related_object->direction)->lists($related_field->name, 'id');

				//indent nested selects
				if ($field->type == 'select' && !empty($related_object->group_by_field)) {
					$grouped_field = DB::table(Config::get('avalon::db_prefix') . 'fields')->where('id', $related_object->group_by_field)->first();
					if ($grouped_field->object_id == $grouped_field->related_object_id) {
						$field->options = $parents = array();
						$options = DB::table($related_object->name)->orderBy($related_object->order_by, $related_object->direction)->get();
						foreach ($options as $option) {
							if (!empty($option->{$grouped_field->name})) {
								//calculate indent
								if (in_array($option->{$grouped_field->name}, $parents)) {
									$parents = array_slice($parents, 0, array_search($option->{$grouped_field->name}, $parents) + 1);
								} else {
									$parents[] = $option->{$grouped_field->name};
								}
								$option->{$related_field->name} = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', count($parents)) . $option->{$related_field->name};
							} elseif (count($parents)) {
								$parents = array();
							}
							$field->options[$option->id] = $option->{$related_field->name};
						}
					}
				}

				//select might be nullable
				if ($field->type == 'select' && !$field->required) {
					$field->options = array(''=>'') + $field->options;
				}

				//get checkbox values todo make a function for consistently getting these checkbox column names
				if ($field->type == 'checkboxes') {
					$table_key = Str::singular($object->name) . '_id';
					$foreign_key = Str::singular($related_object->name) . '_id';
					$instance->{$field->name} = DB::table($field->name)->where($table_key, $instance->id)->lists($foreign_key);
				}

			} elseif ($field->type == 'image') {
				list($field->screen_width, $field->screen_height) = self::getImageDimensions($field->width, $field->height);
				$instance->{$field->name} = DB::table(Config::get('avalon::db_prefix') . 'files')->where('id', $instance->{$field->name})->first();
			} elseif ($field->type == 'slug') {
				if ($field->required && empty($instance->{$field->name}) && $field->related_field_id) {
					//slugify related field to populate this one
					foreach ($fields as $related_field) {
						if ($related_field->id == $field->related_field_id) {
							$instance->{$field->name} = Str::slug($instance->{$related_field->name});
						}
					}
				}
			}
		}
		
		return View::make('avalon::instances.edit', array(
			'object'=>$object,
			'fields'=>$fields,
			'instance'=>$instance,
		));
	}
	
	//save edits to database
	public function update($object_id, $instance_id) {
		$object = DB::table(Config::get('avalon::db_prefix') . 'objects')->where('id', $object_id)->first();
		$fields = DB::table(Config::get('avalon::db_prefix') . 'fields')->where('object_id', $object_id)->get();
		
		//metadata
		$updates = array(
			'updated_at'=>new DateTime,
			'updated_by'=>Auth::user()->id,
		);
		
		//run loop through the fields
		foreach ($fields as $field) {
			if ($field->type == 'checkboxes') {
				//figure out schema, loop through and save all the checkboxes
				$object_column = self::getKey($object->name);
				$remote_column = self::getKey($field->related_object_id);
				DB::table($field->name)->where($object_column, $instance_id)->delete();

				if (Input::has($field->name)) {
					foreach (Input::get($field->name) as $related_id) {
						DB::table($field->name)->insert(array(
							$object_column=>$instance_id,
							$remote_column=>$related_id,
						));
					}
				}
			} elseif ($field->type == 'image') {
				DB::table(Config::get('avalon::db_prefix') . 'files')
					->where('id', Input::get($field->name))
					->update(array('instance_id'=>$instance_id));

				if ($files = DB::table(Config::get('avalon::db_prefix') . 'files')
					->where('field_id', $field->id)
					->where('instance_id', $instance_id)
					->where('id', '<>', Input::get($field->name))
					->get()) {
					FileController::cleanup($files);
				}
				$updates[$field->name] = Input::get($field->name);
			} else {
				$updates[$field->name] = self::sanitize($field);
			}
		}
		
		FileController::cleanup();

		DB::table($object->name)->where('id', $instance_id)->update($updates);
		
		//update object meta
		DB::table(Config::get('avalon::db_prefix') . 'objects')->where('id', $object_id)->update(array(
			'count'=>DB::table($object->name)->whereNull('deleted_at')->count(),
			'updated_at'=>new DateTime,
			'updated_by'=>Auth::user()->id
		));
		
		return Redirect::action('InstanceController@index', $object_id)->with('instance_id', $instance_id);
	}
	
	//remove object from db - todo check key/constraints
	public function destroy($object_id, $instance_id) {
		$object = DB::table(Config::get('avalon::db_prefix') . 'objects')->where('id', $object_id)->first();
		DB::table($object->name)->where('id', $instance_id)->delete();

		//update object meta
		DB::table(Config::get('avalon::db_prefix') . 'objects')->where('id', $object_id)->update(array(
			'count'=>DB::table($object->name)->whereNull('deleted_at')->count(),
		));

		return Redirect::action('InstanceController@index', $object_id);
	}
	
	//reorder fields by drag-and-drop
	public function reorder($object_id) {
		$object = DB::table(Config::get('avalon::db_prefix') . 'objects')->where('id', $object_id)->first();

		//determine whether nested
		$object->nested = false;
		if (!empty($object->group_by_field)) {
			$grouped_field = DB::table(Config::get('avalon::db_prefix') . 'fields')->where('id', $object->group_by_field)->first();
			if ($grouped_field->related_object_id == $object->id) {
				$object->nested = true;
			}
		}

		if ($object->nested) {
			$instance_ids = explode(',', Input::get('list'));
			$precedence = 1;
			foreach ($instance_ids as $instance_id) {
				if (!empty($instance_id)) {
					DB::table($object->name)->where('id', $instance_id)->update(array('precedence'=>$precedence++));
				}
			}
			if (Input::has('id') && Input::has('parent_id')) {
				DB::table($object->name)->where('id', Input::get('id'))->update(array(
					'parent_id'=>Input::get('parent_id'),
					//updated_at, updated_by?
				));
			}
			return 'done reordering linear';
		} else {
			$instances = explode('&', Input::get('order'));
			$precedence = 1;
			foreach ($instances as $instance) {
				list($garbage, $instance_id) = explode('=', $instance);
				if (!empty($instance_id)) {
					DB::table($object->name)->where('id', $instance_id)->update(array('precedence'=>$precedence++));
				}
			}
			return 'done reordering linear';
		}
	}
	
	//soft delete
	public function delete($object_id, $instance_id) {
		$object = DB::table(Config::get('avalon::db_prefix') . 'objects')->where('id', $object_id)->first();
		
		//toggle instance with active or inactive
		$deleted_at = (Input::get('active') == 1) ? null : new DateTime;

		DB::table($object->name)->where('id', $instance_id)->update(array(
			'deleted_at'=>$deleted_at,
			'updated_at'=>new DateTime,
			'updated_by'=>Auth::user()->id,
		));

		//update object meta
		DB::table(Config::get('avalon::db_prefix') . 'objects')->where('id', $object_id)->update(array(
			'count'=>DB::table($object->name)->whereNull('deleted_at')->count(),
			'updated_at'=>new DateTime,
			'updated_by'=>Auth::user()->id,
		));

		$updated = DB::table($object->name)->where('id', $instance_id)->pluck('updated_at');

		return Dates::relative($updated);
	}

	//sanitize field values before inserting
	private function sanitize($field) {
		$value = Input::get($field->name);

		//format date fields
		if ($field->type == 'date') $value = date('Y-m-d', strtotime($value));

		//format slug fields
		if ($field->type == 'slug') $value = Str::slug($value);

		//add each field if not present
		if (empty($value) && !$field->required) $value = null;

		return $value;
	}

	//recursively assemble nested tree
	private function nestedNodeExists(&$array, $parent_id, $child) {
		foreach ($array as &$a) {
			if ($a->id == $parent_id) {
				$a->children[] = $child;
				return true;
			} elseif (count($a->children) && self::nestedNodeExists($a->children, $parent_id, $child)) {
				return true;
			}
		}
		return false;
	}

	//return a foreign key column name for a given table name or object_id
	//needs to be public because called from AvalonServiceProvider::boot
	public static function getKey($table_name) {
		if (is_integer($table_name)) $table_name = DB::table(Config::get('avalon::db_prefix') . 'objects')->where('id', $table_name)->pluck('name');
		return Str::singular($table_name) . '_id';
	}	

	//get display size for create and edit views
	private static function getImageDimensions($width=false, $height=false) {

		//too wide?
		if ($width && $width > Config::get('avalon::image_max_width')) {
			if ($height) $height *= Config::get('avalon::image_max_width') / $width;
			$width = Config::get('avalon::image_max_width');
		}

		//too tall?
		if ($height && $height > Config::get('avalon::image_max_height')) {
			if ($width) $width *= Config::get('avalon::image_max_height') / $height;
			$height = Config::get('avalon::image_max_height');
		}

		//not specified?
		if (!$width) $width = Config::get('avalon::image_default_width');
		if (!$height) $height = Config::get('avalon::image_default_height');

		//too large?
		if ($width * $height > Config::get('avalon::image_max_area')) {
			$width *= Config::get('avalon::image_max_area') / $width * $height;
			$height *= Config::get('avalon::image_max_area') / $width * $height;
		}

		return array($width, $height);
	}

	/* public function redactor_s3() {

		$S3_KEY		= Config::get('aws.key');
		$S3_SECRET	= Config::get('aws.secret');
		$S3_BUCKET	= Config::get('aws.bucket');
		$S3_URL		= 'http://s3.amazonaws.com';
		$EXPIRE_TIME = (60 * 5); // 5 minutes

		$objectName = '/' . $_GET['name'];
		$mimeType	= $_GET['type'];
		$expires 	= time() + $EXPIRE_TIME;
		$amzHeaders	= "x-amz-acl:public-read";
		$stringToSign = "PUT\n\n$mimeType\n$expires\n$amzHeaders\n$S3_BUCKET$objectName";

		$sig = urlencode(base64_encode(hash_hmac('sha1', $stringToSign, $S3_SECRET, true)));
		$url = urlencode("$S3_URL$S3_BUCKET$objectName?AWSAccessKeyId=$S3_KEY&Expires=$expires&Signature=$sig");
	}

	public static function upload_image($object_id, $instance_id) {

		$temp_file = 'temp.dat';

		//resize and save - todo learn how to do facades in a package
		Image::make(Input::file('image_upload')->getRealPath())
				->resize(830, null, true)
				->save($temp_file);

		//send the image to s3
		$s3 = App::make('aws')
			->get('s3')
			->putObject(array(
			    'Bucket'     => Config::get('aws.bucket'),
			    'Key'        => Input::get('filename'),
			    'SourceFile' => $temp_file,
	            'ACL'		 => 'public-read',
			));

		//delete the image
		unlink(base_path() . '/public/' . $temp_file);

		//send a response
		
	}
	*/
}

