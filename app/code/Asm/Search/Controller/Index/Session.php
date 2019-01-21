<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Asm\Search\Controller\Index;
use Magento\Framework\App\Action\Context;

class Session extends \Magento\Framework\App\Action\Action
{
    protected $customerSession;
    protected $request;
    /**
     * Construct
     *
     * @param Context $context
     * @param ModelAdvanced $catalogSearchAdvanced
     * @param UrlFactory $urlFactory
     */
    public function __construct(
        Context $context,
        // \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Session\SessionManagerInterface $session,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->session = $session;
        $this->request = $request;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $parameters = $this->request->getParams();
        $latitude = $parameters['latitude'];
        $longitude = $parameters['longitude'];
        $custmerloginstatus = $parameters['custmerloginstatus'];
        $fulladdress = $parameters['fulladdress'];
        // Unset session value
        // $this->customerSession->unsLatitude();
        // $this->customerSession->unsLongitude();
        // $this->customerSession->unsCustmerloginstatus();
        // Set session value
        $this->session->setLatitude($latitude);
        $this->session->setLongitude($longitude);
        $this->session->setCustmerloginstatus($custmerloginstatus);
        $this->session->setFulladdress($fulladdress);
        // print_r($this->session->getLatitude());exit;
        $data = array('status' => 1,'message' => 'Success',);
        echo json_encode($data);die;
    }
}
