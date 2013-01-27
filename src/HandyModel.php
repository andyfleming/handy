<?php
###########################################################################################################
#	HandyModel
###########################################################################################################

	abstract class HandyModel {
		
	# ---------------------------------------------------------------------------------
	#	Variable Declarations
	# ---------------------------------------------------------------------------------
		
		public	$data;			// where the data actually lives
		private $originalData;	// a private copy of the data for comparison
		
	# ---------------------------------------------------------------------------------
	#	__construct()
	#		This class is inteded to be extended but not required
	#		If it isn't extended, it needs to have a table name passed
	# ---------------------------------------------------------------------------------
	
		final public function __construct($dataObject) {
			
			// Set local database handler refernece
			$this->setDatabaseHandler();
			
			// Store the data from the database in its own variable
			$this->data			= clone $dataObject;
			$this->originalData = clone $dataObject;
			
			// If an extensionConstruct method exists, call it
			if (method_exists($this,'__extensionConstruct')) { $this->__extensionConstruct(); }
			
			
		}
		

	# ---------------------------------------------------------------------------------
	#	setDatabaseHandler()
	# ---------------------------------------------------------------------------------
	
		public function setDatabaseHandler() {
			
			$databaseHandlerVariableName = constant('HANDY_DATABASE_HANDLER_VARIABLE_NAME');
			
			//echo '<pre class="prePrint">'.print_r($GLOBALS[$databaseHandlerVariableName],true).'</pre>';
			$this->db =& $GLOBALS[$databaseHandlerVariableName];
			
		}

	
	# ---------------------------------------------------------------------------------
	#	save()
	#		automatically saves and column that has changed (other than ID)
	#		- uses $this->originalData for comparison (checking what fields to save)
	# ---------------------------------------------------------------------------------
	
		final public function save() {
			
			// Check that there is a table name set
			$thisClassName = get_class($this);
			$tableName = $thisClassName::TABLE_NAME;
			if (empty($tableName)) { return false; }
			
			// Make sure there is an ID with which to save
			if (empty($this->data->id)) { return false; }
			
			// Calculate whether there have been changes made to the data
			$diff = array_diff_assoc(
				((array) $this->data),
				((array) $this->originalData)
			);
						
			// If there are changes to be saved, continue
			if (count($diff) > 0) {
			
				// Start SQL statement
				$sql = "UPDATE `{$tableName}` SET ";
				
				foreach ($this->data as $col => $value) {
				
					// If it isn't the ID field
					// (and it is different from what is originally loaded),
					if ($col != 'id' && $value != $this->originalData->$col) {
						
						// add it to the query
						$sql .= "`{$col}`='";
						$sql .= $this->db->real_escape_string($value);
						$sql .= "', ";
					}
				}
				
				
				// Trim off the extra ", "
				$sql = rtrim($sql,', ');
				
				// Add the where clause for ID
				$sql .= " WHERE `id`='{$this->data->id}' LIMIT 1";
				
				if ($this->db->query($sql)) { return true; }
				else { return false; }
			
			// If there aren't changes, return true	
			} else { return true; }
						
						
		}
		
	# ---------------------------------------------------------------------------------
	#	delete()
	#		an easy way to delete a model with $item->delete();
	# ---------------------------------------------------------------------------------
	
		final public function delete() {
		
			$thisClassName = get_class($this);
			$tableName = $thisClassName::TABLE_NAME;
			if (empty($tableName)) { return false; }
		
			// Create the SQL
			$sql = "DELETE FROM `{$tableName}` WHERE id = '{$this->data->id}' LIMIT 1";
			
			// Return result
			if ($this->db->query($sql)) { return true; }
			else { return false; }
			
		}
		
	
	# ---------------------------------------------------------------------------------
	#	get()
	#		alias for getting properties in $this->data->$x
	# ---------------------------------------------------------------------------------
	
		final public function get($propertyName) {
			
			return $this->data->$propertyName;
			
		}
	
	# ---------------------------------------------------------------------------------
	#	set()
	# ---------------------------------------------------------------------------------
	
		final public function set($propertyNameOrArray,$value='') {
			
			// If there is an array passed, set multiple
			if (is_array($propertyNameOrArray)) {
				
				foreach	($propertyNameOrArray as $name => $value) {
					$this->data->$name = $value;
				}
				
			// Otherwise, set the single property
			} else { $this->data->$propertyNameOrArray = $value; }
			
			return true;
			
		}
		
	# ---------------------------------------------------------------------------------
	#	notEmpty()
	#		
	# ---------------------------------------------------------------------------------
	
		public function notEmpty($propertyName) {
			
			return (!empty($this->data->$propertyName));
			
		}
		

###########################################################################################################
#	Static Methods
###########################################################################################################

	# ---------------------------------------------------------------------------------
	#	ModelName::get()
	#		returns a single "stuffed" model
	#
	#		Example Usage:
	#		Handy::get('WebsiteAccount','where statement')
	#
	# ---------------------------------------------------------------------------------
	
		public static function get($whereQuery=false) {
			
			$modelClassName = __CLASS__;
			
			// Grab the database handler
			$databaseHandlerVariableName = constant('HANDY_DATABASE_HANDLER_VARIABLE_NAME');
			$db =& $GLOBALS[$databaseHandlerVariableName];
			
			
			// Create SQL statement
			if ($whereQuery) {
				$sql = "SELECT * FROM `".$modelClassName::TABLE_NAME."` WHERE {$whereQuery} LIMIT 1";
			} else {
				$sql = "SELECT * FROM `".$modelClassName::TABLE_NAME."` LIMIT 1";
			}
				
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
	#		Handy::getByID('WebsiteAccount',123123)
	#
	# ---------------------------------------------------------------------------------
	
		public static function getByID($id) {
			
			$modelClassName = __CLASS__;
		
			$id = (int) $id;
			return Handy::get($modelClassName,"`id` = '{$id}'");
		}
		
	
		
	# ---------------------------------------------------------------------------------
	#	Handy::deleteByID()
	#		deletes an item by ID
	#
	#		Example Usage:
	#		Handy::deleteByID('WebsiteAccount',123123)
	#
	# ---------------------------------------------------------------------------------
	
		public static function deleteByID($id) {
		
			$modelClassName = __CLASS__;
					
			$id = (int) $id;
			
			$databaseHandlerVariableName = constant('HANDY_DATABASE_HANDLER_VARIABLE_NAME');
			$db =& $GLOBALS[$databaseHandlerVariableName];
			
			// Create SQL statement
			$sql = "SELECT * FROM `".$modelClassName::TABLE_NAME."` WHERE `id`='{$id}' LIMIT 1";
				
			// Run the query
			return $db->query($sql);
			
		}
		
	
		
	# ---------------------------------------------------------------------------------
	#	Handy::getEach()
	#		returns multiple single "stuffed" model
	#
	#		Example Usage:
	#		Handy::get('WebsiteAccount','where statement')
	# ---------------------------------------------------------------------------------
	
		public static function getEach($whereQuery=false) {
			
			// Grab the database handler
			$databaseHandlerVariableName = constant('HANDY_DATABASE_HANDLER_VARIABLE_NAME');
			$db =& $GLOBALS[$databaseHandlerVariableName];
			
			$modelClassName = __CLASS__;		
			
				
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
	
		public static function count($whereQuery=false) {
			
			$modelClassName = __CLASS__;
			
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
	
		public static function create($propertiesArray) {
			
			$modelClassName = __CLASS__;
			
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