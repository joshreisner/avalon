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
		
		include __DIR__.'/../../routes.php';

		//otherwise URL placeholder is Http://
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
		//
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