<?php
###########################################################################################################
#	Handy\Model
###########################################################################################################

namespace andyfleming\handy;

	class Model extends HandySharedMethods {
		
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
	}

?>