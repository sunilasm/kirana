<?php
namespace Asm\Customapi\Model;
use Asm\Customapi\Api\CityInterface;
 
class Cityview implements CityInterface
{
    /**
     * Returns greeting message to user
     *
     * @api
     * @param string $name Users name.
     * @return string Greeting message with users name.
     */
    protected $request;

    public function __construct(
       \Magento\Framework\App\RequestInterface $request
    ) {
       $this->request = $request;
    }

    public function name() {
        //print_r("Api execute successfully");exit;
        $data = array();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();
        $tableName = $resource->getTableName('mglocation');
        $sql = "Select DISTINCT(locality) FROM " . $tableName;
        $result = $connection->fetchAll($sql);
        foreach ($result as $key => $value) {
            $data[$key] = $value['locality'];
        }
        // echo "<PRE>";
        //print_r($data);exit;


        // if($searchtermpara){ $searchterm = 0; }else{ $searchterm = 1; }
        // if($searchterm){
        //     if($title){
        //         $productCollectionArray = $this->getSearchTermData($title, $lat, $lon);
        //          if($productCollectionArray){
        //             $data = $productCollectionArray;
        //         }else{
        //             $data = $productCollectionArray;
        //         }
        //     }else{
        //          $data = array('message' => 'Please specify at least one search term');
        //     }
        // }else{
        //     $productCollectionArray = $this->getSearchTermData($title = null,$lat, $lon);
        //      if($productCollectionArray){
        //         $data = $productCollectionArray;
        //     }else{
        //         $data = $productCollectionArray;
        //     }
        // }
        return $data;
    }
   
}
