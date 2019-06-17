<?php
namespace Retailinsights\Promotion\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use Retailinsights\Promotion\Model\PostTableFactory;
use Retailinsights\Promotion\Model\PromoTableFactory;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address;
use Magento\Quote\Api\Data\TotalsInterfaceFactory;

class ModifyCart implements ObserverInterface
{
    protected $_productRepository;
    protected $_cart;
    protected $quoteRepository;
    protected $_promoFactory;
    protected $_quoteAddressFactory;
    protected $_quot;
    protected $_quotAddress;
    protected $_totalFactory;

  public function __construct(
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Checkout\Model\Cart $cart,
        PostTableFactory $PostTableFactory ,
        PromoTableFactory $promoFactory,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Quote\Model\Quote\AddressFactory $quoteAddressFactory,
        Quote $quot,
        Address $quotAddress,
        TotalsInterfaceFactory $totalFactory
      )
  {
      $this->_productRepository = $productRepository;
      $this->_cart = $cart;
      $this->_PostTableFactory = $PostTableFactory;
      $this->_promoFactory = $promoFactory;
      $this->quoteRepository = $quoteRepository;
      $this->_quoteAddressFactory = $quoteAddressFactory;
      $this->_quot = $quot;
      $this->_quotAddress = $quotAddress;
      $this->_totalFactory = $totalFactory;
  }
  public function execute(\Magento\Framework\Event\Observer $observer)
  {  
      $quote = $observer->getData('quote');
      $quoteId = $quote->getId();

      $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/appromo1.log'); 
      $logger = new \Zend\Log\Logger();
      $logger->addWriter($writer);
      //$logger->info('==============New Observer');

      $promoData = $this->_promoFactory->create()->getCollection()
      ->addFieldToFilter('cart_id', $quoteId);
      $total_disc = 0;
      foreach($promoData->getData() as $k => $val){
        $total_disc = $val['total_discount'];
      }
      if (isset($promoData->getData()[0]['total_discount'])) {
        
        $total_disc = $promoData->getData()[0]['total_discount'];
      }
      $logger->info(print_r($promoData->getData(),1));
      
      
      $subTotal = $quote->getBaseSubtotal();
      $newSubTotal = ($subTotal - $total_disc);
      

      if($total_disc  > 0){

        $usr_quot = $this->_quot->load($quoteId);
        //$ttoalfactory = $this->_totalFactory->create()->load($quoteId);
        $quotaddr = $this->_quotAddress->setQuote($usr_quot)->getQuote();
        $logger->info(print_r($usr_quot->getData(),1));
        $logger->info(print_r($quotaddr->getData(),1));
        $logger->info(print_r($quotaddr->getBaseGrandTotal(),1));
        //$logger->info(print_r($quotaddr->getTotals(),1));
        /*$quotaddr->setBaseSubtotal($subTotal);
        $quotaddr->setSubtotal($subTotal);
        $quotaddr->setDiscountAmount($total_disc);
        $quotaddr->setBaseDiscountAmount($total_disc);
        $quotaddr->setSubtotalWithDiscount($newSubTotal);
        $quotaddr->setBaseSubtotalWithDiscount($newSubTotal);
        $quotaddr->setGrandTotal($newSubTotal);
        $quotaddr->setBaseGrandTotal($newSubTotal);
        $quotaddr->save();*/
        //$logger->info(print_r($ttoalfactory->getData(),1));
        

        $total_disc = '-'.$total_disc;
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); 
        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();
        $sqlOrder = "Update mgsales_order Set grand_total=".$newSubTotal.",total_due=".$newSubTotal.",base_total_due=".$newSubTotal.", base_grand_total=".$newSubTotal.",	discount_amount=".$total_disc.", base_discount_amount =".$total_disc." where quote_id =".$quoteId ;
        //$logger->info($sqlOrder);
       // $connection->query($sqlOrder);

        $sqlQuoteAdd = "Update mgquote_address Set subtotal=".$newSubTotal.", base_subtotal=".$newSubTotal.", subtotal_with_discount =".$subTotal.", base_subtotal_with_discount=".$subTotal.",  grand_total=".$newSubTotal.",  base_grand_total=".$newSubTotal.",	discount_amount=".$total_disc.", base_discount_amount =".$total_disc." where quote_id =".$quoteId ;
        //$logger->info($sqlQuoteAdd);
        //$connection->query($sqlQuoteAdd);

        $sqlQuote = "Update mgquote Set subtotal=".$newSubTotal.", base_subtotal =".$newSubTotal.", subtotal_with_discount =".$subTotal.", base_subtotal_with_discount=".$subTotal.", grand_total=".$newSubTotal.", base_grand_total=".$newSubTotal." where entity_id = ".$quoteId ;
        //$logger->info($sqlQuote);
        //$connection->query($sqlQuote);
      }        
  }
}