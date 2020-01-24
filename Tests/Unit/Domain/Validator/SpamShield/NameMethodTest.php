<?php
namespace In2code\Powermail\Tests\Unit\Domain\Validator\Spamshield;

use In2code\Powermail\Domain\Model\Answer;
use In2code\Powermail\Domain\Model\Field;
use In2code\Powermail\Domain\Model\Mail;
use In2code\Powermail\Domain\Validator\SpamShield\NameMethod;
use In2code\Powermail\Tests\Helper\TestingHelper;
use Nimut\TestingFramework\TestCase\UnitTestCase;
use TYPO3\CMS\Core\Exception;

/**
 * Class NameMethodTest
 * @coversDefaultClass \In2code\Powermail\Domain\Validator\SpamShield\NameMethod
 */
class NameMethodTest extends UnitTestCase
{

    /**
     * @var \In2code\Powermail\Domain\Validator\SpamShield\NameMethod
     */
    protected $generalValidatorMock;

    /**
     * @return void
     * @throws Exception
     */
    public function setUp()
    {
        TestingHelper::initializeTypoScriptFrontendController();
        $this->generalValidatorMock = $this->getAccessibleMock(
            NameMethod::class,
            ['dummy'],
            [
                new Mail(),
                [],
                []
            ]
        );
    }

    /**
     * @return void
     */
    public function tearDown()
    {
        unset($this->generalValidatorMock);
    }

    /**
     * Dataprovider spamCheckReturnsVoid()
     *
     * @return array
     */
    public function spamCheckReturnsVoidDataProvider()
    {
        return [
            [
                [
                    'firstname' => 'abcdef',
                    'lastname' => 'abcdef',
                    'xyz' => '123',
                ],
                true
            ],
            [
                [
                    'firstnamex' => 'abcdef',
                    'lastname' => 'abcdefg',
                    'xyz' => '123',
                ],
                false
            ],
            [
                [
                    'first_name' => 'viagra',
                    'surname' => 'viagra',
                    'xyz' => '123',
                ],
                true
            ],
        ];
    }

    /**
     * @param array $answerProperties
     * @param bool $expectedOverallSpamIndicator
     * @return void
     * @dataProvider spamCheckReturnsVoidDataProvider
     * @test
     * @covers ::spamCheck
     */
    public function spamCheckReturnsVoid($answerProperties, $expectedOverallSpamIndicator)
    {
        $mail = new Mail();
        foreach ($answerProperties as $fieldName => $value) {
            $field = new Field();
            $field->setMarker($fieldName);
            $answer = new Answer();
            $answer->setField($field);
            $answer->_setProperty('value', $value);
            $answer->setValueType(132);
            $mail->addAnswer($answer);
        }

        $this->generalValidatorMock->_set('mail', $mail);
        $this->assertSame($expectedOverallSpamIndicator, $this->generalValidatorMock->_callRef('spamCheck'));
    }
}
