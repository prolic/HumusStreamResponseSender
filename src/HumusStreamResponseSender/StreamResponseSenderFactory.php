<?php

namespace HumusStreamResponseSender;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * @category   Humus
 * @package    HumusStreamResponseSender
 */
class StreamResponseSenderFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');
        if (isset($config[__NAMESPACE__])) {
            $options = $config[__NAMESPACE__];
        } else {
            $options = null;
        }
        $streamResponseSender = new StreamResponseSender($options);
        $streamResponseSender->setRequest($serviceLocator->get('Request'));
        return $streamResponseSender;
    }
}
