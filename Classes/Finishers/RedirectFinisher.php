<?php
namespace TYPO3\Form\Finishers;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3.Form".                 *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 *  of the License, or (at your option) any later version.                *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

/**
 * This finisher redirects to another Controller.
 */
class RedirectFinisher extends \TYPO3\Form\Core\Model\AbstractFinisher {

	protected $defaultOptions = array(
		'package' => NULL,
		'controller' => NULL,
		'action' => '',
		'arguments' => array(),
		'delay' => 0,
		'statusCode' => 303,
	);

	public function executeInternal() {
		$formRuntime = $this->finisherContext->getFormRuntime();
		$request = $formRuntime->getRequest()->getRootRequest();

		$packageKey = $this->parseOption('package');
		$controllerName = $this->parseOption('controller');
		$actionName = $this->parseOption('action');
		$arguments = $this->parseOption('arguments');
		$delay = (integer)$this->parseOption('delay');
		$statusCode = $this->parseOption('statusCode');

		$subpackageKey = NULL;
		if ($packageKey !== NULL && strpos($packageKey, '\\') !== FALSE) {
			list($packageKey, $subpackageKey) = explode('\\', $packageKey, 2);
		}
		$uriBuilder = new \TYPO3\FLOW3\MVC\Web\Routing\UriBuilder();
		$uriBuilder->setRequest($request);
		$uriBuilder->reset();

		$uri = $uriBuilder->uriFor($actionName, $arguments, $controllerName, $packageKey, $subpackageKey);
		$uri = $request->getBaseUri() . $uri;
		$escapedUri = htmlentities($uri, ENT_QUOTES, 'utf-8');

		$response = $formRuntime->getResponse();
		while ($response instanceof \TYPO3\FLOW3\MVC\Web\SubResponse) {
			$response = $response->getParentResponse();
		}
		$response->setContent('<html><head><meta http-equiv="refresh" content="' . $delay . ';url=' . $escapedUri . '"/></head></html>');
		$response->setStatus($statusCode);
		if ($delay === 0) {
			$response->setHeader('Location', (string)$uri);
		}
	}

	/**
	 * @param array $options configuration options in the format array('@action' => 'foo', '@controller' => 'bar', '@package' => 'baz')
	 * @return void
	 * @api
	 */
	public function setOptions(array $options) {
		$this->options = $options;
	}
}
?>