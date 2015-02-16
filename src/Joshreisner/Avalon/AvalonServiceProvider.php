<?php namespace Joshreisner\Avalon;

use App,
	Auth,
	Config,
	DateTime,
	DB,
	FileController,
	Form,
	Illuminate\Support\ServiceProvider,
	InstanceController;

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
	public static function saveImage($field_id, $file, $filename, $instance_id=null) {
		return FileController::saveImage($field_id, $file, $filename, $instance_id);
	}

	/**
	 * leaky abstraction!!
	 */
	public static function cleanupFiles($files=false) {
		return FileController::cleanup($files);
	}

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('joshreisner/avalon');
	
		//register schema command
        $this->app['schema'] = $this->app->share(function($app){
            return new LoadSchema();
        });
        $this->commands('schema');
        
		//capture last activity via updating on every page request
		/*too expensive, processing-wise?
		App::before(function() {
			if ($user = Auth::user()) {
				$user->last_login = new DateTime;
				$user->save();
			}
		});*/

		//add some special fields to the default laravel form class
		Form::macro('date', function($name, $value = null, $options = array()) {
		    $input =  '<input type="date" name="' . $name . '" value="' . $value . '"';

		    foreach ($options as $key => $value) {
		        $input .= ' ' . $key . '="' . $value . '"';
		    }

		    $input .= '>';

		    return $input;
		});

		Form::macro('datetime', function($name, $value = null, $options = array()) {
		    $input =  '<input type="datetime" name="' . $name . '" value="' . $value . '"';

		    foreach ($options as $key => $value) {
		        $input .= ' ' . $key . '="' . $value . '"';
		    }

		    $input .= '>';

		    return $input;
		});

		Form::macro('decimal', function($name, $value = null, $options = array()) {
		    $input =  '<input type="number" step="0.01" name="' . $name . '" value="' . $value . '"';

		    foreach ($options as $key => $value) {
		        $input .= ' ' . $key . '="' . $value . '"';
		    }

		    $input .= '>';

		    return $input;
		});

		Form::macro('integer', function($name, $value = null, $options = array()) {
		    $input =  '<input type="number" step="1" name="' . $name . '" value="' . $value . '"';

		    foreach ($options as $key => $value) {
		        $input .= ' ' . $key . '="' . $value . '"';
		    }

		    $input .= '>';

		    return $input;
		});

		# Currently not using; interferes with Chrome implementation of datetimepicker
		/*Form::macro('time', function($name, $value = null, $options = array()) {
		    $input =  '<input type="time" name="' . $name . '" value="' . $value . '"';

		    foreach ($options as $key => $value) {
		        $input .= ' ' . $key . '="' . $value . '"';
		    }

		    $input .= '>';

		    return $input;
		});*/

		include __DIR__ . '/../../routes.php';

	}

	/**
	 * Register the service provider. In this case, we're going to provide auto-generated models
	 * to the application for all the Avalon objects while only querying the db once
	 *
	 * @return void
	 */
	public function register()
	{
		//have to get config in a special way
		if (!defined('DB_FIELDS'))			define('DB_FIELDS',			Config::get('packages/joshreisner/avalon/config.db_fields'));
		if (!defined('DB_FILES'))			define('DB_FILES',			Config::get('packages/joshreisner/avalon/config.db_files'));
		if (!defined('DB_OBJECTS'))			define('DB_OBJECTS',		Config::get('packages/joshreisner/avalon/config.db_objects'));
		if (!defined('DB_OBJECT_LINKS'))	define('DB_OBJECT_LINKS',	Config::get('packages/joshreisner/avalon/config.db_object_links'));
		if (!defined('DB_OBJECT_USER'))		define('DB_OBJECT_USER',	Config::get('packages/joshreisner/avalon/config.db_object_user'));
		if (!defined('DB_USERS'))			define('DB_USERS',			Config::get('packages/joshreisner/avalon/config.db_users'));

		# Get required Avalon object/field data, error means migration/config needed
		try {
			$fields  = DB::table(DB_FIELDS)
						->join(DB_OBJECTS . ' as object', DB_FIELDS . '.object_id', '=', 'object.id')
						->leftJoin(DB_OBJECTS . ' as related', DB_FIELDS . '.related_object_id', '=', 'related.id')
						->select(
							DB_FIELDS . '.object_id',
							DB_FIELDS . '.related_object_id as related_id',
							DB_FIELDS . '.type as type',
							DB_FIELDS . '.name as field_name',
							'object.name as object_name', 
							'object.model as object_model', 
							'object.order_by as object_order_by',
							'object.direction as object_direction',
							'related.name as related_name', 
							'related.model as related_model', 
							'related.order_by as related_order_by',
							'related.direction as related_direction'
						)
						->orderBy('object.name')
						->get();
		} catch (\Exception $e) {
			//database not installed, don't interfere with migrations
			return false;
		}

		# Loop through and process the $fields into $objects for model methods below
		$objects = array();
		foreach ($fields as $field) {

			//make new empty object
			if (!isset($objects[$field->object_id])) $objects[$field->object_id] = array(
				'model' => $field->object_model,
				'name' => $field->object_name,
				'dates' => array('\'created_at\'', '\'updated_at\'', '\'deleted_at\''),
				'relationships' => array(),
			);
			if (!empty($field->related_model)) {
				if (!isset($objects[$field->related_id])) $objects[$field->related_id] = array(
					'model' => $field->related_model,
					'name' => $field->related_name,
					'dates' => array('\'created_at\'', '\'updated_at\'', '\'deleted_at\''),
					'relationships' => array(),
				);
			}

			//define relationships
			if ($field->type == 'select') {
				//this is legacy. i think we're worried about the universe folding in on itself
				if ($field->object_id == $field->related_id) continue;

				//out from this object
				$objects[$field->object_id]['relationships'][] = '
				public function ' . $field->related_name . '() {
					return $this->belongsTo("' . $field->related_model . '", "' . $field->field_name . '");
				}
				';

				//back from the related object
				$objects[$field->related_id]['relationships'][] = '
				public function ' . $field->object_name . '() {
					return $this->hasMany("' . $field->object_model . '", "' . $field->field_name . '");
				}
				';

			} elseif ($field->type == 'checkboxes') {

				//out from this object
				$objects[$field->object_id]['relationships'][] = '
				public function ' . $field->related_name . '() {
					return $this->belongsToMany("' . $field->related_model . '", "' . $field->field_name . '", "' . InstanceController::getKey($field->object_name) . '", "' . InstanceController::getKey($field->related_name) . '")->orderBy("' . $field->related_order_by . '", "' . $field->related_direction . '");
				}
				';
			
				//back from the related object
				$objects[$field->related_id]['relationships'][] = '
				public function ' . $field->object_name . '() {
					return $this->belongsToMany("' . $field->object_model . '", "' . $field->field_name . '", "' . InstanceController::getKey($field->related_name) . '", "' . InstanceController::getKey($field->object_name) . '")->orderBy("' . $field->object_order_by . '", "' . $field->object_direction . '");
				}
				';
			
			} elseif ($field->type == 'image') {

				$objects[$field->object_id]['relationships'][] = 'public function ' . substr($field->field_name, 0, -3) . '() {
					return $this->hasOne(\'AvalonFile\', \'id\', \'' . $field->field_name . '\');
				}';

			} elseif (in_array($field->type, array('date', 'datetime'))) {
				$objects[$field->object_id]['dates'][] = '\'' . $field->field_name . '\'';
			}
		}

		# Provide this class to extend below if needed
		eval('class AvalonFile extends Eloquent {
				protected $table = \'' . DB_FILES . '\';
			}');

		# Define object models (finally)
		foreach ($objects as $object) {

			eval('
			use Illuminate\Database\Eloquent\SoftDeletingTrait;

			class ' . $object['model'] . ' extends Eloquent {
			    use SoftDeletingTrait;
				
				public $table      = \'' . $object['name'] . '\'; //public intentionally
				protected $guarded = array();
				protected $dates   = array(' . implode(',', $object['dates']) . ');

				public static function boot() {
					parent::boot();
			        static::creating(function($object) {
						$object->precedence = DB::table(\'' . $object['name'] . '\')->max(\'precedence\') + 1;
						$object->created_by = Auth::id();
						$object->updated_by = Auth::id();
			        });
			        static::updating(function($object) {
						$object->updated_by = Auth::id();
			        });
				}

				public function creator() {
					return $this->belongsTo(\'User\', \'created_by\');
				}

				public function updater() {
					return $this->belongsTo(\'User\', \'updated_by\');
				}

				' . implode(' ', $object['relationships']) . '
			}');
		}
		//exit;
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