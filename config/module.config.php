<?php

return array(
    'controller_plugins' => array(
        'invokables' => array(
            'stream' => 'HumusStreamResponseSender\Controller\Plugin\Stream'
        )
    ),
    'service_manager' => array(
        'factories' => array(
            'HumusStreamResponseSender\StreamResponseSender' => 'HumusStreamResponseSender\StreamResponseSenderFactory'
        )
    )
);