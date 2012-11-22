<?php
class Avalon_Fields_Controller extends Controller {
	
	public $restful = true;
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

	public function get_add($id) {
		$object = \Avalon\Object::find($id);
		return View::make('avalon::fields.add', array(
			'object'=>$object,
			'field_types'=>$this->field_types,
			'title'=>'Add Field'
		));
	}
	
	public function get_list($id) {
		$object = \Avalon\Object::find($id);
		$fields = \Avalon\Field::where('object_id', '=', $object->id)->where('active', '=', 1)
			->get(array('id', 'title', 'field_name', 'type', 'updated_at'));
		
		foreach ($fields as $f) {
			$f->updated_at = \Avalon\Date::format($f->updated_at);
		}

		return View::make('avalon::fields.list', array(
			'object'=>$object,
			'fields'=>$fields,
			'title'=>'Fields'
		));
	}

	public function post_add($id) {

		$field = new \Avalon\Field(array(
			'title'      => Input::get('title'),
			'field_name' => Str::slug(Input::get('title')),
			'type'       => Input::get('type'),
			'visibility' => Input::get('visibility'),
			'required'   => ((Input::get('title') == 'on') ? 1 : 0),
			'active'	 => 1,
			'created_by' => Auth::user()->id
		));

		$object = \Avalon\Object::find($id);
		$object->fields()->insert($field);
		
		return Redirect::to_route('fields', $id);
	}
}