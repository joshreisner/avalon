<?php
class Avalon_Fields_Controller extends Controller {
	
	public $restful = true;

	//declare acceptable field types.  need to reconsider how this is bound to the rest of the app
	private $field_types = array(
				'checkbox'=>'Checkbox',
			//	'checkboxes'=>'Checkboxes',
				'color'=>'Color',
				'date'=>'Date',
				'date-time'=>'Date & Time',
				'email'=>'Email',
			//	'dropdown'=>'Dropdown',
			//	'file'=>'File',
			//	'file-size'=>'File Size',
			//	'file-type'=>'File Type',
			//	'image'=>'Image',
				'integer'=>'Integer',
			//	'latitude'=>'Latitude',
				'text'=>'Text',
				'textarea-rich'=>'Textarea (Rich)',
				'textarea-plain'=>'Textarea (Plain)',
				'typeahead'=>'Typeahead',
				'url'=>'URL',
				'url-local'=>'URL (Local)'
			);

	private $field_visibilities = array(
				'list'=>'Show in List',
				'normal'=>'Normal',
				'hidden'=>'Hidden'
			);

	public function delete_edit($object_id, $field_id) {
		//delete a field.  it's a destructive edit, might need a prompt
		
		$field = \Avalon\Field::find($field_id);
		$field->delete();

		$object = \Avalon\Object::find($object_id);

		Schema::table($object->table_name, function($table) use ($field) {
			$table->drop_column($field->field_name);
		});
		
		return Redirect::to_route('fields', $object_id);
	}

	public function get_add($object_id) {
		//display the add new field form

		$object = \Avalon\Object::find($object_id);
		return View::make('avalon::fields.add', array(
			'object'=>$object,
			'field_types'=>$this->field_types,
			'field_visibilities'=>$this->field_visibilities,
			'title'=>'Add Field',
			'link_color'=>DB::table('avalon')->where('id', '=', 1)->only('link_color'),
		));
	}
	
	public function get_edit($object_id, $field_id) {
		//display the field edit form

		$object = \Avalon\Object::find($object_id);
		$field = \Avalon\Field::find($field_id);
		return View::make('avalon::fields.edit', array(
			'object'=>$object,
			'field'=>$field,
			'field_types'=>$this->field_types,
			'field_visibilities'=>$this->field_visibilities,
			'title'=>$field->title,
			'link_color'=>DB::table('avalon')->where('id', '=', 1)->only('link_color'),
		));
	}

	public function get_list($object_id) {
		//display the list of fields

		$object = \Avalon\Object::find($object_id);
		$fields = \Avalon\Field::where('object_id', '=', $object_id)->where('active', '=', 1)->order_by('precedence')->get(array('id', 'title', 'field_name', 'type', 'precedence', 'updated_at'));
		
		foreach ($fields as $f) {
			$f->link = URL::to_route('fields_edit', array($object->id, $f->id));
			$f->updated_at = \Avalon\Date::format($f->updated_at);
		}

		return View::make('avalon::fields.list', array(
			'object'=>$object,
			'fields'=>$fields,
			'title'=>'Fields',
			'link_color'=>DB::table('avalon')->where('id', '=', 1)->only('link_color'),
		));
	}

	public function post_add($object_id) {
		//add a new field

		$object = \Avalon\Object::find($object_id);

		//create table column
		Schema::table($object->table_name, function($table) {

			$field_name = Str::slug(Input::get('title'), '_');

			switch(Input::get('type')) {
				case 'checkbox':
					$field = $table->boolean($field_name);
					break;
					
				case 'color':
				case 'email':
				case 'text':
				case 'typeahead':
				case 'url':
				case 'url-local':
					$field = $table->string($field_name);
					break;

				case 'date':
					$field = $table->date($field_name);
					break;

				case 'date-time':
					$field = $table->timestamp($field_name);
					break;

				case 'integer':
					$field = $table->integer($field_name);
					break;

				case 'textarea-plain';
				case 'textarea-rich';
					$field = $table->text($field_name);
					break;
			}
			$field->nullable = (Input::get('required') != 'on');
		});

		//create field record in fields table
		$field = new \Avalon\Field(array(
			'title'      => Input::get('title'),
			'field_name' => Str::slug(Input::get('title'), '_'),
			'type'       => Input::get('type'),
			'visibility' => Input::get('visibility'),
			'additional' => Input::get('additional'),
			'required'   => ((Input::get('required') == 'on') ? 1 : 0),
			'active'	 => 1,
			'precedence' => \Avalon\Field::where('object_id', '=', $object_id)->max('precedence') + 1,
			'created_by' => Auth::user()->id,
			'updated_by' => Auth::user()->id
		));

		//attach field to object
		$object->fields()->insert($field);
		
		return Redirect::to_route('fields', $object_id);
	}

	public function post_reorder() {
		//use table_dnd to make an ajax request to reorder the fields for an object

		if (Request::ajax()) {
			$field_ids = explode(',', Input::get('ids'));
			$precedences = explode(',', Input::get('precedences'));
			sort($precedences);
			foreach ($field_ids as $field_id) {
				$field = \Avalon\Field::find($field_id);
				$field->precedence = array_shift($precedences);
				$field->save();
			}
    	}
	}

	public function put_edit($object_id, $field_id) {
		//edit an existing field.  not sure why i'm requiring the object_id

		$field = \Avalon\Field::find($field_id);
		$field->title 		= Input::get('title');
		$field->visibility	= Input::get('visibility');
		$field->additional	= Input::get('additional');
		$field->required	= (Input::get('required') == 'on') ? 1 : 0;
		$field->updated_by	= Auth::user()->id;
		$field->save();

		return Redirect::to_route('fields', $field->object_id);
	}

}