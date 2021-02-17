<?php


/**
 * Description of SentryErrorHandler
 *
 * @author Sandreu
 */
class SentryErrorHandler extends ErrorHandler
{

    public static $throwableFilterFunc = null;

    public static function handleError($code, $description, $file = null, $line = null, $context = null)
    {
        try {
            $e = new ErrorException($description, 0, $code, $file, $line);
            self::sentryLog($e);

            return parent::handleError($code, $description, $file, $line, $context);
        } catch (Exception $e) {
            self::handleException($e);
        }
    }

    protected static function sentryLog($exception)
    {
        Sentry\captureException($exception);
    }

    public static function handleException($exception)
    {

        // If the filter returns true, then don't send to sentry as it's in the ignoreList
        if (
            is_callable(self::$throwableFilterFunc) &&
            call_user_func(self::$throwableFilterFunc, $exception) == true
        ){
            parent::handleException($exception);
            return;
        }

        try {
            self::sentryLog($exception);
        } finally {
            parent::handleException($exception);
        }
    }
}
