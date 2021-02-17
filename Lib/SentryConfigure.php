<?php

App::uses('SentryErrorHandler', 'Sentry.Lib');

/**
 * Class SentryConfigure
 *
 * Configuration wrapper for Sentry. Designed to be used in bootstrap.php
 *
 *    // Setup Sentry configuration
 *    App::uses('SentryConfigure', 'Sentry.Lib');
 *    new SentryConfigure();
 *
 */
class SentryConfigure {
     private $defaults = ['traces_sample_rate' => 1.0];
     private $clientConfig = [];

     public function __construct($throwableIgnoreList = []) {
         $userConfig = Configure::read('Sentry.init');
         if (is_array($userConfig)) {
             $this->clientConfig = array_merge_recursive($this->defaults, $userConfig);
         }

         $this->addThrowableFilter($throwableIgnoreList);
         Sentry\init($this->clientConfig);
     }

    /**
     * Adds an throwable (the root class for both Error & Exception) filter function to the SentryErrorHandler
     *
     * @param array $throwableIgnoreList
     * @returns void
     */
    private function addThrowableFilter($throwableIgnoreList = []) {
        SentryErrorHandler::$throwableFilterFunc = function (Throwable $throwable) use (&$throwableIgnoreList): bool {
            return in_array(get_class($throwable), $throwableIgnoreList);
        };
    }

}