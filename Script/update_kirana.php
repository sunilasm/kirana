<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('memory_limit', '5G');
error_reporting(E_ALL);

$TableName = 'kirana_data';
$token = '';
$finalresult = '';
$limit = (isset($_GET['limit'])) ? $_GET['limit'] : 1500;
$page = (isset($_GET['page'])) ? (($_GET['page'] - 1) * $limit) : 1;
$i = 1; 
$sucess = 0;
$error = 0;

// Implementation
try
{
	$connection = mysqli_connect('localhost', 'kirana', 'Kirana@aws123', 'kirana_dev');
	if(!$connection) 
	{
		throw new Exception('Could not connect to database!');
	}
	$query = "SELECT * FROM ".$TableName." WHERE `dev_data_updated` = 0 LIMIT ".$page.",".$limit;
	// print_r($query);exit;
	$result = $connection->query($query);
	$product  = array();
	if(count($result))
	{
		/* fetch associative array */
		while ($row = $result->fetch_assoc()) 
		{
			
			if($row['email'])
			{
				$tableSeller = 'mglof_marketplace_seller';
				$query1 = "SELECT * FROM ".$tableSeller." WHERE `email` = '".$row['email']."'";
				$resultSeller = $connection->query($query1);
				if(count($resultSeller)){
					$query2 = "UPDATE ".$tableSeller." SET `address`= '".$row['address']."',`landmark1`= '".$row['landmark']."',`area`= '".$row['area']."',`sector`= '".$row['sector']."' WHERE `email` = '".$row['email']."'";
					 // print_r($query2);
				}
			}
			
			$result2 = $connection->query($query2);
	 		echo  "\n".$row['email']."\n";
		}
	}
	 echo  "\nSucess :\n";
	exit;  
}catch(Exception $e){
	echo $e->getMessage();
	exit(10);
}
	
?>