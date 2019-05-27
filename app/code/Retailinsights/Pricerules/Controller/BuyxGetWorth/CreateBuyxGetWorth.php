<?php
namespace Retailinsights\Pricerules\Controller\BuyxGetWorth;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Retailinsights\Pricerules\Model\QuotesFactory;
use Magento\Framework\View\Result\PageFactory;
 
class CreateBuyxGetWorth extends \Magento\Framework\App\Action\Action
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
 
    // Function for basic field validation (present and neither empty nor only white space
    public function IsNullOrEmptyString($product_search_key){
        return (!isset($product_search_key) || trim($product_search_key)==='');
    }

    // public function getUom($product)
    // {
    //     # code...
    //     $uom = $product->getResource()->getAttributeRawValue($product->getId(),'mandee_selling_type',1);
    //     return $uom;
    // }

    /**
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
       
       
        // $productcollection = $this->_productCollection->create()
        // ->addAttributeToSelect(['name'])
        // ->addAttributeToFilter('name', array('like' => '%item%'));

        // $needle = 'item';

        // $productcollection = $this->_productCollection->addAttributeToFilter('name', array(
        //     array('like' => '% '.$needle.' %'), //spaces on each side
        //     array('like' => '% '.$needle), //space before and ends with $needle
        //     array('like' => $needle.' %') // starts with needle and space after
        // ));

        // print_r($productcollection->getData());

        // $products_array = [];       

        // foreach ($productcollection as $productInfo) {
           
        //     $data=[];
        //     $data['id'] = $productInfo->getId();
        //     $data['sku'] = $productInfo->getSku();
        //     $data['name'] = $productInfo->getName();

        //     array_push($products_array, $data);

        // }
        // print('<pre>');
        // print_r($products_array);
        // print('</pre>');


        // foreach ($products_array as $product) {
        //     # code...
        //     print('<br>');
        //     print('id =>'.$product['id']);
        //     print('<br>');
        // }

        if ($this->getRequest()->isAjax())
        {    

            $product_search_key = $this->getRequest()->getParam('product_search_key');     

            if($this->IsNullOrEmptyString($product_search_key))
            {
                // print('true');

                $response = $this->_jsonFactory->create()->setData([
                     'success'  => false,
                     'count'   => 0,
                     'products' => []
                    
                ]);
            }
            else
            {
                // print('false');

                $productcollection = $this->_productCollection->create()
                                    ->addAttributeToSelect(['name'])
                                    ->addAttributeToFilter('name', array('like' => '%'.$product_search_key.'%'));

                $products_array = [];       

                foreach ($productcollection as $productInfo) {
                   
                    $data=[];
                    $data['id'] = $productInfo->getId();
                    $data['sku'] = $productInfo->getSku();
                    $data['name'] = $productInfo->getName();
                    // $data['uom'] = $this->getUom($productInfo);

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