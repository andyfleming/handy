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
	$people = Handy::getEach('Person',"age > 30");
	
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
public class Person extends Handy\Handy\Model {
	
	const TABLE_NAME = 'people';
	
}

```





### Item Creation

```php
Handy::create('Person',array(
	'first_name' => 'John',
	'last_name' => 'Smith'
);
```

### Item Lookup by ID

Handy::getByID( *Model Name* , *ID* )

```php
$person = Handy::getByID('Person',12);

// Returns single Person object or false
if (!$person) {
	echo "Person 12 was not found!";
} else {
	echo "Person 12's first name is ".$person->get('first_name');
}

```


### Item Lookup by WHERE

Handy::get( *Model Name* , *WHERE clause* )

```php
$person = Handy::get('Person',"`first_name`='Bob'");

// Returns single Person object or false
```

### Multiple Item Lookup by WHERE

Handy::getEach( *Model Name* , *WHERE clause* )

```php
$people = Handy::getEach('Person',"age > 30");

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

### __postCreate Magic Method()


## Goals and the Future

* Migrate from MySQLI to PDO
* Consider expanding functionality to include other database types other than MySQL


## Changelog

1.0.0 - Initial Release
