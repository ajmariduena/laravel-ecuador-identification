<?php

namespace Luilliarcec\LaravelEcuadorIdentification\Tests\Unit;

use Luilliarcec\LaravelEcuadorIdentification\Support\EcuadorIdentification;
use Luilliarcec\LaravelEcuadorIdentification\Tests\TestCase;

class CustomValidationTest extends TestCase
{
    /**
     * @var EcuadorIdentification
     */
    protected $ecuadorIdentification;

    /**
     * This method is called before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->ecuadorIdentification = new EcuadorIdentification();
    }

    /** @test */
    public function validate_that_all_type_document_return_correct_value()
    {
        $this->assertEquals($this->ecuadorIdentification->validateAllTypeIdentification('1710034065'), '05'); // Personal Identification
        $this->assertEquals($this->ecuadorIdentification->validateAllTypeIdentification('1710034065001'), '04'); // Natural RUC
        $this->assertEquals($this->ecuadorIdentification->validateAllTypeIdentification('1790011674001'), '04'); // Private RUC
        $this->assertEquals($this->ecuadorIdentification->validateAllTypeIdentification('1760001550001'), '04'); // Public RUC
        $this->assertEquals($this->ecuadorIdentification->validateAllTypeIdentification('9999999999999'), '07'); // Final Customer
        $this->assertNull($this->ecuadorIdentification->validateAllTypeIdentification('9999999999998'));
    }

    /** @test */
    public function validate_is_juridical_person()
    {
        $this->assertNull($this->ecuadorIdentification->validateIsJuridicalPersons('1710034065'));
        $this->assertNull($this->ecuadorIdentification->validateIsJuridicalPersons('9999999999999'));
        $this->assertNull($this->ecuadorIdentification->validateIsJuridicalPersons('1710034065001'));

        $this->assertEquals($this->ecuadorIdentification->validateIsJuridicalPersons('1790011674001'), '04');
        $this->assertEquals($this->ecuadorIdentification->validateIsJuridicalPersons('1760001550001'), '04');
    }

    /** @test */
    public function validate_is_natural_person()
    {
        $this->assertNull($this->ecuadorIdentification->validateIsNaturalPersons('1790011674001'));
        $this->assertNull($this->ecuadorIdentification->validateIsNaturalPersons('1760001550001'));
        $this->assertNull($this->ecuadorIdentification->validateIsNaturalPersons('9999999999999'));

        $this->assertEquals($this->ecuadorIdentification->validateIsNaturalPersons('1710034065'), '05');
        $this->assertEquals($this->ecuadorIdentification->validateIsNaturalPersons('1710034065001'), '04');
    }

    /** @test */
    public function validate_rucs()
    {
        $this->assertNull($this->ecuadorIdentification->validateRuc('1710034065'));
        $this->assertNull($this->ecuadorIdentification->validateRuc('1710034065002'));
        $this->assertNull($this->ecuadorIdentification->validateRuc('9999999999999'));
        $this->assertNull($this->ecuadorIdentification->validateRuc('1760001520001'));
        $this->assertNull($this->ecuadorIdentification->validateRuc('1790011274001'));

        $this->assertEquals($this->ecuadorIdentification->validateRuc('1710034065001'), '04'); // Natural
        $this->assertEquals($this->ecuadorIdentification->validateRuc('1760001550001'), '04'); // Public
        $this->assertEquals($this->ecuadorIdentification->validateRuc('1790011674001'), '04'); // Private
    }

    /** @test */
    public function validate_integration_with_validator_laravel_and_response_error_message()
    {
        $data = [
            'identification' => '1710034065002'
        ];

        $rules = [
            'identification' => 'ecuador:natural_ruc'
        ];

        $validator = $this->app['validator']->make($data, $rules);

        $this->assertEquals($validator->getMessageBag()->get('identification')[0],
            'The identification field does not have the corresponding country format. (Ecuador)');
    }
}
