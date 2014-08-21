<?php namespace Joshreisner\Avalon;

use Illuminate\Support\ServiceProvider;

class AvalonServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;


	/**
	 * make this function available (maybe should be on the model though?)
	 */
	public static function saveImage($field_id, $file, $filename, $extension, $instance_id=null) {
		return \FileController::saveImage($field_id, $file, $filename, $extension, $instance_id);
	}

	/**
	 * leaky abstraction!!
	 */
	public static function cleanupFiles($files=false) {
		return \FileController::cleanup($files);
	}

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
		if (!defined('DB_FIELDS'))			define('DB_FIELDS',			\Config::get('packages/joshreisner/avalon/config.db_fields'));
		if (!defined('DB_FILES'))			define('DB_FILES',			\Config::get('packages/joshreisner/avalon/config.db_files'));
		if (!defined('DB_OBJECTS'))			define('DB_OBJECTS',		\Config::get('packages/joshreisner/avalon/config.db_objects'));
		if (!defined('DB_OBJECT_LINKS'))	define('DB_OBJECT_LINKS',	\Config::get('packages/joshreisner/avalon/config.db_object_links'));
		if (!defined('DB_USERS'))			define('DB_USERS',			\Config::get('packages/joshreisner/avalon/config.db_users'));

		if (!\Schema::hasTable(DB_OBJECTS)) return;

		eval('class AvalonFile extends Eloquent {
				protected $table = \'' . DB_FILES . '\';
			}');

		//register avalon objects as models for yr application
		foreach (\DB::table(DB_OBJECTS)->get() as $object) {

			//relationships
			$relationships = array();

			//to the related object
			$related_fields = \DB::table(DB_FIELDS)
					->where('object_id', $object->id)
					->where('related_object_id', '<>', $object->id)
					->whereNotNull('related_object_id')
					->join(DB_OBJECTS, DB_FIELDS . '.related_object_id', '=', DB_OBJECTS . '.id')
					->select(
						DB_FIELDS . '.type as type',
						DB_FIELDS . '.name as field_name',
						DB_OBJECTS . '.name as object_name', 
						DB_OBJECTS . '.model', 
						DB_OBJECTS . '.order_by',
						DB_OBJECTS . '.direction'
					)->get();
			foreach ($related_fields as $field) {
				if ($field->type == 'select') {
					$relationships[] = 'public function ' . $field->object_name . '() {
						return $this->belongsTo("' . $field->model . '", "' . $field->field_name . '");
					}';
				}
			}

			//from the related object
			$related_fields = \DB::table(DB_FIELDS)
					->where('related_object_id', $object->id)
					->join(DB_OBJECTS, DB_FIELDS . '.object_id', '=', DB_OBJECTS . '.id')
					->select(
						DB_FIELDS . '.type as type',
						DB_FIELDS . '.name as field_name',
						DB_OBJECTS . '.name as object_name', 
						DB_OBJECTS . '.model', 
						DB_OBJECTS . '.order_by',
						DB_OBJECTS . '.direction'
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
			$related_fields = \DB::table(DB_FIELDS)
					->where('object_id', $object->id)
					->whereIn('type', array('checkboxes'))
					->join(DB_OBJECTS, DB_FIELDS . '.related_object_id', '=', DB_OBJECTS . '.id')
					->select(
						DB_FIELDS . '.type as type',
						DB_FIELDS . '.name as field_name',
						DB_OBJECTS . '.name as object_name', 
						DB_OBJECTS . '.model', 
						DB_OBJECTS . '.order_by',
						DB_OBJECTS . '.direction'
					)->get();
			foreach ($related_fields as $field) {
				$relationships[] = 'public function ' . $field->object_name . '() {
					return $this->belongsToMany("' . $field->model . '", "' . $field->field_name . '", "' . \InstanceController::getKey($object->name) . '", "' . \InstanceController::getKey($field->object_name) . '")->orderBy("' . $field->order_by . '", "' . $field->direction . '");
				}';
			}

			$images = \DB::table(DB_FIELDS)
				->where('object_id', $object->id)
				->whereIn('type', array('image'))
				->select('name')
				->get();
			foreach ($images as $image)	 {
				$relationships[] = 'public function ' . substr($image->name, 0, -3) . '() {
					return $this->hasOne("AvalonFile", "id", "' . $image->name . '");
				}';
			}

			$dates = \DB::table(DB_FIELDS)
				->where('object_id', $object->id)
				->whereIn('type', array('date', 'datetime'))
				->lists('name');
			$dates = $dates + array('created_at', 'updated_at', 'deleted_at');
			foreach ($dates as &$date) $date = '\'' . $date . '\'';
			$dates = implode(',', $dates);

			//define model
			eval('
			use Illuminate\Database\Eloquent\SoftDeletingTrait;

			class ' . $object->model . ' extends Eloquent {
			    use SoftDeletingTrait;
				
				public $table = \'' . $object->name . '\'; //public intentionally
				protected $guarded = array();
				protected $dates = array(' . $dates . ');

				public static function boot() {
					parent::boot();
			        static::creating(function($object)
			        {
						$object->precedence = DB::table(\'' . $object->name . '\')->max(\'precedence\') + 1;
						$object->updated_by = Auth::id();
			        });
			        static::updating(function($object)
			        {
						$object->updated_by = Auth::id();
			        });
				}

				' . implode(' ', $relationships) . '
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