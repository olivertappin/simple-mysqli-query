# Simple MySQLi Query

An extremely simple MySQLi query class for prepared statements.

## Get started

To initialize a new instance of the `Query` class, simply pass in your `mysqli` object:

```php
$mysqli = new mysqli('127.0.0.1', 'my_user', 'my_password', 'my_db');

if ($mysqli->connect_error) {
    die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}

$query = new Query($mysqli);
```

For more information about creating a new connection, see: https://www.php.net/manual/en/mysqli.construct.php

## Usage

This class is made up of the following methods:

- `select()` - Simple select statements
- `insert()` - Simple insert statements
- `update()` - Simple update statements
- `delete()` - Simple delete statements
- `query()` - Simple queries, accepting `bind_param` parameter types

The first 4 methods automatically convert their parameter values to their corresponding data types for the `bind_param` method.

The supported types for this class are converted by using the `gettype()` value of the variable, into the `bind_param` type using the table below: 

| Type    | Character | Description                             |
|---------|-----------|-----------------------------------------|
| string  | s         | corresponding variable has type string  |
| float   | s         | corresponding variable has type string  |
| integer | i         | corresponding variable has type integer |
| bool    | d         | corresponding variable has type double  |

For more information about `bind_param` types, see: https://www.php.net/manual/en/mysqli-stmt.bind-param.php#refsect1-mysqli-stmt.bind-param-parameters 

Out of the <abbr title="Create, Read, Update, Delete">CRUD</abbr> methods, the `select()` method is the only method that returns an array - the data to be returned. The rest simply return a boolean based on whether the query was able to successfully insert, update, or delete.

## Query examples

### Select

A simple `SELECT` statement, with no parameters:

```php
$data = $query->select('SELECT * FROM `my_table`');

return $data;
```

A simple `SELECT` statement, with parameters:

```php
$params = [
    123,
    '2020-08-22',
];

$data = $query->select('SELECT * FROM `my_table` WHERE `status_id` = ? AND `created_at` > ? ', $params);

return $data;
```

### Insert

A simple `INSERT` statement:

```php
$params = [
    'Thumper',
    'Sabrina',
    'Mini Lop',
    'M',
    '2020-08-22',
];

$return = $query->insert('INSERT INTO `my_table` VALUES (?, ?, ?, ?, ?)');

return $return;
```

### Update

A simple `UPDATE` statement:

```php
$params = [
    'Oliver',
    'Thumper',
];

$return = $query->insert('UPDATE `pets` SET `owner` = ? WHERE `name` = ?');


return $return;
```

### Delete

A simple `DELETE` statement:

```php
$params = [
    'Thumper',
];

$return = $query->insert('DELETE FROM `pets` WHERE `name` = ?', $params);

return $return;
```

### Advanced usage

For more advanced usages, you can use the `query()` method directly:

```php
$types = 'ss';

$params = [
    '2020-01-01',
    '2020-12-31',
];

$data = $query->query('SELECT * FROM `pets` WHERE `death` BETWEEN ? AND ?', $types, $params);

return $data;
```

You may want to use this method to gain more control over the `bind_param` types, without having this class attempt to convert them for you.

### Table structure

The above examples are given as an example. If you would like to use this exact table structure to test these examples directly, you can create this yourself by using the following `CREATE TABLE` statement:

```mysql
CREATE TABLE `pets` (
  `name` VARCHAR(20),
  `owner` VARCHAR(20),
  `species` VARCHAR(20),
  `sex` CHAR(1),
  `birth` DATE,
  `death` DATE
);
```

Which would look something like this, using a `DESCRIBE` statement:

```text
+---------+-------------+------+-----+---------+-------+
| Field   | Type        | Null | Key | Default | Extra |
+---------+-------------+------+-----+---------+-------+
| name    | varchar(20) | YES  |     | NULL    |       |
| owner   | varchar(20) | YES  |     | NULL    |       |
| species | varchar(20) | YES  |     | NULL    |       |
| sex     | char(1)     | YES  |     | NULL    |       |
| birth   | date        | YES  |     | NULL    |       |
| death   | date        | YES  |     | NULL    |       |
+---------+-------------+------+-----+---------+-------+
```
