<?php

namespace FactoryGirl\Tests\Provider\Doctrine\Fixtures;

use FactoryGirl\Provider\Doctrine\FieldDef;

class SequenceTest extends TestCase
{
    /**
     * @test
     */
    public function sequenceGeneratorCallsAFunctionWithAnIncrementingArgument()
    {
        $this->factory->defineEntity(TestEntity\SpaceShip::class, array(
            'name' => FieldDef::sequence(function ($n) {
                return "Alpha $n";
            })
        ));
        $this->assertEquals('Alpha 1', $this->factory->get(TestEntity\SpaceShip::class)->getName());
        $this->assertEquals('Alpha 2', $this->factory->get(TestEntity\SpaceShip::class)->getName());
        $this->assertEquals('Alpha 3', $this->factory->get(TestEntity\SpaceShip::class)->getName());
        $this->assertEquals('Alpha 4', $this->factory->get(TestEntity\SpaceShip::class)->getName());
    }
    
    /**
     * @test
     */
    public function sequenceGeneratorCanTakeAPlaceholderString()
    {
        $this->factory->defineEntity(TestEntity\SpaceShip::class, array(
            'name' => FieldDef::sequence("Beta %d")
        ));
        $this->assertEquals('Beta 1', $this->factory->get(TestEntity\SpaceShip::class)->getName());
        $this->assertEquals('Beta 2', $this->factory->get(TestEntity\SpaceShip::class)->getName());
        $this->assertEquals('Beta 3', $this->factory->get(TestEntity\SpaceShip::class)->getName());
        $this->assertEquals('Beta 4', $this->factory->get(TestEntity\SpaceShip::class)->getName());
    }
    
    /**
     * @test
     */
    public function sequenceGeneratorCanTakeAStringToAppendTo()
    {
        $this->factory->defineEntity(TestEntity\SpaceShip::class, array(
            'name' => FieldDef::sequence("Gamma ")
        ));
        $this->assertEquals('Gamma 1', $this->factory->get(TestEntity\SpaceShip::class)->getName());
        $this->assertEquals('Gamma 2', $this->factory->get(TestEntity\SpaceShip::class)->getName());
        $this->assertEquals('Gamma 3', $this->factory->get(TestEntity\SpaceShip::class)->getName());
        $this->assertEquals('Gamma 4', $this->factory->get(TestEntity\SpaceShip::class)->getName());
    }
}
