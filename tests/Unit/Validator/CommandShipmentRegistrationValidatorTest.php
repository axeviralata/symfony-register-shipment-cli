<?php
declare(strict_types=1);


namespace App\Tests\Unit\Validator;

use App\Validator\CommandShipmentRegistrationValidator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class CommandShipmentRegistrationValidatorTest extends KernelTestCase
{
    private CommandShipmentRegistrationValidator $validator;

    protected function setUp(): void
    {
        self::bootKernel();

        $container = static::getContainer();
        $this->validator = $container->get(CommandShipmentRegistrationValidator::class);
    }

    /**
     * @test
     */
    public function validateInputWithNoViolations()
    {
        // Mock the InputInterface
        $inputMock = $this->createMock(InputInterface::class);
        // ups
        $inputMock->method('getOption')->willReturnMap([
            ['shipping-provider', 'ups'],
            ['order', '{"order_id": 11123234,"country":"LT","street":"addresstest","city":"Vilnius","zip_code":77777}']
        ]);
        $violations = $this->validator->validateInput($inputMock);
        $this->assertEmpty($violations, 'No validation errors should be present for valid data');
        // dhl
        $inputMock->method('getOption')->willReturnMap([
            ['shipping-provider', 'dhl'],
            ['order', '{"order_id": 11123234,"country":"LT","address":"addresstest","town":"Vilnius","zip_code":77777}']
        ]);
        $violations = $this->validator->validateInput($inputMock);
        $this->assertEmpty($violations, 'No validation errors should be present for valid data');
        // omniva
        $inputMock->method('getOption')->willReturnMap([
            ['shipping-provider', 'omniva'],
            ['order', '{"order_id": 11123234,"country":"LT","post_code":77777}']
        ]);
        $violations = $this->validator->validateInput($inputMock);
        $this->assertEmpty($violations, 'No validation errors should be present for valid data');

    }

    /**
     * @test
     */
    public function validateInputNotJsonForOrderAsInput()
    {
        $inputMock = $this->createMock(InputInterface::class);
        // ups
        $inputMock->method('getOption')->willReturnMap([
            ['shipping-provider', 'dhl'],
            ['order', 'notJsonInput']
        ]);
        $violations = $this->validator->validateInput($inputMock);
        $this->assertNotEmpty($violations, 'Wrong order option type(not a json) should create a violation');
    }

    /**
     * @test
     */
    public function buildResponseArrayWrongParseOrNotArray()
    {
        $testList = new ConstraintViolationList([
            new ConstraintViolation('Message1', null, [], null, 'property1', 'invalidValue1'),
            new ConstraintViolation('Message2', null, [], null, 'property2', 'invalidValue2'),
        ]);

        $violations = $this->validator->buildResponseArray($testList);
        $this->assertIsArray($violations, 'Returned value is not array');
        $this->assertEquals(['invalidValue1: Message1', 'invalidValue2: Message2'], $violations, 'Returned data is not matching input data');
    }


}