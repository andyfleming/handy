<?php
###########################################################################################################
#	Handy\SharedMethods
#		should be done as a trait, but easier for compatibility this way
###########################################################################################################

namespace andyfleming\handy;

	class HandySharedMethods {


	# ---------------------------------------------------------------------------------
	#	setDatabaseHandler()
	# ---------------------------------------------------------------------------------
	
		public function setDatabaseHandler() {
			
			$databaseHandlerVariableName = constant('HANDY_DATABASE_HANDLER_VARIABLE_NAME');
			
			//echo '<pre class="prePrint">'.print_r($GLOBALS[$databaseHandlerVariableName],true).'</pre>';
			$this->db =& $GLOBALS[$databaseHandlerVariableName];
			
		}

	}

?>