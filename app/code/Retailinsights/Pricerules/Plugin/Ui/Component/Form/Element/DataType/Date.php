<?php

namespace Retailinsights\Pricerules\Plugin\Ui\Component\Form\Element\DataType;

class Date
{
    public function aroundPrepare(
        \Magento\Ui\Component\Form\Element\DataType\Date $subject,
        \Closure $proceed
    ) {
        $configOrig = $subject->getData('config');
        $proceed();
        $config = $subject->getData('config');
        if (isset($configOrig['options']) && isset($configOrig['options']['dateFormat'])) {
            $config['options']['dateFormat'] = $configOrig['options']['dateFormat'];
        }

        if (isset($configOrig['options']) && isset($configOrig['options']['timeFormat'])) {
            $config['options']['timeFormat'] = $configOrig['options']['timeFormat'];
        }

        $subject->setData('config', $config);
    }
}
