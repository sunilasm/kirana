<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://www.landofcoder.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Landofcoder
 * @package    Lof_MarketPlace
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\MarketPlace\App\Action;

use Magento\Framework\Controller\ResultFactory;

/**
 * Backend Controller context
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Context extends \Magento\Framework\App\Action\Context
{
    /**
     * @var \Magento\Framework\AuthorizationInterface
     */
    protected $_authorization;

    /**
     * @var \Magento\Backend\Model\Auth
     */
    protected $_auth;

    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $_backendUrl;

    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    protected $_formKeyValidator;

    /**
     * @var bool
     */
    protected $_canUseBaseUrl;

    /**
     * @var \Lof\MarketPlace\Model\Session
     */
    protected $_session;

    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    protected $_localeResolver;

    
    protected $_frontendUrl;
    
    /**
     * @var \Lof\MarketPlace\App\ConfigInterface
     */
    protected $_config;
    
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;
    
    /**
     * Date filter instance
     *
     * @var \Magento\Framework\Stdlib\DateTime\Filter\Date
     */
    protected $_dateFilter;
    
    /**
     * 
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\App\ResponseInterface $response
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\UrlInterface $url
     * @param \Magento\Framework\Url $frontendUrl
     * @param \Magento\Framework\App\Response\RedirectInterface $redirect
     * @param \Magento\Framework\App\ActionFlag $actionFlag
     * @param \Magento\Framework\App\ViewInterface $view
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Lof\MarketPlace\Model\View\Result\RedirectFactory $resultRedirectFactory
     * @param ResultFactory $resultFactory
     * @param \Lof\MarketPlace\Model\Session $session
     * @param \Magento\Framework\AuthorizationInterface $authorization
     * @param \Magento\Backend\Model\Auth $auth
     * @param \Lof\MarketPlace\Helper\Data $helper
     * @param \Lof\MarketPlace\Model\UrlInterface $backendUrl
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     * @param \Lof\MarketPlace\App\ConfigInterface $config
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter
     * @param string $canUseBaseUrl
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\App\ResponseInterface $response,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\UrlInterface $url,
        \Magento\Framework\Url $frontendUrl,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Magento\Framework\App\ActionFlag $actionFlag,
        \Magento\Framework\App\ViewInterface $view,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory,
        ResultFactory $resultFactory,
        \Lof\MarketPlace\Model\Session $session,
        \Magento\Framework\AuthorizationInterface $authorization,
        \Magento\Backend\Model\Auth $auth,
        \Lof\MarketPlace\Helper\Data $helper,
        //\Lof\MarketPlace\Model\UrlInterface $backendUrl,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        //\Lof\MarketPlace\App\ConfigInterface $config,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter,
        $canUseBaseUrl = false
    ) {
        parent::__construct(
            $request,
            $response,
            $objectManager,
            $eventManager,
            $url,
            $redirect,
            $actionFlag,
            $view,
            $messageManager,
            $resultRedirectFactory,
            $resultFactory
        );

        $this->_session = $session;
        $this->_authorization = $authorization;
        $this->_auth = $auth;
        $this->_helper = $helper;
        $this->_backendUrl = $url;
        $this->_formKeyValidator = $formKeyValidator;
        $this->_localeResolver = $localeResolver;
        $this->_canUseBaseUrl = $canUseBaseUrl;
        $this->_frontendUrl = $frontendUrl;
        //$this->_config = $config;
        $this->_coreRegistry = $registry;
        $this->_dateFilter = $dateFilter;
    }

    /**
     * @return \Magento\Backend\Model\Auth
     */
    public function getAuth()
    {
        return $this->_auth;
    }

    /**
     * @return \Magento\Framework\AuthorizationInterface
     */
    public function getAuthorization()
    {
        return $this->_authorization;
    }

    /**
     * @return \Magento\Backend\Model\UrlInterface
     */
    public function getBackendUrl()
    {
        return $this->_backendUrl;
    }

    /**
     * @return boolean
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getCanUseBaseUrl()
    {
        return $this->_canUseBaseUrl;
    }

    /**
     * @return \Magento\Framework\Data\Form\FormKey\Validator
     */
    public function getFormKeyValidator()
    {
        return $this->_formKeyValidator;
    }

    /**
     * @return \Lof\MarketPlace\Helper\Data
     */
    public function getHelper()
    {
        return $this->_helper;
    }

    /**
     * @return \Magento\Framework\Locale\ResolverInterface
     */
    public function getLocaleResolver()
    {
        return $this->_localeResolver;
    }

    /**
     * @return \Magento\Backend\Model\Session
     */
    public function getSession()
    {
        return $this->_session;
    }
    
    /**
     * @return \Magento\Framework\Url
     */
    public function getFrontendUrl(){
        return $this->_frontendUrl;
    }
    
    /**
     * Get Config
     * 
     * @return \Lof\MarketPlace\App\ConfigInterface
     */
    public function getConfig(){
        return $this->_config;
    }
    
    /**
     * Get Core Registry
     * 
     * @return \Magento\Framework\Registry
     */
    public function getCoreRegsitry(){
        return $this->_coreRegistry;
    }
    
    /**
     * Get Date Filter
     * 
     * @return \Magento\Framework\Stdlib\DateTime\Filter\Date
     */
    public function getDateFilter(){
        return $this->_dateFilter;
    }
}
