#Handy

Handy is a utility for managing MySQL models, etc.

## Quick Start

**person.class.php**

```php

public class Person extends HandyModel {
	const TABLE_NAME = 'people';
}
```

**example.php**

```php

# ––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––
#	Setup
# ––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––––

	// Setup database handle
	$dbh = new mysqli( $host, $user, $pass, $database );

	// Pass Handy the database handle	
	Handy::setDefaultDB($dbh);
	
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

## Comparison to traditional syntax

### Add new person and fetch them back as a Person (class) object

**Traditional:**

```php
$db->query("INSERT INTO `people` SET `x`='y', `a`='b'");

$newPersonID = $db->insert_id;
$newPerson = $db->query("SELECT * FROM `people` WHERE `id` = '{$newPersonID}'");

$newPerson = $newPerson->fetch_object('Person');
```

**With Handy:**

```php
$newPerson = Person::create(array(
	'x' => 'y',
	'a' => 'b'
));
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

ModelName::lookup( *WHERE clause* (optional) )

```php
$person = Person::lookup("`first_name`='Bob'");

// Returns single Person object or false
```

### Multiple Item Lookup by WHERE

ModelName::lookupEach( *WHERE clause* (optional) )

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

### Item Lookup Random by WHERE

ModelName::lookupRandom( *WHERE clause (optional)* )

```php
$person = Person::lookupRandom("`age` > 30");

// Returns single (random) Person object or false
```


## Advanded Usage

### __extensionConstruct()
### __postCreate()


## Goals and the Future

* Possibly migrate from MySQLI to PDO
* Consider expanding functionality to include other database types other than MySQL
* Add option to select only certain fields or JOIN
* Show recommended syntax for adding custom lookup methods or overriding


## Changelog

**1.2.1** – Added support for using alternate unique id name. Should be set in HandyModel class extension with ```protected $uidName = 'alt_uid'```, (defaults to ```id```).

**1.2.0** – Added support for multiple data sources. Limit 1 per model class. ```Handy::setDefaultDB($dbh)``` or ```Handy::setModelDB('ModelName',$dbh)```

**1.1.4** — Fixes for new static methods access

**1.1.3** — Changed database handle setup to ```Handy::setDB($databaseHandlerVariable);```

**1.1.2** — README udpates

**1.1.1** — Fixes and updated README

**1.1.0** — Simplification of calls. Static methods moved to main model class ```Handy::getByID('Person',12)``` is now ```Person::getByID(12)```

**1.0.1** — Fixes, etc

**1.0.0** — Initial Release
