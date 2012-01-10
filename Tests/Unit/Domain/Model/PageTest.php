<?php
namespace TYPO3\Form\Tests\Unit\Domain\Model;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3.Form".                 *
 *                                                                        *
 *                                                                        */

use TYPO3\Form\Domain\Model\FormDefinition;
use TYPO3\Form\Domain\Model\Page;

/**
 * Test for Page Domain Model
 * @covers \TYPO3\Form\Domain\Model\Page<extended>
 * @covers \TYPO3\Form\Domain\Model\AbstractFormElement<extended>
 */
class PageTest extends \TYPO3\FLOW3\Tests\UnitTestCase {

	/**
	 * @test
	 */
	public function identifierSetInConstructorCanBeReadAgain() {
		$page = new Page('foo');
		$this->assertSame('foo', $page->getIdentifier());

		$page = new Page('bar');
		$this->assertSame('bar', $page->getIdentifier());
	}

	public function invalidIdentifiers() {
		return array(
			'Null Identifier' => array(NULL),
			'Integer Identifier' => array(42),
			'Empty String Identifier' => array('')
		);
	}

	/**
	 * @test
	 * @expectedException TYPO3\Form\Exception\IdentifierNotValidException
	 * @dataProvider invalidIdentifiers
	 */
	public function ifBogusIdentifierSetInConstructorAnExceptionIsThrown($identifier) {
		new Page($identifier);
	}

	/**
	 * @test
	 */
	public function getElementsReturnsEmptyArrayByDefault() {
		$page = new Page('foo');
		$this->assertSame(array(), $page->getElements());
	}

	/**
	 * @test
	 * @expectedException TYPO3\Form\Exception\FormDefinitionConsistencyException
	 */
	public function aFormElementCanOnlyBeAttachedToASinglePage() {
		$element = $this->getMockBuilder('TYPO3\Form\Domain\Model\AbstractFormElement')->setMethods(array('dummy'))->disableOriginalConstructor()->getMock();

		$page1 = new Page('bar1');
		$page2 = new Page('bar2');

		$page1->addElement($element);
		$page2->addElement($element);
	}

	/**
	 * @test
	 */
	public function addElementAddsElementAndSetsBackReferenceToPage() {
		$page = new Page('bar');
		$element = $this->getMockBuilder('TYPO3\Form\Domain\Model\AbstractFormElement')->setMethods(array('dummy'))->disableOriginalConstructor()->getMock();
		$page->addElement($element);
		$this->assertSame(array($element), $page->getElements());
		$this->assertSame($page, $element->getParentRenderable());
	}

	/**
	 * @test
	 */
	public function createElementCreatesElementAndAddsItToForm() {
		$formDefinition = $this->getDummyFormDefinition();
		$page = $formDefinition->createPage('myPage');
		$element = $page->createElement('myElement', 'TYPO3.Form:MyElementType');

		$this->assertSame('myElement', $element->getIdentifier());
		$this->assertInstanceOf('TYPO3\Form\Domain\Model\GenericFormElement', $element);
		$this->assertSame('TYPO3.Form:MyElementType', $element->getType());
		$this->assertSame(array($element), $page->getElements());
	}

	/**
	 * @test
	 */
	public function createElementSetsAdditionalPropertiesInElement() {
		$formDefinition = $this->getDummyFormDefinition();
		$page = $formDefinition->createPage('myPage');
		$element = $page->createElement('myElement', 'TYPO3.Form:MyElementTypeWithAdditionalProperties');

		$this->assertSame('my label', $element->getLabel());
		$this->assertSame('This is the default value', $element->getDefaultValue());
		$this->assertSame(array('property1' => 'val1', 'property2' => 'val2'), $element->getProperties());
		$this->assertSame(array('ro1' => 'rv1', 'ro2' => 'rv2'), $element->getRenderingOptions());
		$this->assertSame('MyRendererClassName', $element->getRendererClassName());
	}

	/**
	 * @test
	 * @expectedException TYPO3\Form\Exception\FormDefinitionConsistencyException
	 */
	public function createElementThrowsExceptionIfPageIsNotAttachedToParentForm() {
		$page = new Page('id');
		$page->createElement('myElement', 'TYPO3.Form:MyElementType');
	}

	/**
	 * @test
	 * @expectedException TYPO3\Form\Exception\TypeDefinitionNotFoundException
	 */
	public function createElementThrowsExceptionIfImplementationClassNameNotFound() {
		$formDefinition = $this->getDummyFormDefinition();
		$page = $formDefinition->createPage('myPage');
		$element = $page->createElement('myElement', 'TYPO3.Form:MyElementTypeWithoutImplementationClassName');
	}

	/**
	 * @test
	 * @expectedException TYPO3\Form\Exception\TypeDefinitionNotValidException
	 */
	public function createElementThrowsExceptionIfUnknownPropertyFoundInTypeDefinition() {
		$formDefinition = $this->getDummyFormDefinition();
		$page = $formDefinition->createPage('myPage');
		$element = $page->createElement('myElement', 'TYPO3.Form:MyElementTypeWithUnknownProperties');
	}

	/**
	 * @test
	 */
	public function moveElementBeforeMovesElementBeforeReferenceElement() {
		$formDefinition = $this->getDummyFormDefinition();
		$page = $formDefinition->createPage('myPage');
		$element1 = $page->createElement('myElement', 'TYPO3.Form:MyElementType');
		$element2 = $page->createElement('myElement2', 'TYPO3.Form:MyElementType');

		$this->assertSame(array($element1, $element2), $page->getElements());
		$page->moveElementBefore($element2, $element1);
		$this->assertSame(array($element2, $element1), $page->getElements());
	}

	/**
	 * @test
	 * @expectedException TYPO3\Form\Exception\FormDefinitionConsistencyException
	 */
	public function moveElementBeforeThrowsExceptionIfElementsAreNotOnSamePage() {
		$formDefinition = $this->getDummyFormDefinition();
		$page1 = $formDefinition->createPage('myPage1');
		$page2 = $formDefinition->createPage('myPage2');

		$element1 = $page1->createElement('myElement', 'TYPO3.Form:MyElementType');
		$element2 = $page2->createElement('myElement2', 'TYPO3.Form:MyElementType');

		$page1->moveElementBefore($element1, $element2);
	}

	/**
	 * @test
	 */
	public function moveElementAfterMovesElementAfterReferenceElement() {
		$formDefinition = $this->getDummyFormDefinition();
		$page = $formDefinition->createPage('myPage');
		$element1 = $page->createElement('myElement', 'TYPO3.Form:MyElementType');
		$element2 = $page->createElement('myElement2', 'TYPO3.Form:MyElementType');

		$this->assertSame(array($element1, $element2), $page->getElements());
		$page->moveElementAfter($element1, $element2);
		$this->assertSame(array($element2, $element1), $page->getElements());
	}

	/**
	 * @test
	 * @expectedException TYPO3\Form\Exception\FormDefinitionConsistencyException
	 */
	public function moveElementAfterThrowsExceptionIfElementsAreNotOnSamePage() {
		$formDefinition = $this->getDummyFormDefinition();
		$page1 = $formDefinition->createPage('myPage1');
		$page2 = $formDefinition->createPage('myPage2');

		$element1 = $page1->createElement('myElement', 'TYPO3.Form:MyElementType');
		$element2 = $page2->createElement('myElement2', 'TYPO3.Form:MyElementType');

		$page1->moveElementAfter($element1, $element2);
	}

	/**
	 * @test
	 */
	public function removeElementRemovesElementFromCurrentPageAndUnregistersItFromForm() {
		$formDefinition = $this->getDummyFormDefinition();
		$page1 = $formDefinition->createPage('myPage1');
		$element1 = $page1->createElement('myElement', 'TYPO3.Form:MyElementType');

		$page1->removeElement($element1);

		$this->assertSame(array(), $page1->getElements());
		$this->assertNull($formDefinition->getElementByIdentifier('myElement'));

		$this->assertNull($element1->getParentRenderable());
	}

	/**
	 * @test
	 * @expectedException TYPO3\Form\Exception\FormDefinitionConsistencyException
	 */
	public function removeElementThrowsExceptionIfElementIsNotOnCurrentPage() {
		$formDefinition = $this->getDummyFormDefinition();
		$page1 = $formDefinition->createPage('myPage1');
		$element1 = $this->getMockBuilder('TYPO3\Form\Domain\Model\AbstractFormElement')->setMethods(array('dummy'))->disableOriginalConstructor()->getMock();

		$page1->removeElement($element1);
	}

	protected function getDummyFormDefinition() {
		return new FormDefinition('myForm', array(
			'formElementTypes' => array(
				'TYPO3.Form:Form' => array(),
				'TYPO3.Form:Page' => array(
					'implementationClassName' => 'TYPO3\Form\Domain\Model\Page'
				),
				'TYPO3.Form:MyElementType' => array(
					'implementationClassName' => 'TYPO3\Form\Domain\Model\GenericFormElement'
				),
				'TYPO3.Form:MyElementTypeWithAdditionalProperties' => array(
					'implementationClassName' => 'TYPO3\Form\Domain\Model\GenericFormElement',
					'label' => 'my label',
					'defaultValue' => 'This is the default value',
					'properties' => array(
						'property1' => 'val1',
						'property2' => 'val2'
					),
					'renderingOptions' => array(
						'ro1' => 'rv1',
						'ro2' => 'rv2'
					),
					'rendererClassName' => 'MyRendererClassName'
				),
				'TYPO3.Form:MyElementTypeWithoutImplementationClassName' => array(),
				'TYPO3.Form:MyElementTypeWithUnknownProperties' => array(
					'implementationClassName' => 'TYPO3\Form\Domain\Model\GenericFormElement',
					'unknownProperty' => 'foo'
				),

			)
		));
	}
}
?>