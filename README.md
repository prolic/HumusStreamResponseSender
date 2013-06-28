HumusStreamResponseSender
=========================

[![Build Status](https://travis-ci.org/prolic/HumusStreamResponseSender.png?branch=master)](https://travis-ci.org/prolic/HumusStreamResponseSender)
[![Coverage Status](https://coveralls.io/repos/prolic/HumusStreamResponseSender/badge.png)](https://coveralls.io/r/prolic/HumusStreamResponseSender)
[![Dependency Status](https://www.versioneye.com/user/projects/51cdf6155bca140002000998/badge.png)](https://www.versioneye.com/user/projects/51cdf6155bca140002000998)

Introduction
------------

HumusStreamResponseSender is a Zend Framework 2 module that sends stream responses with additional
features like download resume and speed limit

Requirements
------------

* [Zend Mvc 2.2.1](https://github.com/zendframework/zf2) (latest master)
* [Zend Http 2.2.1](https://github.com/zendframework/zf2) (latest master)

Features / Goals
----------------

* Send stream responses with Zend Framwork 2 [COMPLETE]
* Limit download speed [COMPLETE]
* Allow for download resume [COMPLETE]
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
            'enable_download_resume' => true,
            'chunk_size' => 1024 * 1024 //  = 1MB/s
        ),
    );

Usage of controller plugin
--------------------------

To return a stream response from a controller, you can use the stream plugin.

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
- enable_download_resume = false
- chunk_size = 262144
