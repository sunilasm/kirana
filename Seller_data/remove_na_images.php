<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('memory_limit', '5G');
error_reporting(E_ALL);

$TableName='mglof_marketplace_seller';
	try{
		$connection = mysqli_connect('localhost', 'kirana', 'Kirana@aws123', 'kirana_qa_new');
		if(!$connection) {
			throw new Exception('Could not connect to database!');
		}
		$result =array();
		
		//echo "<pre>";
		//print_r($result);exit;
		$deleteQuery = 'DELETE FROM '. $TableName .' WHERE image = "NA" OR image = " "';
		// print_r($deleteQuery);exit;
		mysqli_query($connection, $deleteQuery);

	}catch(Exception $e){
		echo $e->getMessage();
		exit(1);
	}
?>