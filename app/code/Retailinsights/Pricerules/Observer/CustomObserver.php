<?php

namespace Retailinsights\Pricerules\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;

class CustomObserver implements ObserverInterface
{    
    protected $_product;
    protected $_cart;
    protected $formKey;

    public function __construct(
        \Magento\Catalog\Model\ProductFactory $product,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Magento\Checkout\Model\Cart $cart
    ){
        $this->_product = $product;
        $this->formKey = $formKey;
        $this->_cart = $cart;
    }
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $items = $this->_cart->getQuote()->getAllVisibleItems();
        $isFreeItem = 0;
        $isXItem = 0;
        foreach($items as $item) {
            if($item->getProductId()=="2"){
                $isXItem = 1;
            }
            if($item->getProductId()=="2"){
                $isFreeItem = 1;
            }
        }

        if(!$isFreeItem && $isXItem) {
            $params = array(
                'form_key' => $this->formKey->getFormKey(),
                'product_id' => 2, //product Id
                'qty'   =>1 //quantity of product                
            );
            $_product = $this->_product->create()->load(2);       
            $this->_cart->addProduct($_product, $params);
            $this->_cart->save();
        }
        if(!$isXItem) {
            /* Remove logic here */
        }
    }
}
