<?php
namespace Temando\Shipping\Model\Shipment\ShipmentProviderInterface;

/**
 * Proxy class for @see \Temando\Shipping\Model\Shipment\ShipmentProviderInterface
 */
class Proxy implements \Temando\Shipping\Model\Shipment\ShipmentProviderInterface, \Magento\Framework\ObjectManager\NoninterceptableInterface
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
     * @var \Temando\Shipping\Model\Shipment\ShipmentProviderInterface
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
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager, $instanceName = '\\Temando\\Shipping\\Model\\Shipment\\ShipmentProviderInterface', $shared = true)
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
     * @return \Temando\Shipping\Model\Shipment\ShipmentProviderInterface
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
    public function getShipment()
    {
        return $this->_getSubject()->getShipment();
    }

    /**
     * {@inheritdoc}
     */
    public function setShipment(\Temando\Shipping\Model\ShipmentInterface $shipment)
    {
        return $this->_getSubject()->setShipment($shipment);
    }

    /**
     * {@inheritdoc}
     */
    public function getSalesShipment()
    {
        return $this->_getSubject()->getSalesShipment();
    }

    /**
     * {@inheritdoc}
     */
    public function setSalesShipment(\Magento\Sales\Api\Data\ShipmentInterface $shipment)
    {
        return $this->_getSubject()->setSalesShipment($shipment);
    }
}
