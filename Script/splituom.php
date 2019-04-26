<html>
<body>
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('memory_limit', '5G');
error_reporting(E_ALL);
//$TableName = $resource->getTableName('grocery_pharma_review_sheet'); 
$TableName = 'grocery_pharma_review_sheet';
$TableName1 = 'TABLE_388';
$TableAttributeOptionName = 'mgeav_attribute_option_value';
$token = '';
$finalresult = '';
$limit = (isset($_GET['limit'])) ? $_GET['limit'] : 1;
$page = (isset($_GET['page'])) ? (($_GET['page'] - 1) * $limit) : 1;

 
	try{
		$connection = mysqli_connect('localhost', 'kirana', 'Kirana@aws123', 'kirana_qa_new');
		if(!$connection) {
			throw new Exception('Could not connect to database!');
		}
		//print_r($connection);exit;
		
		// $query = "SELECT * FROM ".$TableName." WHERE `size` LIKE '%kg%' AND `qa_data_uploaded` = 0 ORDER by ID ASC LIMIT ".$page.",".$limit;
		// print_r($query);exit;
		$query = "SELECT * FROM ".$TableName1." ORDER by COL1 ASC LIMIT ".$page.",".$limit;
		//print_r($query);exit;
		$result = $connection->query($query);
		$product  = array();
		$output = '';
		$output .= "<table id='size-table'>";
		$output .= "<tr>";
		$output .= "<th>ID</th>";
		$output .= "<th>Org Size</th>";
		$output .= "<th>Size</th>";
		$output .= "<th>Value</th>";
		$output .= "<th>UOM</th>";
		$output .= "</tr>";

		if(count($result))
		{
            while ($row = $result->fetch_assoc()) {
				//echo $row['size'];
				//$uom_type = $uom_value = '';
				//$uom_type = preg_replace('/[^a-zA-Z]/', '', $row['uom']);
				//$uom_value = preg_replace('/[^0-9]/', '', $row['size']);
				//$re = '/^.*?([\d]+(?:\.[\d]+)?).*?$/';
				//$uom_value = (float) filter_var( $row['size'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
				//echo "<pre>".print_r($uom_value, true); exit;
				$query2 = "UPDATE ".$TableName." SET `size1`= '".$row['COL10']."',`volume`= '".$row['COL11']."', `uom_type`= '".$row['COL12']."' WHERE `article` = '".$row['COL1']."'";
				$result2 = $connection->query($query2);
			   	$output .= "<tr>";
				$output .= "<td>".$row['COL1']."</td>";
				$output .= "<td>".$row['COL9']."</td>";
				$output .= "<td>".$row['COL10']."</td>";
				$output .= "<td>".$row['COL11']."</td>";
				$output .= "<td>".$row['COL12']."</td>";
				$output .= "</tr>";
			}
			$output .= "</table>";
			echo $output;
		}
	}catch(Exception $e){
		echo $e->getMessage();
		exit(10);
	}
?>
<style>
#size-table, tr, th, td{
	border: 1px solid #000;
	border-collapse: collapse;
}

</style>
</body>
</html>