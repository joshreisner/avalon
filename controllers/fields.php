<?php
class Avalon_Fields_Controller extends Controller {
	
	public $restful = true;

	//declare acceptable field types.  need to reconsider how this is bound to the rest of the app
	private $field_types = array(
				'Checkbox',
				'Checkboxes',
				'Color',
				'Date',
				'Date & Time',
				'Email',
				'Dropdown',
				'File',
				'File Size',
				'File Type',
				'Image',
				'Integer',
				'Latitude',
				'Text',
				'Textarea (Rich)',
				'Textarea (Plain)',
				'Typeahead',
				'URL',
				'URL (Local)'
			);

	public function get_add($object_id) {
		//display the add new field form

		$object = \Avalon\Object::find($object_id);
		return View::make('avalon::fields.add', array(
			'object'=>$object,
			'field_types'=>$this->field_types,
			'title'=>'Add Field'
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
			'title'=>$field->title
		));
	}

	public function get_list($object_id) {
		//display the list of fields

		$object = \Avalon\Object::find($object_id);
		$fields = \Avalon\Field::where('object_id', '=', $object_id)->where('active', '=', 1)->order_by('precedence')->get(array('id', 'title', 'field_name', 'type', 'precedence', 'updated_at'));
		
		foreach ($fields as $f) {
			$f->updated_at = \Avalon\Date::format($f->updated_at);
		}

		return View::make('avalon::fields.list', array(
			'object'=>$object,
			'fields'=>$fields,
			'title'=>'Fields'
		));
	}

	public function post_add($object_id) {
		//add a new field

		$field = new \Avalon\Field(array(
			'title'      => Input::get('title'),
			'field_name' => Str::slug(Input::get('title')),
			'type'       => Input::get('type'),
			'visibility' => Input::get('visibility'),
			'additional' => Input::get('additional'),
			'required'   => ((Input::get('required') == 'on') ? 1 : 0),
			'active'	 => 1,
			'precedence' => \Avalon\Field::max('precedence') + 1,
			'created_by' => Auth::user()->id,
			'updated_by' => Auth::user()->id
		));
		$object = \Avalon\Object::find($object_id);
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
		$field->type 		= Input::get('type');
		$field->visibility	= Input::get('visibility');
		$field->additional	= Input::get('additional');
		$field->required	= (Input::get('required') == 'on') ? 1 : 0;
		$field->updated_by	= Auth::user()->id;
		$field->save();

		return Redirect::to_route('fields', $field->object_id);
	}

}