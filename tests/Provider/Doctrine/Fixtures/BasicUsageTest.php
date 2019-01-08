<?php
namespace FactoryGirl\Tests\Provider\Doctrine\Fixtures;

use Doctrine\Common\Collections\ArrayCollection;
use FactoryGirl\Provider\Doctrine\FieldDef;

class BasicUsageTest extends TestCase
{
    /**
     * @test
     */
    public function acceptsConstantValuesInEntityDefinitions()
    {
        $ss = $this->factory
            ->defineEntity(TestEntity\SpaceShip::class, array(
                'name' => 'My BattleCruiser'
            ))
            ->get(TestEntity\SpaceShip::class);
        
        $this->assertEquals('My BattleCruiser', $ss->getName());
    }
    
    /**
     * @test
     */
    public function acceptsGeneratorFunctionsInEntityDefinitions()
    {
        $name = "Star";
        $this->factory->defineEntity(TestEntity\SpaceShip::class, array(
            'name' => function () use (&$name) {
                return "M/S $name";
            }
        ));
        
        $this->assertEquals('M/S Star', $this->factory->get(TestEntity\SpaceShip::class)->getName());
        $name = "Superstar";
        $this->assertEquals('M/S Superstar', $this->factory->get(TestEntity\SpaceShip::class)->getName());
    }
    
    /**
     * @test
     */
    public function valuesCanBeOverriddenAtCreationTime()
    {
        $ss = $this->factory
            ->defineEntity(TestEntity\SpaceShip::class, array(
                'name' => 'My BattleCruiser'
            ))
            ->get(TestEntity\SpaceShip::class, array('name' => 'My CattleBruiser'));
        $this->assertEquals('My CattleBruiser', $ss->getName());
    }

    /**
     * @test
     */
    public function preservesDefaultValuesOfEntity()
    {
        $ss = $this->factory
            ->defineEntity(TestEntity\SpaceStation::class)
            ->get(TestEntity\SpaceStation::class);
        $this->assertEquals('Babylon5', $ss->getName());
    }
    
    /**
     * @test
     */
    public function doesNotCallTheConstructorOfTheEntity()
    {
        $ss = $this->factory
            ->defineEntity(TestEntity\SpaceShip::class, array())
            ->get(TestEntity\SpaceShip::class);
        $this->assertFalse($ss->constructorWasCalled());
    }
    
    /**
     * @test
     */
    public function instantiatesCollectionAssociationsToBeEmptyCollectionsWhenUnspecified()
    {
        $ss = $this->factory
            ->defineEntity(TestEntity\SpaceShip::class, array(
                'name' => 'Battlestar Galaxy'
            ))
            ->get(TestEntity\SpaceShip::class);
        
        $this->assertTrue($ss->getCrew() instanceof ArrayCollection);
        $this->assertTrue($ss->getCrew()->isEmpty());
    }

    /**
     * @test
     */
    public function arrayElementsAreMappedToCollectionAsscociationFields()
    {
        $this->factory->defineEntity(TestEntity\SpaceShip::class);
        $this->factory->defineEntity(TestEntity\Person::class, array(
            'spaceShip' => FieldDef::reference(TestEntity\SpaceShip::class)
        ));

        $p1 = $this->factory->get(TestEntity\Person::class);
        $p2 = $this->factory->get(TestEntity\Person::class);

        $ship = $this->factory->get(TestEntity\SpaceShip::class, array(
            'name' => 'Battlestar Galaxy',
            'crew' => array($p1, $p2)
        ));
        
        $this->assertTrue($ship->getCrew() instanceof ArrayCollection);
        $this->assertTrue($ship->getCrew()->contains($p1));
        $this->assertTrue($ship->getCrew()->contains($p2));
    }
    
    /**
     * @test
     */
    public function unspecifiedFieldsAreLeftNull()
    {
        $this->factory->defineEntity(TestEntity\SpaceShip::class);
        $this->assertNull($this->factory->get(TestEntity\SpaceShip::class)->getName());
    }

    /**
     * @test
     */
    public function entityIsDefinedToDefaultNamespace()
    {
        $this->factory->defineEntity(TestEntity\SpaceShip::class);
        $this->factory->defineEntity(TestEntity\Person\User::class);

        $this->assertEquals(
            'FactoryGirl\Tests\Provider\Doctrine\Fixtures\TestEntity\SpaceShip',
            get_class($this->factory->get(TestEntity\SpaceShip::class))
        );

        $this->assertEquals(
            'FactoryGirl\Tests\Provider\Doctrine\Fixtures\TestEntity\Person\User',
            get_class($this->factory->get(TestEntity\Person\User::class))
        );
    }

    /**
     * @test
     */
    public function entityCanBeDefinedToAnotherNamespace()
    {
        $this->factory->defineEntity(
            '\FactoryGirl\Tests\Provider\Doctrine\Fixtures\TestAnotherEntity\Artist'
        );

        $this->assertEquals(
            'FactoryGirl\Tests\Provider\Doctrine\Fixtures\TestAnotherEntity\Artist',
            get_class($this->factory->get(
                '\FactoryGirl\Tests\Provider\Doctrine\Fixtures\TestAnotherEntity\Artist'
            ))
        );
    }

    /**
     * @test
     */
    public function returnsListOfEntities()
    {
        $this->factory->defineEntity(TestEntity\SpaceShip::class);

        $this->assertCount(1, $this->factory->getList(TestEntity\SpaceShip::class));
    }

    /**
     * @test
     */
    public function canSpecifyNumberOfReturnedInstances()
    {
        $this->factory->defineEntity(TestEntity\SpaceShip::class);

        $this->assertCount(5, $this->factory->getList(TestEntity\SpaceShip::class, array(), 5));
    }
}
