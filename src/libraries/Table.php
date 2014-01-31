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
	public function draw($class=false) {

		if ($class) $class = ' ' . $class;
		
		//start up
		if (self::$draggable) array_unshift(self::$columns, array('head'=>'', 'type'=>'draggy'));
		if (self::$deletable) self::$columns[] = array('head'=>'', 'type'=>'delete');
		if (self::$grouped) $last_group = '';
		$colspan = count(self::$columns);
		$rowspan = count(self::$rows);

		//build <thead>
		$columns = self::$columns;
		foreach ($columns as &$column) {
			if ($column['type'] == 'color') $column['head'] = '&nbsp;';
			$column = '<th class="' . $column['type'] . '">' . $column['head'] . '</th>';
		}
		$columns = implode($columns);
		$head = '<thead><tr>' . $columns . '</tr></thead>';

		//build rows
		$bodies = $rows = array();
		foreach (self::$rows as $row) {
			$columns = array();
			$link = true;
			foreach (self::$columns as $column) {

				//handle groupings
				if (self::$grouped && ($last_group != $row->{self::$grouped})) {
					$last_group = $row->{self::$grouped};
					$bodies[] = '<tbody>' . implode($rows) . '</tbody>';
					$bodies[] = '<tr class="group"><td colspan=' . $colspan . '">' . $last_group . '</td></tr>';
					$rows = array();
				}

				//process value if necessary
				if ($column['type'] == 'draggy') {
					$value = '<i class="glyphicon glyphicon-align-justify"></i>';
				} elseif ($column['type'] == 'delete') {
					$value = '<a href="' . $row->delete . '">' . (!$row->deleted_at ? '<i class="glyphicon glyphicon-ok-circle"></i>' : '<i class="glyphicon glyphicon-remove-circle"></i>') . '</a>';
				} else {
					$value = Str::limit(strip_tags($row->{$column['key']}));
					if ($column['type'] == 'updated_at') {
						$value = Dates::relative($value);
					} elseif ($column['type'] == 'time') {
						$value = Dates::time($value);
					} elseif ($column['type'] == 'date') {
						$value = Dates::absolute($value);
					} elseif ($column['type'] == 'datetime') {
						$value = Dates::absolute($value);
					}

					if (isset($row->link) && $link) {
						if ($column['type'] == 'color') {
							$value = '<a href="' . $row->link . '" style="background-color: ' . $value . '"></a>';
						} else {
							if ($value == '') $value = '&hellip;';
							$value = '<a href="' . $row->link . '">' . $value . '</a>';
							$link = false;
						}
					}
				}

				//create cell
				$columns[] = '<td class="' . $column['type'] . '">' . $value . '</td>';
			}

			//create row
			$rows[] = '<tr id="' . $row->id . '"' . (self::$deletable && $row->deleted_at ? ' class="inactive"' : '') . '>' . implode($columns) . '</tr>';
		}

		$bodies[] = '<tbody>' . implode($rows) . '</tbody>';

		//output
		return '<table class="table table-condensed' . $class . (self::$draggable ? ' draggable" data-draggable-url="' . self::$draggable : '') . '">' .
					$head . 
					implode($bodies) . 
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