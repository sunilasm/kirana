<?php
namespace WebbyTroops\WishlistAPI\Plugin;

use Magento\Framework\Exception\AuthorizationException;

class RestrictAccess
{
    /**
     * Config path
     */
    const XML_WISHLIST_ACTIVE = 'wishlist/general/active';
    const WISHLIST_RESOURCE = '/V1/wishlist/';

    /**
     * @var \Magento\Framework\App\Config\ReinitableConfigInterface
     */
    protected $config;

    /**
     * @var array
     */
    protected $resources;

    /**
     * AnonymousResourceSecurity constructor.
     *
     * @param \Magento\Framework\App\Config\ReinitableConfigInterface $config
     * @param \Magento\Framework\App\RequestInterface $requestInterface $requestInterface
     */
    public function __construct(
        \Magento\Framework\App\Config\ReinitableConfigInterface $config,
        \Magento\Framework\App\RequestInterface $requestInterface
    ) {
        $this->config = $config;
        $this->request = $requestInterface;
    }

    /**
     * Filter config values.
     *
     * @param Converter $subject
     * @param array $nodes
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterConvert(\Magento\Webapi\Model\Config\Converter $subject, $nodes)
    {
        $uriPath = explode('/rest', $this->request->getRequestUri());
        if (isset($uriPath[1]) && (empty($nodes) || $uriPath[1] != self::WISHLIST_RESOURCE)) {
            return $nodes;
        }
        $isActive = $this->config->getValue(self::XML_WISHLIST_ACTIVE);
        if ($isActive) {
            return $nodes;
        } else {
            throw new AuthorizationException(
                __('Consumer is not authorized to access this resource')
            );
        }
    }
}
