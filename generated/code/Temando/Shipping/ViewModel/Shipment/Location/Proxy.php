<?php
namespace Temando\Shipping\ViewModel\Shipment\Location;

/**
 * Proxy class for @see \Temando\Shipping\ViewModel\Shipment\Location
 */
class Proxy extends \Temando\Shipping\ViewModel\Shipment\Location implements \Magento\Framework\ObjectManager\NoninterceptableInterface
{
    /**
     * Object Manager instance
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager = null;

    /**
     * Proxied instance name
     *
     * @var string
     */
    protected $_instanceName = null;

    /**
     * Proxied instance
     *
     * @var \Temando\Shipping\ViewModel\Shipment\Location
     */
    protected $_subject = null;

    /**
     * Instance shareability flag
     *
     * @var bool
     */
    protected $_isShared = null;

    /**
     * Proxy constructor
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param string $instanceName
     * @param bool $shared
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager, $instanceName = '\\Temando\\Shipping\\ViewModel\\Shipment\\Location', $shared = true)
    {
        $this->_objectManager = $objectManager;
        $this->_instanceName = $instanceName;
        $this->_isShared = $shared;
    }

    /**
     * @return array
     */
    public function __sleep()
    {
        return ['_subject', '_isShared', '_instanceName'];
    }

    /**
     * Retrieve ObjectManager from global scope
     */
    public function __wakeup()
    {
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    }

    /**
     * Clone proxied instance
     */
    public function __clone()
    {
        $this->_subject = clone $this->_getSubject();
    }

    /**
     * Get proxied instance
     *
     * @return \Temando\Shipping\ViewModel\Shipment\Location
     */
    protected function _getSubject()
    {
        if (!$this->_subject) {
            $this->_subject = true === $this->_isShared
                ? $this->_objectManager->get($this->_instanceName)
                : $this->_objectManager->create($this->_instanceName);
        }
        return $this->_subject;
    }

    /**
     * {@inheritdoc}
     */
    public function getShipFromAddressHtml()
    {
        return $this->_getSubject()->getShipFromAddressHtml();
    }

    /**
     * {@inheritdoc}
     */
    public function getShipToAddressHtml()
    {
        return $this->_getSubject()->getShipToAddressHtml();
    }

    /**
     * {@inheritdoc}
     */
    public function getFinalRecipientAddressHtml()
    {
        return $this->_getSubject()->getFinalRecipientAddressHtml();
    }

    /**
     * {@inheritdoc}
     */
    public function getReturnFromAddressHtml()
    {
        return $this->_getSubject()->getReturnFromAddressHtml();
    }

    /**
     * {@inheritdoc}
     */
    public function getReturnToAddressHtml()
    {
        return $this->_getSubject()->getReturnToAddressHtml();
    }

    /**
     * {@inheritdoc}
     */
    public function hasOriginLocation()
    {
        return $this->_getSubject()->hasOriginLocation();
    }
}
