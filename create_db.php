<?php

// script to create sqlite db 

// file of csv to read in
$all_out = "/home/valdeslab/Documents/php_scripts/CindysListSupplementals/all_out.csv";
$recipes = "/home/valdeslab/Documents/php_scripts/CindysListSupplementals/recipes.csv";

// connect to database
// open database if one exists create it if it doesn't
$db = new Connect();
if (!$db) {
	echo $db -> lastErrorMsg();
} else {
	echo "Opened database successfully\n";
}

// Drop table statements
$drop_tables = array (
	"drop table products;",
	"drop table created_lists;",
	"drop table lists;",
	"drop table recipes;",
	"drop table android_metadata;"
); 

// Drop each table
foreach ($drop_tables as $query) {

	$ret = $db -> exec($query);
	if (!$ret) {
		echo $db -> lastErrorMsg();
	} else {
		echo $query. " successful\n";
	}

}


// create tables
// use heredoc (<<<) for structured queries
$products_table =<<< EOF
CREATE TABLE products
(_id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
category TEXT,
product TEXT,
price REAL,
pic_id BLOB,
upc TEXT,
selections INTEGER);
EOF;

// Create the table
$ret = $db -> exec($products_table);
if (!$ret) {
	echo $db -> lastErrorMsg();
} else {
	echo "product table created\n";
}

// repeat for lists and created_lists
$created_lists_table =<<< EOF
CREATE TABLE created_lists
(_id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
list_name TEXT,
date TEXT,
items INTEGER,
cost REAL);
EOF;

$ret = $db -> exec($created_lists_table);
if (!$ret) {
	echo $db -> lastErrorMsg();
} else {
	echo "created_lists table created\n";
}

$list_table =<<<EOF
CREATE TABLE lists
(_id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
list_name TEXT,
product TEXT,
qty INTEGER);
EOF;

$ret = $db -> exec($list_table);
if (!$ret) {
	echo $db -> lastErrorMsg();
} else {
	echo "lists table created\n";
}

$recipes_table =<<<EOF
CREATE TABLE recipes
(_id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
recipe_name TEXT,
product TEXT,
measurement REAL,
unit TEXT);
EOF;

$ret = $db -> exec($recipes_table);
if (!$ret) {
	echo $db -> lastErrorMsg();
} else {
	echo "recipes table created\n";
}

// Create metadata table
$metadata_table =<<<EOF
CREATE TABLE android_metadata (locale TEXT DEFAULT 'en_US');
EOF;

$ret = $db -> exec($metadata_table);
if (!$ret) {
	echo $db -> lastErrorMsg();
} else {
	echo "metadata table created\n";
}

// Insert metadata
$metadata =<<<EOF
INSERT INTO "android_metadata" VALUES ('en_US');
EOF;
$ret = $db -> exec($metadata);
if (!$ret) {
	echo $db -> lastErrorMsg();
} else {
	echo "metadata inserted\n";
}


// Insert product data

// open file to read
$file = fopen($all_out , "r");

// go over each row
while (($data = fgetcsv($file, 1000, ",")) != FALSE) {

// place data in sql statement to be inserted
$insert_statement =<<<EOF
INSERT INTO products (category, product, price, pic_id, upc, selections) VALUES ('$data[0]', '$data[1]', $data[2], $data[3], '$data[4]', $data[5]);
EOF;
	// execute insert
	$ret = $db -> exec($insert_statement);
	// check if insertion successful
	if (!$ret) {
		echo $db -> lastErrorMsg() . "\n";
	} else {
		echo "$insert_statement\nAdded Successfully\n";
	}
}

// close file
fclose($file);

// open file to read
$file = fopen($recipes , "r");

// go over each row
while (($data = fgetcsv($file, 1000, ",")) != FALSE) {

// place data in sql statement to be inserted
$insert_statement =<<<EOF
INSERT INTO recipes (recipe_name, product, measurement, unit) VALUES ('$data[0]', '$data[1]', $data[2], '$data[3]');
EOF;
	// execute insert
	$ret = $db -> exec($insert_statement);
	// check if insertion successful
	if (!$ret) {
		echo $db -> lastErrorMsg() . "\n";
	} else {
		echo "$insert_statement\nAdded Successfully\n";
	}
}

// close file
fclose($file);

// close db
$db -> close();


###########################################################
###		Class to connect to sqlite3		###
###########################################################

class Connect extends SQLite3 {

      function __construct() {

         $this -> open('cindys_list.db');
      }

}

?>
