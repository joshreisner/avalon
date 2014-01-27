<?php namespace Joshreisner\Avalon;

use Illuminate\Support\ServiceProvider;

class AvalonUpload extends \Eloquent {
	protected $table = 'avalon_uploads';
}

class AvalonServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('joshreisner/avalon');
		
		include __DIR__ . '/../../routes.php';

		//add some special fields to the default laravel form class
		\Form::macro('date', function($name, $value = null, $options = array()) {
		    $input =  '<input type="date" name="' . $name . '" value="' . $value . '"';

		    foreach ($options as $key => $value) {
		        $input .= ' ' . $key . '="' . $value . '"';
		    }

		    $input .= '>';

		    return $input;
		});

		\Form::macro('datetime', function($name, $value = null, $options = array()) {
		    $input =  '<input type="datetime" name="' . $name . '" value="' . $value . '"';

		    foreach ($options as $key => $value) {
		        $input .= ' ' . $key . '="' . $value . '"';
		    }

		    $input .= '>';

		    return $input;
		});

		\Form::macro('integer', function($name, $value = null, $options = array()) {
		    $input =  '<input type="number" step="1" name="' . $name . '" value="' . $value . '"';

		    foreach ($options as $key => $value) {
		        $input .= ' ' . $key . '="' . $value . '"';
		    }

		    $input .= '>';

		    return $input;
		});

		\Form::macro('time', function($name, $value = null, $options = array()) {
		    $input =  '<input type="time" name="' . $name . '" value="' . $value . '"';

		    foreach ($options as $key => $value) {
		        $input .= ' ' . $key . '="' . $value . '"';
		    }

		    $input .= '>';

		    return $input;
		});

		\View::composer('*', function($view)
		{
		    $view->with('account', \DB::table('avalon')->where('id', 1)->first());
		});


	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{

		if (!\Schema::hasTable('avalon')) return;

		//register avalon objects as models for yr application
		foreach (\DB::table('avalon_objects')->get() as $object) {

			//relationships
			$relationships = array();

			//from the related object
			$related_fields = \DB::table('avalon_fields')
					->where('related_object_id', $object->id)
					->join('avalon_objects', 'avalon_fields.object_id', '=', 'avalon_objects.id')
					->select(
						'avalon_fields.type as type',
						'avalon_fields.name as field_name',
						'avalon_objects.name as object_name', 
						'avalon_objects.model', 
						'avalon_objects.order_by',
						'avalon_objects.direction'
					)->get();
			foreach ($related_fields as $field) {
				if ($field->type == 'select') {
					$relationships[] = 'public function ' . $field->object_name . '() {
						return $this->hasMany("' . $field->model . '", "' . $field->field_name . '")->orderBy("' . $field->order_by . '", "' . $field->direction . '");
					}';
				} elseif ($field->type == 'checkboxes') {
					$relationships[] = 'public function ' . $field->object_name . '() {
						return $this->belongsToMany("' . $field->model . '", "' . $field->field_name . '", "' . \InstanceController::getKey($object->name) . '", "' . \InstanceController::getKey($field->object_name) . '")->orderBy("' . $field->order_by . '", "' . $field->direction . '");
					}';
				}
			}

			//also need many-to-many from the object
			$related_fields = \DB::table('avalon_fields')
					->where('object_id', $object->id)
					->whereIn('type', array('checkboxes'))
					->join('avalon_objects', 'avalon_fields.related_object_id', '=', 'avalon_objects.id')
					->select(
						'avalon_fields.type as type',
						'avalon_fields.name as field_name',
						'avalon_objects.name as object_name', 
						'avalon_objects.model', 
						'avalon_objects.order_by',
						'avalon_objects.direction'
					)->get();
			foreach ($related_fields as $field) {
				$relationships[] = 'public function ' . $field->object_name . '() {
					return $this->belongsToMany("' . $field->model . '", "' . $field->field_name . '", "' . \InstanceController::getKey($object->name) . '", "' . \InstanceController::getKey($field->object_name) . '")->orderBy("' . $field->order_by . '", "' . $field->direction . '");
				}';
			}

			//define model
			eval('class ' . $object->model . ' extends Eloquent {
				protected $table = "' . $object->name . '";
				protected $guarded = array();
			    protected $softDelete = true;
				public $object_id = "' . $object->id . '";

				public static function boot() {
			        static::creating(function($object)
			        {
			        	$object->precedence = DB::table(\'' . $object->name . '\')->max(\'precedence\') + 1;
			            //$object->created_by = Auth::user()->id;
			            //$object->updated_by = Auth::user()->id;
			        });
				}

				' /* save this for later
				public function uploads() {
					return $this->hasMany("AvalonUpload", "instance_id")->where("table", "' . $object->name . '");
				}
				'*/ . implode(' ', $relationships) . '
			}');
		}

	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}