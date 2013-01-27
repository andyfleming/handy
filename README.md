#Handy

Handy is a utility for managing MySQL models, etc.

## Quick Start

**person.class.php**

```php

public class Person extends Handy\Handy\Model {
	const TABLE_NAME = 'people';
}
```

**example.php**

```php

# ––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––
#	Setup
# ––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––

	// Setup database handler
	$dbh = new mysqli( $host, $user, $pass, $database );

	// Set above variable name for Handy
	define('HANDY_DATABASE_HANDLER_VARIABLE_NAME', 'dbh');

	// Require class
	require "person.class.php";


# ––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––
#	Usage
# ––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––
	
	// Get each person over 30
	$people = Person::lookupEach("age > 30");
	
	// Handy returns an array of Person objects by ID (or empty array)
	if (count($people) > 0) {
	    foreach ($people as $person) {
	    	echo $person->get('first_name').": ".$person->get('age');
	        echo "<br />";
	    }
	}
	
```

## Documentation

* [Basic Usage](#basic-usage)
	* [Basic Item Class](#basic-item-class)
	* [Item Creation](#item-creation)
	* [Item Lookup by ID](#item-lookup-by-id)
	* [Item Lookup WHERE](#item-lookup-where)
	* [Multiple Item Lookup by WHERE](#multiple-item-lookup-by-where)



## Basic Usage

### Basic Item Class

```php
public class Person extends HandyModel {
	
	const TABLE_NAME = 'people';
	
}

```





### Item Creation

```php
Person::create(array(
	'first_name' => 'John',
	'last_name' => 'Smith'
);
```

### Item Lookup by ID

ModelName::lookupByID( *ID* )

```php
$person = Person::lookupByID(12);

// Returns single Person object or false
if (!$person) {
	echo "Person 12 was not found!";
} else {
	echo "Person 12's first name is ".$person->get('first_name');
}

```


### Item Lookup by WHERE

ModelName::lookup( *WHERE clause* )

```php
$person = Person::get("`first_name`='Bob'");

// Returns single Person object or false
```

### Multiple Item Lookup by WHERE

ModelName::lookupEach( *WHERE clause* )

```php
$people = Person::lookupEach("age > 30");

// Returns array of Person objects by ID or empty array

if (count($people) == 0) {
	echo 'No one found!';
} else {
	foreach ($people as $person) {
		echo $person->get('first_name').": ".$person->get('age');
		echo "<br />";
	}
}

/*

Array people
	17 => Object Person
	27 => Object Person
	28 => Object Person

*/

```

## Advanded Usage

### __extensionConstruct()
### __postCreate()


## Goals and the Future

* Possibly migrate from MySQLI to PDO
* Consider expanding functionality to include other database types other than MySQL


## Changelog

1.1.1 - Fixes and updated README

1.1.0 - Simplification of calls. Static methods moved to main model class ```Handy::getByID('Person',12)``` is now ```Person::getByID(12)```

1.0.1 - Fixes, etc

1.0.0 - Initial Release
