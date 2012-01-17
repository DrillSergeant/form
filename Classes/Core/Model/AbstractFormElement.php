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
 * Subclassing this class is a good starting-point for implementing custom PHP-based
 * Form Elements.
 *
 * Most of the functionality and API is implemented in {@link \TYPO3\Form\Core\Model\Renderable\AbstractRenderable}, so
 * make sure to check out this class as well.
 *
 * Still, it is quite rare that you need to subclass this class; often
 * you can just use the {@link \TYPO3\Form\FormElements\GenericFormElement} and replace some templates.
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

	/**
	 * @param \TYPO3\FLOW3\Validation\Validator\ValidatorInterface $validator
	 * @todo this method might become part of the interface...
	 */
	public function addValidator(\TYPO3\FLOW3\Validation\Validator\ValidatorInterface $validator) {
		$formDefinition = $this->getRootForm();
		if ($formDefinition !== NULL) {
			$formDefinition->getProcessingRule($this->getIdentifier())->addValidator($validator);
		} else {
			throw new \TYPO3\Form\Exception\FormDefinitionConsistencyException(sprintf('The form element "%s" is not attached to a parent form, thus addValidator() cannot be called.', $this->identifier), 1326803371);
		}
	}

	/**
	 * @return \TYPO3\FLOW3\Validation\Validator\ValidatorInterface
	 * @todo this method might become part of the interface...
	 */
	public function getValidator() {
		$formDefinition = $this->getRootForm();
		if ($formDefinition !== NULL) {
			return $formDefinition->getProcessingRule($this->getIdentifier())->getValidator();
		} else {
			throw new \TYPO3\Form\Exception\FormDefinitionConsistencyException(sprintf('The form element "%s" is not attached to a parent form, thus getValidator() cannot be called.', $this->identifier), 1326803398);
		}
	}

	public function setProperty($key, $value) {
		$this->properties[$key] = $value;
	}

	public function getProperties() {
		return $this->properties;
	}
}
?>