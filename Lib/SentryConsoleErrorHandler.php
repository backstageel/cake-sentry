<?php


App::uses('ConsoleErrorHandler', 'Console');
/**
 * Description of SentryConsoleErrorHandler
 *
 * @author Sandreu
 */
class SentryConsoleErrorHandler extends ConsoleErrorHandler {

	protected static function sentryLog($exception) {
        Raven_Autoloader::register();
        App::uses('CakeRavenClient', 'Sentry.Lib');

        $client = new CakeRavenClient(Configure::read('Sentry.init.dsn'));
        $client->captureException($exception, get_class($exception), 'PHP');
	}

	public function handleException($exception) {
		try {
			self::sentryLog($exception);

			return parent::handleException($exception);
		} catch (Exception $e) {
			return parent::handleException($e);
		}
	}

	public function handleError($code, $description, $file = null, $line = null, $context = null) {
		try {
			$e = new ErrorException($description, 0, $code, $file, $line);
			self::sentryLog($e);

			return parent::handleError($code, $description, $file, $line, $context);
		} catch (Exception $e) {
			self::handleException($e);
		}
	}
}
