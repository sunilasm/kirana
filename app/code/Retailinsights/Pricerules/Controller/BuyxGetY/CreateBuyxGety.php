<?php

namespace Retailinsights\Pricerules\Controller\BuyxGetY;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Retailinsights\Pricerules\Model\QuotesFactory;
use Magento\Framework\View\Result\PageFactory;
 
class CreateBuyxGety extends \Magento\Framework\App\Action\Action
{
    protected $_resultPageFactory;
    protected $_session;
    protected $_qoutesFactory;
    protected $_jsonFactory;
    protected $_quotesSellerFactory;
    protected $_productCollection;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Retailinsights\Pricerules\Model\QuotesSellerFactory $quotesSellerFactory
    ) {
        $this->_jsonFactory = $jsonFactory;
        $this->_quotesSellerFactory = $quotesSellerFactory;
        $this->_productCollection = $productCollectionFactory;
        return parent::__construct($context);
    }

    public function IsNullOrEmptyString($product_search_key){
        return (!isset($product_search_key) || trim($product_search_key)==='');
    }

    /**
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if ($this->getRequest()->isAjax())
        {    
            $product_search_key = $this->getRequest()->getParam('product_search_key');     

            if($this->IsNullOrEmptyString($product_search_key))
            {
                $response = $this->_jsonFactory->create()->setData([
                     'success'  => false,
                     'count'   => 0,
                     'products' => []       
                ]);
            }
            else
            {
                $productcollection = $this->_productCollection->create()
                                    ->addAttributeToSelect(['name'])
                                    ->addAttributeToFilter('name', array('like' => '%'.$product_search_key.'%'));

                $products_array = [];       

                foreach ($productcollection as $productInfo) {      
                    $data=[];
                    $data['id'] = $productInfo->getId();
                    $data['sku'] = $productInfo->getSku();
                    $data['name'] = $productInfo->getName();

                    array_push($products_array, $data);
                    }

                $response = $this->_jsonFactory->create()->setData([
                     'success'  => true,
                     'count'   => count($products_array),
                     'products' => $products_array
                ]);
            }
            return $response;
        }
    }
}
