<?php
echo "<pre>";
error_reporting( E_ALL );
ini_set('display_errors', 1);

$servername="localhost";
$username="root";
$password="mysql";
$dbname="kemal_poyraz";

function DebugLog($msg)
{
	echo $msg.'<br>';
}

DebugLog('Connecting to DB');
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error)
{
    die("Connection failed: " . $conn->connect_error);
}

DebugLog("Dropping tables in kemal_poyraz DB if they already exist.");


$tables = array('DISTRICT', 'CITY', 'MARKET', 'MARKETS_CITY', 'SALESMAN', 'CUSTOMER', 'PRODUCT', 'SALE');
$sql = '';
foreach ($tables as $table)
{
	$sql = "DROP TABLE IF EXISTS $table;";
	$conn->query($sql);
}		

DebugLog('Creating DB Tables');

$sql = "CREATE TABLE DISTRICT
(district_id INT NOT NULL,
district_name VARCHAR(50),
PRIMARY KEY (district_id)) ENGINE=INNODB;";

$conn->query($sql);

$sql = "CREATE TABLE CITY
(city_id INT NOT NULL AUTO_INCREMENT,
city_name VARCHAR(50),
district_id INT,
PRIMARY KEY (city_id),
FOREIGN KEY (district_id) REFERENCES DISTRICT(district_id)) ENGINE=INNODB;";

$conn->query($sql);

$sql = "CREATE TABLE MARKET
(market_id INT NOT NULL AUTO_INCREMENT,
market_name VARCHAR(50),
PRIMARY KEY (market_id)) ENGINE=INNODB;";

$conn->query($sql);

$sql = "CREATE TABLE MARKETS_CITY 
(shop_id INT NOT NULL AUTO_INCREMENT,
city_id INT NOT NULL,
market_id INT NOT NULL,
PRIMARY KEY (shop_id)) ENGINE=INNODB;";

$conn->query($sql);

$sql = "CREATE TABLE SALESMAN
(salesman_id INT NOT NULL AUTO_INCREMENT,
salesman_name VARCHAR(50),
salesman_surname VARCHAR(50),
market_id INT, 
PRIMARY KEY (salesman_id),
FOREIGN KEY (market_id) REFERENCES MARKET(market_id)) ENGINE=INNODB;";

$conn->query($sql);

$sql = "CREATE TABLE CUSTOMER
(customer_id INT NOT NULL AUTO_INCREMENT,
customer_name VARCHAR(50),
customer_surname VARCHAR(50),
PRIMARY KEY (customer_id)) ENGINE=INNODB;";

$conn->query($sql);

$sql = "CREATE TABLE PRODUCT
(product_id INT NOT NULL AUTO_INCREMENT,
product_name VARCHAR(50),
price FLOAT(10,2),
PRIMARY KEY (product_id)) ENGINE=INNODB;";

$conn->query($sql);

$sql = "CREATE TABLE SALE
(sale_id INT NOT NULL AUTO_INCREMENT,
product_id INT NOT NULL,
customer_id INT NOT NULL,
salesman_id INT NOT NULL,
sale_date Date,
PRIMARY KEY (sale_id)) ENGINE=INNODB;";

$conn->query($sql);

DebugLog('DB Created');

define('CSV_DIR', dirname(__FILE__).'/');

$districts = CSV_DIR."Districts.csv";

if (($handle = fopen($districts, "r")) !== FALSE)
{
   fgetcsv($handle);   
   while (($data = fgetcsv($handle, 1000, ";")) !== FALSE)
   {
        $num = count($data);
        for ($i=0; $i < $num; $i++)
		{
          $col[$i] = $data[$i];
        }
		$col1 = $col[0];
		$col2 = $col[1];
		
		$sql = "INSERT INTO DISTRICT(district_id,district_name) VALUES('".$col1."','".$col2."')";
		
		$conn->query($sql);
	}
    fclose($handle);
}

$cities = CSV_DIR."Cities.csv";

if (($handle = fopen($cities, "r")) !== FALSE)
{
   fgetcsv($handle);   
   while (($data = fgetcsv($handle, 1000, ";")) !== FALSE)
   {
        $num = count($data);
        for ($i=0; $i < $num; $i++)
		{
          $col[$i] = $data[$i];
        }
		$col1 = $col[0];
		$col2 = $col[1];
	
		$sql = "INSERT INTO CITY(city_name,district_id) VALUES('".$col1."','".$col2."')";
		
		$conn->query($sql);
	}
    fclose($handle);
}

$markets = CSV_DIR."Markets.csv";

if (($handle = fopen($markets, "r")) !== FALSE)
{
   fgetcsv($handle);   
   while (($data = fgetcsv($handle, 1000, ";")) !== FALSE)
   {
        $num = count($data);
        for ($i=0; $i < $num; $i++)
		{
          $col[$i] = $data[$i];
        }
		$col1 = $col[0];
	
		$sql = "INSERT INTO MARKET(market_name) VALUES('".$col1."')";
		
		$conn->query($sql);
	}
    fclose($handle);
}

$sql = "SELECT city_id FROM CITY";
$result = $conn->query($sql);
$market_city = array();
while ($row = $result->fetch_array())
{
	$city_id = intval($row['city_id']);
	if($city_id == 0)
	{
		die('zero found');
	}
	
	$sql = "SELECT market_id FROM MARKET ORDER BY RAND() LIMIT 5";
	$result2 = $conn->query($sql);
	while ($row2 = $result2->fetch_array())
	{

		$market_id = intval($row2['market_id']);
		$valuesArr[] = "($city_id, $market_id)";
		
	}
}

$values_string = implode(',', $valuesArr);

$sql = "INSERT INTO MARKETS_CITY (city_id, market_id) VALUES $values_string";

$conn->query($sql);

unset($valuesArr);

$sql = "CREATE TABLE NAME
(name_id INT NOT NULL AUTO_INCREMENT,
name VARCHAR(50),
PRIMARY KEY (name_id)) ENGINE=INNODB;";

$conn->query($sql);

$sql = "CREATE TABLE SURNAME
(surname_id INT NOT NULL AUTO_INCREMENT,
surname VARCHAR(50),
PRIMARY KEY (surname_id)) ENGINE=INNODB;";

$conn->query($sql);

$names = CSV_DIR."Names.csv";
$surnames = CSV_DIR."Surnames.csv";

if (($handle = fopen($names, "r")) !== FALSE)
{
   fgetcsv($handle);   
   while (($data = fgetcsv($handle, 1000, ";")) !== FALSE)
   {
        $num = count($data);
        for ($i=0; $i < $num; $i++)
		{
          $col[$i] = $data[$i];
        }
		$col1 = $col[0];
	
		$sql = "INSERT INTO NAME(name) VALUES('".$col1."')";
		
		$conn->query($sql);
	}
    fclose($handle);
}

if (($handle = fopen($surnames, "r")) !== FALSE)
{
   fgetcsv($handle);   
   while (($data = fgetcsv($handle, 1000, ";")) !== FALSE)
   {
        $num = count($data);
        for ($i=0; $i < $num; $i++)
		{
          $col[$i] = $data[$i];
        }
		$col1 = $col[0];
	
		$sql = "INSERT INTO SURNAME(surname) VALUES('".$col1."')";
		
		$conn->query($sql);
	}
    fclose($handle);
}

$sql = "INSERT INTO CUSTOMER(customer_name,customer_surname) SELECT name, surname FROM NAME CROSS JOIN SURNAME ORDER BY RAND() LIMIT 1620";

$conn->query($sql);

$sql = "INSERT INTO SALESMAN(salesman_name,salesman_surname) SELECT name, surname FROM NAME CROSS JOIN SURNAME ORDER BY RAND() LIMIT 1215";

$conn->query($sql);

$sql = "DROP TABLE IF EXISTS NAME, SURNAME";

$conn->query($sql);

// assign salesman to shop 

// get shop ids in case not using 0 AUTO_INCREMENT on table
$sql = "SELECT shop_id FROM MARKETS_CITY";
$shop_id_res = $conn->query($sql);

$sql = "SELECT salesman_id FROM SALESMAN";
$salesman_res = $conn->query($sql);

$count = 0;
// get first shop id
$shop_id_array = $shop_id_res->fetch_array();
$shop_id = $shop_id_array['shop_id'];
while ($salesman_row = $salesman_res->fetch_array())
{
	//$three_count++;
	if($count >=3) 
	{
		// get next shop id
		$shop_id_array = $shop_id_res->fetch_array();
		$shop_id = $shop_id_array['shop_id'];
		$count = 0;
	}
	$salesman_id = intval($salesman_row['salesman_id']);
	$sql = "UPDATE SALESMAN set market_id = '$shop_id' where salesman_id = '$salesman_id'";
	$conn->query($sql);
	$count++;

}

$products = CSV_DIR."Products.csv";

if (($handle = fopen($products, "r")) !== FALSE)
{
   fgetcsv($handle);   
   while (($data = fgetcsv($handle, 1000, ";")) !== FALSE)
   {
        $num = count($data);
        for ($i=0; $i < $num; $i++)
		{
          $col[$i] = $data[$i];
        }
		$col1 = $col[0];
		$price = mt_rand (1,200);
	
		$sql = "INSERT INTO PRODUCT(product_name, price) VALUES('".$col1."', '".$price."')";
		
		$conn->query($sql);
	}
    fclose($handle);
}

$conn->close();
print "</pre>";