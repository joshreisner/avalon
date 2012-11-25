<?php
class Avalon_Objects_Controller extends Controller {
	
	public $restful = true;
	
	public function get_add() {		
		//display add new form

		return View::make('avalon::objects.add', array(
			'list_groupings'=>$this->list_groupings(),
			'title'=>'Add Object'
		));
	}
	
	public function get_edit($id) {
		//display edit form

		$object = \Avalon\Object::find($id);
		return View::make('avalon::objects.edit', array(
			'object'=>$object,
			'list_groupings'=>$this->list_groupings(),
			'title'=>'Object Settings'
		));
	}
	
	public function get_list() {
		//display a list of objdcts on the home page

		$user = Auth::user();
		$objects = \Avalon\Object::where('active','=',1)->order_by('list_grouping')->order_by('title')->get(array('id', 'title', 'table_name', 'list_grouping'));

		foreach ($objects as $o) {
			$o->count = DB::table($o->table_name)->where('active', '=', 1)->count();
			if ($latest = DB::table($o->table_name)->where('active', '=', 1)->order_by('updated_at', 'DESC')->first()) {
				$o->updated_at = \Avalon\Date::format($latest->updated_at);
				$o->updated_by = 'Josh';
			}
		}

		return View::make('avalon::objects.list', array(
			'user'=>$user,
			'objects'=>$objects,
			'title'=>'Objects'
		));
	}

	private function list_groupings() {
		//create a JSON array to tell Boostrap what values to suggest for the List Grouping field
		$list_groupings = array();
		$objects = \Avalon\Object::where('active', '=', 1)->where('list_grouping', '<>', '')->group_by('list_grouping')->get(array('list_grouping'));
		foreach ($objects as $o) $list_groupings[] = $o->list_grouping;
		return htmlentities(json_encode($list_groupings));
	}

	public function post_add() {
		//add a new object

		$object = new \Avalon\Object;
		$object->title 			= Input::get('title');
		$object->list_grouping 	= Input::get('list_grouping');
		$object->table_name 	= Str::slug($object->title, '_');
		$object->order_by	 	= 'created_at';
		$object->direction 		= 'ASC';
		$object->created_by 	= Auth::user()->id;
		$object->updated_by		= Auth::user()->id;
		$object->active 		= 1;
		$object->save();
		
		//create empty table (just metadata)
		Schema::table($object->table_name, function($table) {
		    $table->create();
		    $table->increments('id');
			$table->integer('created_by');
			$table->integer('updated_by');
			$table->boolean('active');
			$table->integer('precedence');
		    $table->timestamps();
		});

		return Redirect::to_route('objects');
	}
	
	public function put_edit($id) {
		//save changes to an object

		$object = \Avalon\Object::find($id);

		//rename the table if necessary
		$table_name = Str::slug(Input::get('table_name'), '_');
		if ($object->table_name != $table_name) Schema::rename($object->table_name, $table_name);
		
		$object->title 			= Input::get('title');
		$object->list_grouping 	= Input::get('list_grouping');
		$object->order_by		= Input::get('order_by');
		$object->direction		= Input::get('direction');
		$object->show_published	= ((Input::get('show_published') == 'on') ? 1 : 0);
		$object->table_name 	= $table_name;
		$object->list_help 		= Input::get('list_help');
		$object->form_help 		= Input::get('form_help');
		$object->save();

		return Redirect::to_route('instances', $object->id);
	}
}