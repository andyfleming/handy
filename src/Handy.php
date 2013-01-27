<?php
###########################################################################################################
#	Handy
###########################################################################################################

namespace handy\handy;

	class Handy {
		
	# ---------------------------------------------------------------------------------
	#	Handy::get()
	#		returns a single "stuffed" model
	#
	#		Example Usage:
	#		Handy::get('WebsiteAccount','where statement')
	#
	# ---------------------------------------------------------------------------------
	
		public static function get($modelClassName,$whereQuery) {
			
			// Grab the database handler
			$databaseHandlerVariableName = constant('HANDY_DATABASE_HANDLER_VARIABLE_NAME');
			$db =& $GLOBALS[$databaseHandlerVariableName];
			
			
			// Create SQL statement
			$sql = "SELECT * FROM `".$modelClassName::TABLE_NAME."` WHERE {$whereQuery} LIMIT 1";
				
			// Run the query
			$results = $db->query($sql);
			
			// If there is an object found
			if (isset($results->num_rows) && $results->num_rows > 0) {
				
				// fetch it
				$item = $results->fetch_object();
				
				// inject it in the provided model class and RETURN IT
				return new $modelClassName($item);
			
			// Otherwise, return false
			} else { return false; }
				
		}
	
	# ---------------------------------------------------------------------------------
	#	Handy::getByID()
	#		returns a "stuffed" model by ID (an alias of sorts)
	#
	#		Example Usage:
	#		Handy\get('WebsiteAccount',123123)
	#
	# ---------------------------------------------------------------------------------
	
		public static function getByID($modelClassName,$id) {
			$id = (int) $id;
			return Handy::get($modelClassName,"`id` = '{$id}'");
		}
		
	# ---------------------------------------------------------------------------------
	#	Handy::getEach()
	#		returns multiple single "stuffed" model
	#
	#		Example Usage:
	#		Handy::get('WebsiteAccount','where statement')
	# ---------------------------------------------------------------------------------
	
		public static function getEach($modelClassName,$whereQuery=false) {
			
			// Grab the database handler
			$databaseHandlerVariableName = constant('HANDY_DATABASE_HANDLER_VARIABLE_NAME');
			$db =& $GLOBALS[$databaseHandlerVariableName];
			
			// Create SQL statement
			$sql = "SELECT * FROM `".$modelClassName::TABLE_NAME."`";
			
			// If there is a where statement passed, use it
			if ($whereQuery) {
				
				// If it is only an order by, don't prepend "WHERE"
				if (strtoupper(substr($whereQuery,0,8)) == 'ORDER BY') {
					$sql .= " {$whereQuery}";
				
				// Otherwise, prepend WHERE
				} else { $sql .= " WHERE {$whereQuery}"; }
				
			}
			
			// Run the query
			$results = $db->query($sql);
			
			// If there is an object found
			if (isset($results->num_rows) && $results->num_rows > 0) {
			
				$itemsToReturn = array();
				
				// fetch all
				while ($item = $results->fetch_object()) {
				
					// add to array and inject it in the provided model class
					$itemsToReturn[$item->id] = new $modelClassName($item);
				}
				
				return $itemsToReturn;
			
			// Otherwise, return false
			} else { return array(); }
			
		}
		
		
	# ---------------------------------------------------------------------------------
	#	Handy::count()
	#		
	# ---------------------------------------------------------------------------------
	
		public static function count($modelClassName,$whereQuery=false) {
			
			// Grab the database handler
			$databaseHandlerVariableName = constant('HANDY_DATABASE_HANDLER_VARIABLE_NAME');
			$db =& $GLOBALS[$databaseHandlerVariableName];
			
			// Create SQL statement
			$sql = "SELECT COUNT(`id`) as `count` FROM `".$modelClassName::TABLE_NAME."`";
			
			// If there is a where statement, include it
			if ($whereQuery) { $sql .= " WHERE {$whereQuery}"; }
			
			// Run the query
			$results = $db->query($sql);
			
			if (isset($results->num_rows) && $results->num_rows > 0) {
				
				$result = $results->fetch_object();
				
				if (isset($result->count)) {
					return $result->count;
				
				// Otherwise, return false
				} else { return false; }
				
			// Otherwise, return false
			} else { return false; }
			
		}
	
	# ---------------------------------------------------------------------------------
	#	Handy::create()
	#		creates new item in database and returns a single "stuffed" model
	#
	#		Example Usage:
	#		Handy::create('WebsiteAccount',array('field_name' => 'value'))
	#
	# ---------------------------------------------------------------------------------
	
		public static function create($modelClassName,$propertiesArray) {
			
			// Grab the database handler
			$databaseHandlerVariableName = constant('HANDY_DATABASE_HANDLER_VARIABLE_NAME');
			$db =& $GLOBALS[$databaseHandlerVariableName];
			
			
			// Create SQL statement
			$sql = "INSERT INTO `".$modelClassName::TABLE_NAME."` SET ";
		
			foreach ($propertiesArray as $col => $value) {
			
				// If it isn't the ID field
				if ($col != 'id') {
					
					// add it to the query
					$sql .= "`{$col}`='";
					$sql .= $db->real_escape_string($value);
					$sql .= "', ";
				}
			}
			
			// Trim off the extra ", "
			$sql = rtrim($sql,', ');
			//echo $sql;
			
			// Attempt to insert the item
			if (!$db->query($sql)) { return false; } else { 
				
				$newItem = Handy::getByID($modelClassName,$db->insert_id);
				
				if (method_exists($newItem,'__postCreate')) { $newItem->__postCreate(); }
				
				return $newItem;
				
			}
				
		}
		
		
	}	
	
?>