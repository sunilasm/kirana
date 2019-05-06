<?php
namespace Asm\Timeslot\Model;
use Asm\Timeslot\Api\TimeslotInterface;
 
class Timeslotview implements TimeslotInterface
{
    /**
     * Returns greeting message to user
     *
     * @api
     * @param string $name Users name.
     * @return string Greeting message with users name.
     */
    protected $request;
    protected $orderRepository;

    public function __construct(
       \Magento\Framework\App\RequestInterface $request,
       \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
    ) {
       $this->request = $request;
       $this->orderRepository = $orderRepository;
    }

    public function timeslot() {
        // print_r("Execute successfully");exit;
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $request = $objectManager->get('\Magento\Framework\Webapi\Rest\Request');
        $post = $request->getBodyParams();
        $order = $this->orderRepository->get($post['order_id']);
        $orderIncrementId = $order->getIncrementId();

        $resource = $objectManager->get('\Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();
        $tableName = $resource->getTableName('order_time_slot');
        foreach($post['slots'] as $slot):
            if($slot['time_slot_type']){
                $sql = "INSERT INTO " . $tableName . "(order_id, order_increment_id, store_id, time_slot_type, date_slot, time_slot) VALUES('" . $post['order_id'] . "','" . $orderIncrementId . "','" . $slot['store_id'] . "','" . $slot['time_slot_type'] . "','" . $slot['date_slot'] . "','" . $slot['time_slot'] . "')";
                // print_r($sql);exit;

            }else{
                $sql = "INSERT INTO " . $tableName . "(order_id, order_increment_id, store_id, time_slot_type, date_slot, time_slot) VALUES('" . $post['order_id'] . "','" . $orderIncrementId . "','" . $slot['store_id'] . "','" . $slot['time_slot_type'] . "','NULL','NULL')";
            }
          $connection->query($sql);
        endforeach;
    }
}
