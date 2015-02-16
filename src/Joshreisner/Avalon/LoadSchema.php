<?php namespace Joshreisner\Avalon;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class LoadSchema extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'avalon:schema';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Load the database structure from schema file.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		\ObjectController::loadSchema();	
	}

}
