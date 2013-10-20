HumusStreamResponseSender
=========================

[![Build Status](https://travis-ci.org/prolic/HumusStreamResponseSender.png?branch=master)](https://travis-ci.org/prolic/HumusStreamResponseSender)
[![Coverage Status](https://coveralls.io/repos/prolic/HumusStreamResponseSender/badge.png)](https://coveralls.io/r/prolic/HumusStreamResponseSender)
[![Total Downloads](https://poser.pugx.org/prolic/humus-stream-response-sender/downloads.png)](https://packagist.org/packages/prolic/humus-stream-response-sender)
[![Latest Stable Version](https://poser.pugx.org/prolic/humus-stream-response-sender/v/stable.png)](https://packagist.org/packages/prolic/humus-stream-response-sender)
[![Latest Unstable Version](https://poser.pugx.org/prolic/humus-stream-response-sender/v/unstable.png)](https://packagist.org/packages/prolic/humus-stream-response-sender)
[![Dependency Status](https://www.versioneye.com/php/prolic:humus-stream-response-sender/dev-master/badge.png)](https://www.versioneye.com/php/prolic:humus-stream-response-sender)

Introduction
------------

HumusStreamResponseSender is a Zend Framework 2 module that sends stream responses
with HTTP Range header, XSendFile & pecl_http support.

Requirements
------------

* [Zend Mvc 2.2.1](https://github.com/zendframework/zf2) (latest master)
* [Zend Http 2.2.1](https://github.com/zendframework/zf2) (latest master)
* [Zend ModuleManager 2.2.1](https://github.com/zendframework/zf2) (latest master)

Features / Goals
----------------

* Send stream responses with Zend Framwork 2 [COMPLETE]
* Limit download speed [COMPLETE]
* Allow for range support (download resume) [COMPLETE]
* Send streams with pecl_http extension [INCOMPLETE]
* Send streams with X-SendFile [INCOMPLETE]
* Send streams with X-Accel-Redirect [INCOMPLETE]
* Add controller plugin for easy streaming from controllers [COMPLETE]

Installation
------------

 1.  Add `"prolic/humus-stream-response-sender": "dev-master"` to your `composer.json`
 2.  Run `php composer.phar install`
 3.  Enable the module in your `config/application.config.php` by adding `HumusStreamResponseSender` to `modules`

Configuration
-------------

Sample configuration:

    <?php
    return array(
        'HumusStreamResponseSender' => array(
            'enable_speed_limit' => true,
            'enable_range_support' => true,
            'chunk_size' => 1024 * 1024 //  = 1MB/s
        ),
    );

Usage of controller plugin
--------------------------

The simplest way to stream a response from a controller, is the stream plugin.

    class IndexController extends AbstractActionController
    {
        public function fileAction()
        {
            return $this->plugin('stream')->binaryFile('/path/to/my/file');
        }
    }

Additional notes
----------------

If the speed limit switch is set to true, the used chunksize will also be the download speed in bytes per second

The default configuration is:
- enable_speed_limit = false
- enable_range_support = false
- chunk_size = 262144
