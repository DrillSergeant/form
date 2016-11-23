<?php
namespace TYPO3\Form\ViewHelpers;

/*
 * This file is part of the TYPO3.Form package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Neos\FluidAdaptor\Core\ViewHelper\AbstractViewHelper;
use Neos\FluidAdaptor\Core\ViewHelper\Exception as ViewHelperException;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\ResourceManagement\Publishing\ResourcePublisher;
use TYPO3\Form\Factory\ArrayFormFactory;

/**
 * Output the configured stylesheets and JavaScript include tags for a given preset
 */
class RenderHeadViewHelper extends AbstractViewHelper
{
    /**
     * @var boolean
     */
    protected $escapeOutput = false;

    /**
     * @Flow\Inject
     * @var ResourcePublisher
     */
    protected $resourcePublisher;

    /**
     * @Flow\Inject
     * @var ArrayFormFactory
     */
    protected $formBuilderFactory;

    /**
     * @param string $presetName name of the preset to use
     * @return string the rendered form head
     */
    public function render($presetName = 'default')
    {
        $content = '';
        $presetConfiguration = $this->formBuilderFactory->getPresetConfiguration($presetName);
        $stylesheets = isset($presetConfiguration['stylesheets']) ? $presetConfiguration['stylesheets'] : [];
        foreach ($stylesheets as $stylesheet) {
            $content .= sprintf('<link href="%s" rel="stylesheet">', $this->resolveResourcePath($stylesheet['source']));
        }
        $javaScripts = isset($presetConfiguration['javaScripts']) ? $presetConfiguration['javaScripts'] : [];
        foreach ($javaScripts as $javaScript) {
            $content .= sprintf('<script src="%s"></script>', $this->resolveResourcePath($javaScript['source']));
        }
        return $content;
    }

    /**
     * @param string $resourcePath
     * @return string
     * @throws ViewHelperException
     */
    protected function resolveResourcePath($resourcePath)
    {
        // TODO: This method should be somewhere in the resource manager probably?
        $matches = [];
        preg_match('#resource://([^/]*)/Public/(.*)#', $resourcePath, $matches);
        if ($matches === []) {
            throw new ViewHelperException('Resource path "' . $resourcePath . '" can\'t be resolved.', 1328543327);
        }
        $package = $matches[1];
        $path = $matches[2];
        return $this->resourcePublisher->getStaticResourcesWebBaseUri() . 'Packages/' . $package . '/' . $path;
    }
}
