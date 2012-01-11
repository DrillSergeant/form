<?php
namespace TYPO3\Form\Core\Renderer;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3.Form".                 *
 *                                                                        *
 *                                                                        */

use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * The renderer for the form
 *
 * @todo greatly expand documentation
 */
class FluidFormRenderer extends \TYPO3\Fluid\View\TemplateView implements RendererInterface {

	public function setControllerContext(\TYPO3\FLOW3\MVC\Controller\ControllerContext $controllerContext) {
		$this->controllerContext = $controllerContext;
	}

	public function renderRenderable(\TYPO3\Form\Core\Model\Renderable\RootRenderableInterface $renderable) {
		$renderableType = $renderable->getType();

		if ($renderable->getRendererClassName() !== NULL && $renderable->getRendererClassName() !== get_class($this)) {
			$rendererClassName = $renderable->getRendererClassName();
			$renderer = new $rendererClassName;
			if (!($renderer instanceof RendererInterface)) {
				throw new \TYPO3\Form\Exception\RenderingException(sprintf('The renderer class "%s" for "%s" does not implement RendererInterface.', $rendererClassName, $renderableType), 1326098022);
			}
			$renderer->setControllerContext($this->controllerContext);
			return $renderer->renderRenderable($renderable);
		}

		$renderingOptions = $renderable->getRenderingOptions();

		$renderablePathAndFilename = $this->getPathAndFilenameForRenderable($renderableType, $renderingOptions);
		$parsedRenderable = $this->getParsedRenderable($renderable->getType(), $renderablePathAndFilename);

		if ($this->getCurrentRenderingContext() === NULL) {
				// We do not have a "current" rendering context yet, so we use the base rendering context
			$this->baseRenderingContext->setControllerContext($this->controllerContext);
			$renderingContext = $this->baseRenderingContext;
		} else {
			$renderingContext = clone $this->getCurrentRenderingContext();
		}

		if (!isset($renderingOptions['renderableNameInTemplate'])) {
			throw new \TYPO3\Form\Exception\RenderingException(sprintf('The Renderable "%s" did not have the rendering option "renderableNameInTemplate" defined.', $renderableType), 1326094948);
		}

		$templateVariableContainer = new \TYPO3\Fluid\Core\ViewHelper\TemplateVariableContainer(array($renderingOptions['renderableNameInTemplate'] => $renderable));
		$renderingContext->injectTemplateVariableContainer($templateVariableContainer);

		if ($parsedRenderable->hasLayout()) {
			$renderableLayoutName = $parsedRenderable->getLayoutName($renderingContext);
			$renderableLayoutPathAndFilename = $this->getPathAndFilenameForRenderableLayout($renderableLayoutName, $renderingOptions);
			$parsedLayout = $this->getParsedRenderable($renderableLayoutName, $renderableLayoutPathAndFilename);

			$this->startRendering(self::RENDERING_LAYOUT, $parsedRenderable, $renderingContext);
			$output = $parsedLayout->render($renderingContext);
			$this->stopRendering();
		} else {
			$this->startRendering(self::RENDERING_TEMPLATE, $parsedRenderable, $renderingContext);
			$output = $parsedRenderable->render($renderingContext);
			$this->stopRendering();
		}

		return $output;
	}

	protected function getPathAndFilenameForRenderable($renderableType, array $renderingOptions) {
		if (!isset($renderingOptions['templatePathPattern'])) {
			var_dump($renderingOptions);
			throw new \TYPO3\Form\Exception\RenderingException(sprintf('The Renderable "%s" did not have the rendering option "templatePathPattern" defined.', $renderableType), 1326094041);
		}
		list($packageKey, $shortRenderableType) = explode(':', $renderableType);

		return strtr($renderingOptions['templatePathPattern'], array(
			'{@package}' => $packageKey,
			'{@type}' => $shortRenderableType
		));
	}

	protected function getPathAndFilenameForRenderableLayout($renderableType, array $renderingOptions) {
		if (!isset($renderingOptions['layoutPathPattern'])) {
			throw new \TYPO3\Form\Exception\RenderingException(sprintf('The Renderable "%s" did not have the rendering option "layoutPathPattern" defined.', $renderableType), 1326094161);
		}
		list($packageKey, $shortRenderableType) = explode(':', $renderableType);

		return strtr($renderingOptions['layoutPathPattern'], array(
			'{@package}' => $packageKey,
			'{@type}' => $shortRenderableType
		));
	}



	protected function getParsedRenderable($renderableType, $renderablePathAndFilename) {
		if (!file_exists($renderablePathAndFilename)) {
			throw new \Exception('TODO (fix exception message): Path ' . $renderablePathAndFilename . ' not found');
		}
		$templateModifiedTimestamp = \filemtime($renderablePathAndFilename);
		$renderableIdentifier = sprintf('renderable_%s_%s', str_replace(array('.', ':'), '_', $renderableType), sha1($renderablePathAndFilename . '|' . $templateModifiedTimestamp));

		if ($this->templateCompiler->has($renderableIdentifier)) {
			$parsedRenderable = $this->templateCompiler->get($renderableIdentifier);
		} else {
			$parsedRenderable = $this->templateParser->parse(file_get_contents($renderablePathAndFilename));
			if ($parsedRenderable->isCompilable()) {
				$this->templateCompiler->store($renderableIdentifier, $parsedRenderable);
			}
		}
		return $parsedRenderable;
	}
}
?>