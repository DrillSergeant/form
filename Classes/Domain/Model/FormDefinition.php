<?php
namespace TYPO3\Form\Domain\Model;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3.Form".                 *
 *                                                                        *
 *                                                                        */

use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * A Form
 */
class FormDefinition {

	/**
	 * The identifier
	 * @var string
	 */
	protected $identifier;

	/**
	 * The pages
	 * @var array<TYPO3\Form\Domain\Model\Page>
	 */
	protected $pages = array();

	/**
	 * Constructor. Needs this Form's identifier
	 *
	 * @param string $identifier The Form's identifier
	 * @return void
	 * @throws \TYPO3\Form\Exception\IdentifierNotValidException if the identifier was no non-empty string
	 * @api
	 */
	public function __construct($identifier) {
		if (!is_string($identifier) || strlen($identifier) === 0) {
			throw new \TYPO3\Form\Exception\IdentifierNotValidException('The given identifier was not a string or the string was empty.', 1325574803);
		}
		$this->identifier = $identifier;
	}

	/**
	 * Get the Form's identifier
	 *
	 * @return string The Form's identifier
	 * @api
	 */
	public function getIdentifier() {
		return $this->identifier;
	}

	/**
	 * Get the Form's pages
	 *
	 * @return array<TYPO3\Form\Domain\Model\Page> The Form's pages
	 * @api
	 */
	public function getPages() {
		return $this->pages;
	}

	/**
	 * Add a new page at the end of the form
	 *
	 * @param Page $page
	 * @api
	 */
	public function addPage(Page $page) {
		$this->pages[] = $page;
		$page->setParentForm($this);
		$page->setIndex(count($this->pages) - 1);
	}

	/**
	 * If index does not exist, returns NULL
	 * @param type $index
	 * @return type
	 */
	public function getPageByIndex($index) {
		return isset($this->pages[$index]) ? $this->pages[$index] : NULL;
	}
	/**
	 * @param \TYPO3\FLOW3\MVC\Web\Request $request
	 * @return \TYPO3\Form\Domain\Model\FormRuntime
	 * @api
	 * @todo request arguments rauspuhlen, current page befüllen
	 */
	public function bind(\TYPO3\FLOW3\MVC\Web\Request $request) {
		return new FormRuntime($this, $request);
	}

}
?>