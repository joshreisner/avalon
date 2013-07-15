<?php

class Table {

	private static $rows		= array();
	private static $columns		= array();
	private static $deletable	= false;
	private static $draggable	= false;
	private static $grouped		= false;


	//add a column.  $trans is translation file key
	public function column($key, $type, $head) {
		self::$columns[] = array('key'=>$key, 'type'=>$type, 'head'=>$head);
		return $this;
	}


	//set table to be deletable
	public function deletable() {
		self::$deletable = true;
		return $this;
	}


	//set table to be draggable
	public function draggable($url) {
		self::$draggable = $url;
		return $this;
	}


	//draw the table
	public function draw() {

		//start up
		if (self::$draggable) array_unshift(self::$columns, array('head'=>'', 'type'=>'draggy'));
		if (self::$deletable) self::$columns[] = array('head'=>'', 'type'=>'delete');
		if (self::$grouped) $last_group = '';
		$colspan = count(self::$columns);
		$rowspan = count(self::$rows);

		//build <thead>
		$columns = self::$columns;
		foreach ($columns as &$column) $column = '<th class="' . $column['type'] . '">' . $column['head'] . '</th>';
		$columns = implode($columns);
		$head = '<thead><tr>' . $columns . '</tr></thead>';

		//build rows
		$rows = array();
		foreach (self::$rows as $row) {
			$columns = array();
			foreach (self::$columns as $column) {

				//handle groupings
				if (self::$grouped && ($last_group != $row->{self::$grouped})) {
					$last_group = $row->{self::$grouped};
					$rows[] = '<tr class="group"><td colspan=' . $colspan . '">' . $last_group . '</td></tr>';
				}

				//process value if necessary
				if ($column['type'] == 'draggy') {
					$value = '<i class="icon-reorder"></i>';
				} elseif ($column['type'] == 'delete') {
					$value = '<a href="' . $row->delete . '">' . ($row->active ? '<i class="icon-check"></i>' : '<i class="icon-check-empty"></i>') . '</a>';
				} else {
					$value	= $row->{$column['key']};
					if ($column['type'] == 'updated') {
						$value = Dates::relative($value);
					} else {
						if ($column['type'] == 'date') {
							$value = Dates::absolute($value);
						}
						if (isset($row->link)) {
							$value = '<a href="' . $row->link . '">' . $value . '</a>';
						}
					}
				}

				//create cell
				$columns[] = '<td class="' . $column['type'] . '">' . $value . '</td>';
			}

			//create row
			$rows[] = '<tr id="' . $row->id . '"' . (self::$deletable && !$row->active ? ' class="inactive"' : '') . '>' . implode($columns) . '</tr>';
		}

		//output
		return '<table class="table table-condensed' . (self::$draggable ? ' draggable" data-draggable-url="' . self::$draggable: '') . '">' .
					$head . 
					implode($rows) . 
				'</table>';

	}

	//set a key to group by
	public function groupBy($key) {
		self::$grouped = $key;
		return $this;
	}


	//always comes first.  $rows must be an object, eg a Laravel Query Builder resultset
	public static function rows($rows) {
		self::$rows = $rows;
		return new static;
	}
}