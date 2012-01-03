<?php
namespace TYPO3\Form\Tests\Unit\Domain\Model;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3.Form".                 *
 *                                                                        *
 *                                                                        */

use TYPO3\Form\Domain\Model\Form;
use TYPO3\Form\Domain\Model\Page;

/**
 * Test for Form Domain Model
 * @covers \TYPO3\Form\Domain\Model\Form
 */
class FormTest extends \TYPO3\FLOW3\Tests\UnitTestCase {

	/**
	 * @test
	 */
	public function identifierSetInConstructorCanBeReadAgain() {
		$form = new Form('foo');
		$this->assertSame('foo', $form->getIdentifier());

		$form = new Form('bar');
		$this->assertSame('bar', $form->getIdentifier());
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
		new Form($identifier);
	}

	/**
	 * @test
	 */
	public function getPagesReturnsEmptyArrayByDefault() {
		$form = new Form('foo');
		$this->assertSame(array(), $form->getPages());
	}

	/**
	 * @test
	 */
	public function addPageAddsPageToPagesArrayAndSetsBackReferenceToForm() {
		$form = new Form('foo');
		$page = new Page('bar');
		$form->addPage($page);
		$this->assertSame(array($page), $form->getPages());
		$this->assertSame($form, $page->getParentForm());
	}
}
?>