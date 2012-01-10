<?php
namespace TYPO3\Form\Domain\Model;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3.Form".                 *
 *                                                                        *
 *                                                                        */

/**
 * A base form element interface, which can be the starting point for creating
 * custom (PHP-based) Form Elements.
 *
 * A *FormElement* is a part of a *Page*, which in turn is part of a FormDefinition.
 * See {@link FormDefinition} for an in-depth explanation.
 *
 * Often, you should rather subclass {@link AbstractFormElement} instead of
 * implementing this interface.
 */
interface FormElementInterface extends RenderableInterface {

	/**
	 * Get the default value with which the Form Element should be initialized
	 * during display.
	 *
	 * @return mixed the default value for this Form Element
	 * @api
	 */
	public function getDefaultValue();

	/**
	 * Set the default value with which the Form Element should be initialized
	 * during display.
	 *
	 * @param mixed $defaultValue the default value for this Form Element
	 * @api
	 */
	public function setDefaultValue($defaultValue);

	/**
	 * @param string $key
	 * @param mixed $value
	 * @return void
	 */
	public function setProperty($key, $value);

	/**
	 * @return array
	 */
	public function getProperties();

	/**
	 * Set a rendering option
	 *
	 * @param string $key
	 * @param mixed $value
	 * @api
	 */
	public function setRenderingOption($key, $value);
}
?>