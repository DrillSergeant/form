<?php
namespace TYPO3\Form\FormElements;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3.Form".                 *
 *                                                                        *
 *                                                                        */

use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * A Section, being part of a bigger Page
 *
 * **This class is not meant to be subclassed by developers.**
 *
 * This class contains multiple FormElements ({@link FormElementInterface}).
 *
 * Please see {@link FormDefinition} for an in-depth explanation.
 */
class Section extends \TYPO3\Form\Core\Model\AbstractSection implements \TYPO3\Form\Core\Model\FormElementInterface {

	public function getUniqueIdentifier() {
		$formDefinition = $this->getRootForm();
		return sprintf('%s-%s', $formDefinition->getIdentifier(), $this->identifier);
	}

	public function getDefaultValue() {
		return NULL;
	}
	public function getProperties() {
		return array();
	}
	public function setDefaultValue($defaultValue) {

	}
	public function setProperty($key, $value) {

	}
	public function setRenderingOption($key, $value) {
		$this->renderingOptions[$key] = $value;
	}

	public function getValidators() {
		$formDefinition = $this->getRootForm();
		return $formDefinition->getProcessingRule($this->getIdentifier())->getValidators();
	}

	public function isRequired() {
		// TODO get rid of code duplication
		foreach ($this->getValidators() as $validator) {
			if ($validator instanceof \TYPO3\FLOW3\Validation\Validator\NotEmptyValidator) {
				return TRUE;
			}
		}
		return FALSE;
	}
}
?>