<?php
namespace TYPO3\Form\Core\Model;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3.Form".                 *
 *                                                                        *
 *                                                                        */

use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * A base form element, which is the starting point for creating custom (PHP-based)
 * Form Elements.
 *
 * **This class is meant to be subclassed by developers.**
 *
 * A *FormElement* is a part of a *Page*, which in turn is part of a FormDefinition.
 * See {@link FormDefinition} for an in-depth explanation.
 *
 * Often, you should rather subclass this class instead of directly
 * implementing {@link FormElementInterface}.
 *
 * Still, it is quite rare that you need to subclass this class; often
 * you can just use the {@link GenericFormElement} and replace some templates.
 */
abstract class AbstractFormElement extends Renderable\AbstractRenderable implements FormElementInterface {

	/**
	 * @var mixed
	 */
	protected $defaultValue = NULL;

	/**
	 * @var array
	 */
	protected $properties = array();

	/**
	 * @var \TYPO3\FLOW3\Validation\Validator\ConjunctionValidator
	 */
	protected $conjunctionValidator;

	/**
	 * Constructor. Needs this FormElement's identifier and the FormElement type
	 *
	 * @param string $identifier The FormElement's identifier
	 * @param string $type The Form Element Type
	 * @api
	 */
	public function __construct($identifier, $type) {
		if (!is_string($identifier) || strlen($identifier) === 0) {
			throw new \TYPO3\Form\Exception\IdentifierNotValidException('The given identifier was not a string or the string was empty.', 1325574803);
		}
		$this->identifier = $identifier;
		$this->type = $type;
	}

	public function getDefaultValue() {
		return $this->defaultValue;
	}

	public function setDefaultValue($defaultValue) {
		$this->defaultValue = $defaultValue;
	}

	public function addValidator(\TYPO3\FLOW3\Validation\Validator\ValidatorInterface $validator) {
		if ($this->conjunctionValidator === NULL) {
			$this->conjunctionValidator = new \TYPO3\FLOW3\Validation\Validator\ConjunctionValidator();
		}
		$this->conjunctionValidator->addValidator($validator);
	}

	public function getValidator() {
		if ($this->conjunctionValidator === NULL) {
			$this->conjunctionValidator = new \TYPO3\FLOW3\Validation\Validator\ConjunctionValidator();
		}
		return $this->conjunctionValidator;
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 * @return void
	 */
	public function setProperty($key, $value) {
		$this->properties[$key] = $value;
	}

	/**
	 * @return array
	 */
	public function getProperties() {
		return $this->properties;
	}
}
?>