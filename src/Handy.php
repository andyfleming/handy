<?php
###########################################################################################################
#	Handy
#		Primarily provides backwards compability for some old code
#			Also, provides an alternative way to call static methods in model classes
#
#			Example:
#				Handy::lookupByID('Person',12)
#					-- instead of --
#				Person::lookupByID(12) 
#
###########################################################################################################

	class Handy {
		
		public static $db;
		
		
		
		public static function __callStatic($methodName, $arguments) {
			
			$compatibilityMethodMap = array(
				'get'		=> 'lookup',
				'getByID'	=> 'lookupByID',
				'getRandom' => 'lookupRandom',
				'getEach'	=> 'lookupEach'
			);
			
			// If they are using a deprecated method, let them know
			if (in_array($methodName,array_keys($compatibilityMethodMap))) {
				$msg = 'Handy::'.$methodName.' is now deprecated. Use Handy::'.$compatibilityMethodMap[$methodName].'.';
				trigger_error($msg,E_USER_WARNING);
			}
			
			
			$methodName = str_replace(array_keys($compatibilityMethodMap),$compatibilityMethodMap,$methodName);
			
			$modelName = array_shift($arguments);
			
			return call_user_func_array( $modelName.'::'.$methodName , $arguments );
			
		}
		
		public static function setDB($db) {
			Handy::$db = $db;
			return true;
		}
		
		public static function DB($db) { return Handy::setDB($db); }
		public static function db($db) { return Handy::setDB($db); }
	
	}
	
?>