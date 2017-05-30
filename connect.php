<?php

// script to create sqlite db 

// file of csv to read in
$file = "/home/valdeslab/Documents/php_scripts/CindysListSupplementals/all_out.csv";

echo "Doing the work\n";

// connect to database
// open database if one exists create it if it doesn't
$db = new Connect();
if (!$db) {
	echo $db -> lastErrorMsg();
} else {
	echo "Opened database successfully\n";
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
$handle = fopen($file , "r");

// go over each row
while (($data = fgetcsv($handle, 1000, ",")) != FALSE) {

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
fclose($handle);
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
