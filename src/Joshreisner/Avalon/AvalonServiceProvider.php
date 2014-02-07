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

	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		//have to get config in a special way
		$db_prefix = \Config::get('packages/joshreisner/avalon/config.db_prefix');

		//register avalon objects as models for yr application
		foreach (\DB::table($db_prefix .'objects')->get() as $object) {

			//relationships
			$relationships = array();

			//to the related object
			$related_fields = \DB::table($db_prefix . 'fields')
					->where('object_id', $object->id)
					->where('related_object_id', '<>', $object->id)
					->whereNotNull('related_object_id')
					->join($db_prefix . 'objects', $db_prefix . 'fields.related_object_id', '=', $db_prefix . 'objects.id')
					->select(
						$db_prefix . 'fields.type as type',
						$db_prefix . 'fields.name as field_name',
						$db_prefix . 'objects.name as object_name', 
						$db_prefix . 'objects.model', 
						$db_prefix . 'objects.order_by',
						$db_prefix . 'objects.direction'
					)->get();
			foreach ($related_fields as $field) {
				if ($field->type == 'select') {
					$relationships[] = 'public function ' . $field->object_name . '() {
						return $this->belongsTo("' . $field->model . '", "' . $field->field_name . '");
					}';
				}
			}

			//from the related object
			$related_fields = \DB::table($db_prefix . 'fields')
					->where('related_object_id', $object->id)
					->join($db_prefix . 'objects', $db_prefix . 'fields.object_id', '=', $db_prefix . 'objects.id')
					->select(
						$db_prefix . 'fields.type as type',
						$db_prefix . 'fields.name as field_name',
						$db_prefix . 'objects.name as object_name', 
						$db_prefix . 'objects.model', 
						$db_prefix . 'objects.order_by',
						$db_prefix . 'objects.direction'
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
			$related_fields = \DB::table($db_prefix . 'fields')
					->where('object_id', $object->id)
					->whereIn('type', array('checkboxes'))
					->join($db_prefix . 'objects', $db_prefix . 'fields.related_object_id', '=', $db_prefix . 'objects.id')
					->select(
						$db_prefix . 'fields.type as type',
						$db_prefix . 'fields.name as field_name',
						$db_prefix . 'objects.name as object_name', 
						$db_prefix . 'objects.model', 
						$db_prefix . 'objects.order_by',
						$db_prefix . 'objects.direction'
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