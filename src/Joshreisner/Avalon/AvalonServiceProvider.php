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
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('joshreisner/avalon');
		
		include __DIR__ . '/../../routes.php';

		//Former Config -- otherwise URL placeholder is Http://
		\Config::set('former::translatable', array(
			'help', 'inlineHelp', 'blockHelp', 'label'
		));

		\Config::set('former::required_text', '');

    
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{

		//register avalon objects as models for yr application
		foreach (\DB::table('avalon_objects')->get() as $object) {

			//hasMany relationships
			$hasMany = '';
			$related_fields = \DB::table('avalon_fields')
					->where('related_object_id', $object->id)
					->join('avalon_objects', 'avalon_fields.object_id', '=', 'avalon_objects.id')
					->select(
						'avalon_objects.name as object_name', 
						'avalon_objects.model', 
						'avalon_fields.name as field_name',
						'avalon_objects.order_by',
						'avalon_objects.direction'
					)->get();
			foreach ($related_fields as $field) {
				$hasMany .= 'public function ' . $field->object_name . '() {
					return $this->hasMany("' . $field->model . '", "' . $field->field_name . '")->active()->orderBy("' . $field->order_by . '", "' . $field->direction . '");
				}';
			}

			//define model
			eval('class ' . $object->model . ' extends Eloquent {
				protected $table = "' . $object->name . '";
				public function scopeActive($query) {
					return $query->where("active", 1);
				}
				' . $hasMany . '
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