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
		$importFolder ='kirana_details1.csv';
		$row = 1;
		$result =array();
		if(($handle = fopen($importFolder, "r")) !== FALSE) {
			while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
				$num = count($data);
				$result[]=$data;
				//echo "<p> $num fields in line $row: <br /></p>\n";
				$row++;
			}
			fclose($handle);
		}
		//echo "<pre>";
		//print_r($result);exit;
		if(isset($result) && !empty($result)){
			
			foreach($result as $key=>$parseResponse){
				if($key==0){
					continue;
				}
				//print_r($parseResponse);exit;
				$storeid	 		= isset($parseResponse[0]) ? $parseResponse[0] : '';
				$picturepath  		= isset($parseResponse[1]) ? $parseResponse[1] : '';
				$newpicturepath		= isset($parseResponse[2]) ? $parseResponse[2] : '';

				$selectquery = 'SELECT * FROM '.$TableName.' WHERE position='.$storeid.'';
				$selectData = mysqli_query($connection, $selectquery);

				while($result = mysqli_fetch_array($selectData,MYSQLI_ASSOC))
				{
					$seller_id = $result['seller_id'];
					
					if(!empty($seller_id)){
						$Updatequery='UPDATE '. $TableName .' SET image="'.$newpicturepath.'",thumbnail="'.$newpicturepath.'" WHERE seller_id='.$seller_id.'';
						//print_r($Updatequery);exit;
						mysqli_query($connection, $Updatequery);
					}
				}
			}
		}
	}catch(Exception $e){
		echo $e->getMessage();
		exit(1);
	}
?>
