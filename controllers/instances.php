<?php
class Avalon_Instances_Controller extends Controller {
	
	public $restful = true;
	
	public function delete_edit($object_id, $instance_id) {
		//'delete' an instance, which amounts to setting its active and published flags to off
		//todo handle all deletes with ajax

		$object = \Avalon\Object::find($object_id);
		DB::table($object->table_name)->where('id', '=', $instance_id)->update(array(
				'active' => 0,
				'published' => 0,
				'updated_by' => Auth::user()->id,
				'updated_at' => DB::raw('NOW()'),
			));

		return Redirect::to_route('instances', $object_id);
	}

	public function get_add($object_id) {
		//show the add new instance form

		$object = \Avalon\Object::find($object_id);
		foreach ($object->fields as $field) {
			if ($field->type == 'typeahead') {
				$values = DB::table($object->table_name)->where('active', '=', 1)->where($field->field_name, '<>', '')->group_by($field->field_name)->get(array($field->field_name));
				foreach ($values as &$v) $v = $v->{$field->field_name};
				$field->values = htmlentities(json_encode($values));
			}
		}

		return View::make('avalon::instances.add', array(
			'object'=>$object,
			'title'=>'Add New',
			'link_color'=>DB::table('avalon')->where('id', '=', 1)->only('link_color'),
		));
	}

	public function get_edit($object_id, $instance_id) {
		//show the edit instance form

		$object = \Avalon\Object::find($object_id);

		foreach ($object->fields as $field) {
			if ($field->type == 'typeahead') {
				$values = DB::table($object->table_name)->where('active', '=', 1)->where($field->field_name, '<>', '')->group_by($field->field_name)->get(array($field->field_name));
				foreach ($values as &$v) $v = $v->{$field->field_name};
				$field->values = htmlentities(json_encode($values));
			}
		}

		$instance = DB::table($object->table_name)->find($instance_id);

		foreach ($object->fields as $field) {
			if ($field->type == 'date') {
				if (!empty($instance->{$field->field_name})) {
					$instance->{$field->field_name} = date('Y-m-d', strtotime($instance->{$field->field_name}));
				}
			}
		}

		return View::make('avalon::instances.edit', array(
			'object'=>$object,
			'instance'=>$instance,
			'title'=>'Edit',
			'link_color'=>DB::table('avalon')->where('id', '=', 1)->only('link_color'),
		));
	}

	public function get_list($object_id) {
		//display the instance list, which is dynamic
		//todo add string to empty fields for linking

		$object = \Avalon\Object::find($object_id);

		//get table columns
		$columns = \Avalon\Field::where('object_id', '=', $object_id)->where('visibility', '=', 'list')->where('active', '=', 1)->order_by('precedence', 'ASC')->take(5)->get(array('title', 'field_name', 'type'));

		//get instances
		$instances = DB::table($object->table_name)->order_by($object->order_by, $object->direction)->where('active', '=', 1)->get();
		foreach ($instances as &$instance) {
			$instance->link = URL::to_route('instances_edit', array($object->id, $instance->id));
			$instance->updated_at = \Avalon\Date::relative($instance->updated_at);

			//per-cell updates
			foreach ($columns as $column) {
				if ($column->type == 'checkbox') {
					$instance->{$column->field_name} = ($instance->{$column->field_name}) ? 'Yes' : '';
				} elseif ($column->type == 'date') {
					$instance->{$column->field_name} = \Avalon\Date::format($instance->{$column->field_name});
				} elseif ($column->type == 'textarea-rich') {
					$instance->{$column->field_name} = $this->rip_tags($instance->{$column->field_name});
				}
			}
		}

		return View::make('avalon::instances.list', array(
			'object'=>$object,
			'columns'=>$columns,
			'instances'=>$instances,
			'title'=>$object->title,
			'user'=>Auth::user(),
			'link_color'=>DB::table('avalon')->where('id', '=', 1)->only('link_color'),
		));
	}

	public function post_add($object_id) {
		$object = \Avalon\Object::find($object_id);

		//set meta values
		$values = array(
			'active'	 => 1,
			'precedence' => DB::table($object->table_name)->max('precedence') + 1,
			'created_by' => Auth::user()->id,
			'updated_by' => Auth::user()->id,
			'created_at' => DB::raw('NOW()'),
			'updated_at' => DB::raw('NOW()'),
		);

		//insert field values
		foreach ($object->fields as $field) {
			$value = Input::get($field->field_name);
			
			//per-type processing
			if ($field->type == 'checkbox') {
				$value = ($value == 'on') ? 1 : 0;
			} elseif ($field->type == 'url-local') {
				$value = Str::slug($value);
			}

			$values[$field->field_name] = $value;
		}

		$id = DB::table($object->table_name)->insert_get_id($values);

		//flash inserted $id row somehow?
		return Redirect::to_route('instances', $object_id);
	}

	public function post_publish($object_id, $instance_id) {
		//publish or unpublish an instance
		$object = \Avalon\Object::find($object_id);
		DB::table($object->table_name)->where('id', '=', $instance_id)->update(array('published'=>(Input::get('published') == 'true')));
	}

	public function post_reorder($object_id) {
		//use table_dnd to make an ajax request to reorder the instances for an object

		$object = \Avalon\Object::find($object_id);

		if (Request::ajax()) {
			$instance_ids = explode(',', Input::get('ids'));
			$precedences = explode(',', Input::get('precedences'));
			sort($precedences);
			foreach ($instance_ids as $instance_id) {
				DB::table($object->table_name)->where('id', '=', $instance_id)->update(array('precedence'=>array_shift($precedences)));
			}
    	}
	}

	public function put_edit($object_id, $instance_id) {

		$object = \Avalon\Object::find($object_id);

		//set meta values
		$values = array(
			'updated_by' => Auth::user()->id,
			'updated_at' => DB::raw('NOW()'),
		);

		//insert field values
		foreach ($object->fields as $field) {
			$value = Input::get($field->field_name);
			
			//per-type processing
			if ($field->type == 'checkbox') {
				$value = ($value == 'on') ? 1 : 0;
			} elseif ($field->type == 'url-local') {
				$value = Str::slug($value);
			}

			$values[$field->field_name] = $value;
		}

		DB::table($object->table_name)->where('id', '=', $instance_id)->update($values);

		//flash inserted row somehow?
		return Redirect::to_route('instances', $object_id);
	}

	private function rip_tags($string) { 
	    //thanks bzplan http://www.php.net/manual/en/function.strip-tags.php#110280

	    $string = preg_replace ('/<[^>]*>/', ' ', $string); 
	    $string = str_replace("\r", '', $string);    // --- replace with empty space
	    $string = str_replace("\n", ' ', $string);   // --- replace with space
	    $string = str_replace("\t", ' ', $string);   // --- replace with space
	    
	    return trim(preg_replace('/ {2,}/', ' ', $string)); 
	}

	private function typeahead_values($table, $column) {
		//create a JSON array to tell Boostrap what values to suggest for a typeahead field
		$list_groupings = array('foo', 'bar');
		return htmlentities(json_encode($list_groupings));
	}
}