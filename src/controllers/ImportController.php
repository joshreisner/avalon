<?php

class ImportController extends BaseController {

	# Index view
	public function index() {

		$tables = array_diff(ObjectController::getTables(), self::getAvalonTables());
		foreach ($tables as &$table) $table = '\'' . $table . '\'';
		$tables = DB::select('SHOW TABLE STATUS WHERE Name IN (' . implode(',', $tables) . ')');
		foreach ($tables as &$table) {
			$table->link = URL::action('ImportController@setup', $table->Name);
			$table->Data_length = self::formatBytes($table->Data_length);
		}
		return View::make('avalon::import.index', compact('tables'));
	}

	# Setup view
	public function setup($table) {
		return $table;
	}

	private static function formatBytes($size, $precision=2) {
		$base = log($size, 1024);
		$suffixes = array('', 'k', 'M', 'G', 'T');
		return round(pow(1024, $base - floor($base)), $precision) . $suffixes[floor($base)];
	}


	private static function getAvalonTables() {
		//remove the objects
		return array_merge(
			DB::table(DB_OBJECTS)->lists('name'),
			DB::table(DB_FIELDS)->where('type', 'checkboxes')->lists('name'),
			[DB_FIELDS, DB_FILES, DB_OBJECT_LINKS, DB_OBJECT_USER, DB_OBJECTS, DB_USERS],
			['migrations'] //laravel
		);
	}

}