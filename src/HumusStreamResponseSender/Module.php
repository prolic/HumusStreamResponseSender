<?php

namespace HumusStreamResponseSender;

use Zend\EventManager\EventInterface;
use Zend\EventManager\SharedEventManager;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\Mvc\ResponseSender\SendResponseEvent;

/**
 * @category Humus
 * @package HumusStreamResponseSender
 */
class Module implements
    ConfigProviderInterface,
    BootstrapListenerInterface
{
    /**
     * Listen to the bootstrap event
     *
     * @param EventInterface $e
     * @return array
     */
    public function onBootstrap(EventInterface $e)
    {
        $app = $e->getTarget();
        $serviceManager = $app->getServiceManager();
        $streamResponseSender = $serviceManager->get(__NAMESPACE__ . '\StreamResponseSender');
        $sharedEventManager = $app->getEventManager()->getSharedManager();
        /* @var $sharedEventManager SharedEventManager */
        $sharedEventManager->attach('Zend\Mvc\SendResponseListener', SendResponseEvent::EVENT_SEND_RESPONSE, $streamResponseSender);
    }

    /**
     * Returns configuration to merge with application configuration
     *
     * @return array|\Traversable
     */
    public function getConfig()
    {
        return array(
            'service_manager' => array(
                'factories' => array(
                    __NAMESPACE__ . '\StreamResponseSender' => __NAMESPACE__ . '\StreamResponseSenderFactory'
                )
            )
        );
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/../../autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
}
