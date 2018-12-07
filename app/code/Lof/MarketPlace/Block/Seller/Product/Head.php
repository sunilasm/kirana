<?php

namespace Lof\MarketPlace\Block\Seller\Product;

class Head extends \Magento\Framework\View\Element\Template
{
	
    protected $request;

    /**
     * @param \Magento\Framework\View\Element\Template\Context
     * @param \Magento\Framework\Registry
     * @param \Lof\MarketPlace\Model\Seller
     * @param \Magento\Framework\App\ResourceConnection
     * @param array
    */
	public function __construct(
    	\Magento\Framework\View\Element\Template\Context $context,
        array $data = []
        ) {
        parent::__construct($context);

        $this->request = $context->getRequest();

        
    }
   

    public function getTypeProduct() {
        $path = trim($this->request->getPathInfo(), '/');
        $params = explode('/', $path); 
        if(isset($params[9])) {
            $type = $params[9];
        } else {
             $type = end($params);
        }
        return $type;
    }
     
}