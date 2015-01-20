<?php
use Aws\Common\Enum\Region;
use Aws\Laravel\AwsServiceProvider;
use Illuminate\Foundation\Application;

class InstanceController extends \BaseController {

	# Show list of instances for an object
	# $group_by_id is for when coming from a linked object
	public function index($object_name, $linked_id=false) {

		# Get info about the object
		$object = DB::table(DB_OBJECTS)->where('name', $object_name)->first();
		$fields = DB::table(DB_FIELDS)
			->where('object_id', $object->id)
			->where('visibility', 'list')
			->orWhere('id', $object->group_by_field)
			->orderBy('precedence')->get();

		# Start query
		$instances = DB::table($object->name);

		# Build select statement
		$instances->select([$object->name . '.id', $object->name . '.updated_at', $object->name . '.deleted_at']);
		foreach ($fields as $field) {
			if ($field->type == 'checkboxes') {
				$related_object = self::getRelatedObject($field->related_object_id);
				$instances->addSelect(DB::raw('(SELECT GROUP_CONCAT(' . $related_object->name . '.' . $related_object->field->name . ' SEPARATOR ", ") 
					FROM ' . $related_object->name . ' 
					JOIN ' . $field->name . ' ON ' . $related_object->name . '.id = ' . $field->name . '.' . self::getKey($related_object->name) . '
					WHERE ' . $field->name . '.' . self::getKey($object->name) . ' = ' . $object->name . '.id 
					ORDER BY ' . $related_object->name . '.' . $related_object->field->name . ') AS ' . $field->name));
			} elseif ($field->type == 'image') {
				$instances
					->leftJoin(DB_FILES, $object->name . '.' . $field->name, '=', DB_FILES . '.id')
					->addSelect(DB_FILES . '.url AS ' . $field->name . '_url');
			} elseif ($field->type == 'select') {
				$related_object = self::getRelatedObject($field->related_object_id);
				$instances
					->leftJoin($related_object->name, $object->name . '.' . $field->name, '=', $related_object->name . '.id')
					->addSelect($related_object->name . '.' . $related_object->field->name . ' AS ' . $field->name);
			} elseif ($field->type == 'user') {
				$instances
					->leftJoin(DB_USERS, $object->name . '.' . $field->name, '=', DB_USERS . '.id')
					->addSelect(DB_USERS . '.name AS ' . $field->name);
			} else {
				$instances->addSelect($object->name . '.' . $field->name);
			}
		}

		# Handle group-by fields
		$object->nested = false;
		if (!empty($object->group_by_field)) {
			$grouped_field = DB::table(DB_FIELDS)->where('id', $object->group_by_field)->first();
			$grouped_object = self::getRelatedObject($grouped_field->related_object_id);
			if ($grouped_object->id == $object->id) {
				//nested object
				$object->nested = true;
			} else {
				# Pull group_by_field out of the list of fields so it's not a column in the table
				foreach ($fields as $key=>$field) {
					if ($field->id == $object->group_by_field) unset($fields[$key]);
				}
				
				# Include group_by_field in resultset
				$instances
					->orderBy($grouped_object->name . '.' . $grouped_object->order_by, $grouped_object->direction)
					->addSelect($grouped_object->name . '.' . $grouped_object->field->name . ' as group');
	
				# If $linked_id, limit scope to just $linked_id
				if ($linked_id) {
					$instances->where($grouped_field->name, $linked_id);
				}
			}
		}

		# Set the order and direction
		$instances->orderBy($object->name . '.' . $object->order_by, $object->direction);

		# Run query and save it to a variable
		$instances = $instances->get();
		
		# Set Avalon URLs on each instance
		if ($object->can_edit) {
			foreach ($instances as &$instance) {
				$instance->link = URL::action('InstanceController@edit', array($object->name, $instance->id, $linked_id));
				$instance->delete = URL::action('InstanceController@delete', array($object->name, $instance->id));
			}
		}

		# If it's a nested object, nest-ify the resultset
		if ($object->nested) {
			$list = array();
			foreach ($instances as &$instance) {
				$instance->children = array();
				if (empty($instance->{$grouped_field->name})) { //$grouped_field->name is for ex parent_id
					$list[] = $instance;
				} elseif (self::nestedNodeExists($list, $instance->{$grouped_field->name}, $instance)) {
					//attached child to parent node
				} else {
					//an error occurred; a parent should exist but is not yet present
				}
			}
			$instances = $list;
		}

		$return = compact('object', 'fields', 'instances');

		# Return array to edit()
		if ($linked_id) {
			$object->group_by_field = false; //hacky, but easiest way to remove grouping
			return $return;
		}

		# Return HTML view
		return View::make('avalon::instances.index', $return);
	}

	//show create form for an object instance
	public function create($object_name, $linked_id=false) {
		$object = DB::table(DB_OBJECTS)->where('name', $object_name)->first();
		$fields = DB::table(DB_FIELDS)->where('object_id', $object->id)->orderBy('precedence')->get();
		$options = array();
		
		# Add return var to the queue
		if ($linked_id) {
			$return_to = action('InstanceController@edit', [self::getRelatedObjectName($object), $linked_id]);
		} elseif (URL::previous()) {
			$return_to = URL::previous();
		} else {
			$return_to = action('InstanceController@index', $object->name);
		}

		foreach ($fields as $field) {
			if (($field->type == 'checkboxes') || ($field->type == 'select')) {

				//load options for checkboxes or selects
				$related_object = self::getRelatedObject($field->related_object_id);
				$field->options = DB::table($related_object->name)->orderBy($related_object->order_by, $related_object->direction)->lists($related_object->field->name, 'id');

				//indent nested selects
				if ($field->type == 'select' && !empty($related_object->group_by_field)) {
					$grouped_field = DB::table(DB_FIELDS)->where('id', $related_object->group_by_field)->first();
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
								$option->{$related_object->field->name} = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', count($parents)) . $option->{$related_object->field->name};
							} elseif (count($parents)) {
								$parents = array();
							}
							$field->options[$option->id] = $option->{$related_object->field->name};
						}
					}
				}

				//select might be nullable
				if ($field->type == 'select' && !$field->required) {
					$field->options = [''=>''] + $field->options;
				}
			} elseif ($field->type == 'user') {
				$field->options = DB::table(DB_USERS)->orderBy('name')->lists('name', 'id');
				if (!$field->required) $field->options = [''=>''] + $field->options;
			} elseif (in_array($field->type, array('image', 'images'))) {
				list($field->screen_width, $field->screen_height) = FileController::getImageDimensions($field->width, $field->height);
			}
		}

		return View::make('avalon::instances.create', compact('object', 'fields', 'linked_id', 'return_to'));
	}

	//save a new object instance to the database
	public function store($object_name, $linked_id=false) {
		$object = DB::table(DB_OBJECTS)->where('name', $object_name)->first();
		$fields = DB::table(DB_FIELDS)->where('object_id', $object->id)->orderBy('precedence')->get();
		
		//metadata
		$inserts = array(
			'created_at'=>new DateTime,
			'updated_at'=>new DateTime,
			'created_by'=>Auth::user()->id,
			'updated_by'=>Auth::user()->id,
			'precedence'=>DB::table($object->name)->max('precedence') + 1
		);
		
		//run various cleanup processes on the fields
		foreach ($fields as $field) {
			if (!in_array($field->type, array('checkboxes', 'images'))) {
				$inserts[$field->name] = self::sanitize($field);
			}
		}

		//determine where slug is coming from
		if ($slug_source = Slug::source($object->id)) {
			$slug_source = Input::get($slug_from);
		} else {
			$slug_source = date('Y-m-d');
		}

		//get other values to check uniqueness
		$uniques = DB::table($object->name)->lists('slug');

		//add unique, formatted slug to the insert batch
		$inserts['slug'] = Slug::make($slug_source, $uniques);

		//run insert
		$instance_id = DB::table($object->name)->insertGetId($inserts);
		
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
				DB::table(DB_FILES)->where('id', Input::get($field->name))->update(array('instance_id'=>$instance_id));
			} elseif ($field->type == 'images') {
				$file_ids = explode(',', Input::get($field->name));
				$precedence = 0;
				foreach ($file_ids as $file_id) {
					DB::table(DB_FILES)->where('id', $file_id)->update(array(
						'instance_id'=>$instance_id,
						'precedence'=>++$precedence,
					));
				}
			}
		}

		//update objects table with latest counts
		DB::table(DB_OBJECTS)->where('id', $object->id)->update(array(
			'count'=>DB::table($object->name)->whereNull('deleted_at')->count(),
			'updated_at'=>new DateTime,
			'updated_by'=>Auth::user()->id
		));

		//clean up any abandoned files
		FileController::cleanup();

		//return to target		
		return Redirect::to(Input::get('return_to'));
	}
	
	//show edit form
	public function edit($object_name, $instance_id, $linked_id=false) {

		# Get object / field / whatever infoz
		$object = DB::table(DB_OBJECTS)->where('name', $object_name)->first();
		$fields = DB::table(DB_FIELDS)->where('object_id', $object->id)->orderBy('precedence')->get();
		$instance = DB::table($object->name)->where('id', $instance_id)->first();

		# Add return var to the queue
		if ($linked_id) {
			$return_to = action('InstanceController@edit', [self::getRelatedObjectName($object), $linked_id]);
		} elseif (URL::previous()) {
			$return_to = URL::previous();
		} else {
			$return_to = action('InstanceController@index', $object->name);
		}

		//format instance values for form
		foreach ($fields as &$field) {
			if ($field->type == 'datetime') {
				if (!empty($instance->{$field->name})) $instance->{$field->name} = date('m/d/Y h:i A', strtotime($instance->{$field->name}));
			} elseif (($field->type == 'checkboxes') || ($field->type == 'select')) {

				//load options for checkboxes or selects
				$related_object = self::getRelatedObject($field->related_object_id);
				$field->options = DB::table($related_object->name)->orderBy($related_object->order_by, $related_object->direction)->lists($related_object->field->name, 'id');

				//indent nested selects
				if ($field->type == 'select' && !empty($related_object->group_by_field)) {
					$grouped_field = DB::table(DB_FIELDS)->where('id', $related_object->group_by_field)->first();
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
								$option->{$related_object->field->name} = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', count($parents)) . $option->{$related_object->field->name};
							} elseif (count($parents)) {
								$parents = array();
							}
							$field->options[$option->id] = $option->{$related_object->field->name};
						}
					}
				}

				//select might be nullable
				if ($field->type == 'select' && !$field->required) {
					$field->options = [''=>''] + $field->options;
				}

				//get checkbox values todo make a function for consistently getting these checkbox column names
				if ($field->type == 'checkboxes') {
					$table_key = Str::singular($object->name) . '_id';
					$foreign_key = Str::singular($related_object->name) . '_id';
					$instance->{$field->name} = DB::table($field->name)->where($table_key, $instance->id)->lists($foreign_key);
				}

			} elseif ($field->type == 'user') {
				$field->options = DB::table(DB_USERS)->orderBy('name')->lists('name', 'id');
				if (!$field->required) $field->options = [''=>''] + $field->options;
			} elseif ($field->type == 'image') {
				$instance->{$field->name} = DB::table(DB_FILES)->where('id', $instance->{$field->name})->first();
				if (!empty($instance->{$field->name}->width) && !empty($instance->{$field->name}->height)) {
					$field->width = $instance->{$field->name}->width;
					$field->height = $instance->{$field->name}->height;
				}
				list($field->screen_width, $field->screen_height) = FileController::getImageDimensions($field->width, $field->height);
			} elseif ($field->type == 'images') {
				$instance->{$field->name} = DB::table(DB_FILES)->where('field_id', $field->id)->where('instance_id', $instance->id)->orderBy('precedence', 'asc')->get();
				foreach ($instance->{$field->name} as &$image) {
					if (!empty($image->width) && !empty($image->height)) {
						$image->screen_width = $image->width;
						$image->screen_width = $image->height;
					}
				}
				list($field->screen_width, $field->screen_height) = FileController::getImageDimensions($field->width, $field->height);
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

		# Get linked objects
		$links = DB::table(DB_OBJECT_LINKS)
				->where('object_id', $object->id)
				->join(DB_OBJECTS, DB_OBJECT_LINKS . '.linked_id', '=', DB_OBJECTS . '.id')
				->lists(DB_OBJECTS . '.name');
		foreach ($links as &$link) {
			$link = self::index($link, $instance_id, $linked_id);
		}

		return View::make('avalon::instances.edit', compact('object', 'fields', 'instance', 'links', 'linked_id', 'return_to'));
	}
	
	//save edits to database
	public function update($object_name, $instance_id, $linked_id=false) {
		$object = DB::table(DB_OBJECTS)->where('name', $object_name)->first();
		$fields = DB::table(DB_FIELDS)->where('object_id', $object->id)->get();
		
		//metadata
		$updates = array(
			'updated_at'=>new DateTime,
			'updated_by'=>Auth::user()->id,
		);
		
		//run loop through the fields
		foreach ($fields as $field) {
			if ($field->type == 'checkboxes') {
				
				# Figure out schema
				$object_column = self::getKey($object->name);
				$remote_column = self::getKey($field->related_object_id);

				# Clear old values
				DB::table($field->name)->where($object_column, $instance_id)->delete();

				# Loop through and save all the checkboxes
				if (Input::has($field->name)) {
					foreach (Input::get($field->name) as $related_id) {
						DB::table($field->name)->insert(array(
							$object_column=>$instance_id,
							$remote_column=>$related_id,
						));
					}
				}
			} elseif ($field->type == 'images') {

				# Unset any old file associations (will get cleaned up after this loop)
				DB::table(DB_FILES)
					->where('field_id', $field->id)
					->where('instance_id', $instance_id)
					->update(array('instance_id'=>null));

				# Create new associations
				$file_ids = explode(',', Input::get($field->name));
				$precedence = 0;
				foreach ($file_ids as $file_id) {
					DB::table(DB_FILES)
						->where('id', $file_id)
						->update(array(
							'instance_id'=>$instance_id,
							'precedence'=>++$precedence,
						));
				}

			} else {
				if ($field->type == 'image') {

					# Unset any old file associations (will get cleaned up after this loop)
					DB::table(DB_FILES)
						->where('field_id', $field->id)
						->where('instance_id', $instance_id)
						->update(array('instance_id'=>null));


					# Capture the uploaded file by setting the reverse-lookup
					DB::table(DB_FILES)
						->where('id', Input::get($field->name))
						->update(array('instance_id'=>$instance_id));

				}

				$updates[$field->name] = self::sanitize($field);
			}
		}

		//slug
		if (!empty($object->url)) {
			$uniques = DB::table($object->name)->where('id', '<>', $instance_id)->lists('slug');
			$updates['slug'] = Slug::make(Input::get('slug'), $uniques);
		}
		/* //todo manage a redirect table if client demand warrants it
		$old_slug = DB::table($object->name)->find($instance_id)->pluck('slug');
		if ($updates['slug'] != $old_slug) {
		}*/
		
		//run update
		DB::table($object->name)->where('id', $instance_id)->update($updates);
		
		//clean up abandoned files
		FileController::cleanup();

		//update object meta
		DB::table(DB_OBJECTS)->where('id', $object->id)->update([
			'count'=>DB::table($object->name)->whereNull('deleted_at')->count(),
			'updated_at'=>new DateTime,
			'updated_by'=>Auth::user()->id
		]);
		
		return Redirect::to(Input::get('return_to'));
	}
	
	# Remove object from db - todo check key/constraints
	public function destroy($object_name, $instance_id) {
		$object = DB::table(DB_OBJECTS)->where('name', $object_name)->first();
		DB::table($object->name)->where('id', $instance_id)->delete();

		//update object meta
		DB::table(DB_OBJECTS)->where('id', $object->id)->update([
			'count'=>DB::table($object->name)->whereNull('deleted_at')->count(),
		]);

		return Redirect::to(Input::get('return_to'));
	}
	
	# Reorder fields by drag-and-drop
	public function reorder($object_name) {
		$object = DB::table(DB_OBJECTS)->where('name', $object_name)->first();

		//determine whether nested
		$object->nested = false;
		if (!empty($object->group_by_field)) {
			$grouped_field = DB::table(DB_FIELDS)->where('id', $object->group_by_field)->first();
			if ($grouped_field->related_object_id == $object->id) {
				$object->nested = true;
			}
		}

		if ($object->nested) {
			$instance_ids = explode(',', Input::get('list'));
			$precedence = 1;
			foreach ($instance_ids as $instance_id) {
				if (!empty($instance_id)) {
					DB::table($object->name)->where('id', $instance_id)->update(['precedence'=>$precedence++]);
				}
			}
			if (Input::has('id') && Input::has('parent_id')) {
				DB::table($object->name)->where('id', Input::get('id'))->update([
					'parent_id'=>Input::get('parent_id'),
					//updated_at, updated_by?
				]);
			}
			return 'done reordering nested';
		} else {
			$instances = explode('&', Input::get('order'));
			$precedence = 1;
			foreach ($instances as $instance) {
				list($garbage, $instance_id) = explode('=', $instance);
				if (!empty($instance_id)) {
					DB::table($object->name)->where('id', $instance_id)->update(['precedence'=>$precedence++]);
				}
			}
			return 'done reordering ' . Input::get('order')  . ' instances, linear';
		}
	}
	
	# Soft delete
	public function delete($object_name, $instance_id) {
		$object = DB::table(DB_OBJECTS)->where('name', $object_name)->first();
		
		//toggle instance with active or inactive
		$deleted_at = (Input::get('active') == 1) ? null : new DateTime;

		DB::table($object->name)->where('id', $instance_id)->update(array(
			'deleted_at'=>$deleted_at,
			'updated_at'=>new DateTime,
			'updated_by'=>Auth::user()->id,
		));

		//update object meta
		DB::table(DB_OBJECTS)->where('id', $object->id)->update(array(
			'count'=>DB::table($object->name)->whereNull('deleted_at')->count(),
			'updated_at'=>new DateTime,
			'updated_by'=>Auth::user()->id,
		));

		$updated = DB::table($object->name)->where('id', $instance_id)->pluck('updated_at');

		return Dates::relative($updated);
	}

	# Sanitize field values before inserting
	private function sanitize($field) {
		$value = trim(Input::get($field->name));

		//add each field if not present
		if ($field->type == 'checkbox') {
			
			$value = !empty($value); //1 or 0, never null
		
		} elseif (empty($value) && ($value !== '0') && !$field->required) {
		
			$value = null; //nullable
		
		} else {

			//format date fields
			if ($field->type == 'date') $value = date('Y-m-d', strtotime($value));

			//format date fields
			if ($field->type == 'datetime') $value = date('Y-m-d H:i:s', strtotime($value));

			//format slug fields
			if ($field->type == 'slug') $value = Str::slug($value);

		}

		return $value;
	}

	# Recursively assemble nested tree
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

	# Return a foreign key column name for a given table name or object_id (public for AvalonServiceProvider::boot)
	public static function getKey($table_name) {
		if (ctype_digit(strval($table_name))) $table_name = DB::table(DB_OBJECTS)->where('id', $table_name)->pluck('name');
		return Str::singular($table_name) . '_id';
	}

	# Get related object with the first string field name
	private static function getRelatedObject($related_object_id) {
		$related = DB::table(DB_OBJECTS)->where('id', $related_object_id)->first();
		$related->field = DB::table(DB_FIELDS)->where('object_id', $related_object_id)->whereIn('type', ['string', 'text'])->first();
		return $related;
	}

	# Get related object's name with an object
	private static function getRelatedObjectName($object) {
		return DB::table(DB_FIELDS)
			->join(DB_OBJECTS, DB_FIELDS . '.related_object_id', '=', DB_OBJECTS . '.id')
			->where(DB_FIELDS . '.id', $object->group_by_field)
			->pluck(DB_OBJECTS . '.name');
	}

	# Draw an instance table, used both by index and by edit > linked
	public static function table($object, $fields, $instances) {
		$table = new Table;
		$table->rows($instances);
		foreach ($fields as $field) {
			$table->column($field->name, $field->type, $field->title, $field->width, $field->height);
		}
		$table->column('updated_at', 'updated_at', trans('avalon::messages.site_updated_at'));
		if ($object->can_edit) {
			$table->deletable();
			if ($object->order_by == 'precedence') $table->draggable(URL::action('InstanceController@reorder', $object->name));
		}
		if (!empty($object->group_by_field)) $table->groupBy('group');
		return $table->draw();
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
		
	}*/
}

