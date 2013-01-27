<?php
###########################################################################################################
#	Handy
###########################################################################################################

	class Handy {
		
		public static function __callStatic($methodName, $arguments) {
			
			$modelName = array_shift($arguments);
			
			$modelName::$methodName($arguments);
			
		}
		
	}
	
?>