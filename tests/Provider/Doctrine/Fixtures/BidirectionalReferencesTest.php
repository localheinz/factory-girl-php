<?php
namespace FactoryGirl\Tests\Provider\Doctrine\Fixtures;

use FactoryGirl\Provider\Doctrine\FieldDef;

class BidirectionalReferencesTest extends TestCase
{
    /**
     * @test
     */
    public function bidirectionalOntToManyReferencesAreAssignedBothWays()
    {
        $this->factory->defineEntity(TestEntity\SpaceShip::class);
        $this->factory->defineEntity(TestEntity\Person::class, array(
            'spaceShip' => FieldDef::reference(TestEntity\SpaceShip::class)
        ));
        
        $person = $this->factory->get(TestEntity\Person::class);
        $ship = $person->getSpaceShip();
        
        $this->assertTrue($ship->getCrew()->contains($person));
    }
    
    /**
     * @test
     */
    public function unidirectionalReferencesWorkAsUsual()
    {
        $this->factory->defineEntity(TestEntity\Badge::class, array(
            'owner' => FieldDef::reference(TestEntity\Person::class)
        ));
        $this->factory->defineEntity(TestEntity\Person::class);
        
        $this->assertTrue($this->factory->get(TestEntity\Badge::class)->getOwner() instanceof TestEntity\Person);
    }
    
    /**
     * @test
     */
    public function whenTheOneSideIsASingletonItMayGetSeveralChildObjects()
    {
        $this->factory->defineEntity(TestEntity\SpaceShip::class);
        $this->factory->defineEntity(TestEntity\Person::class, array(
            'spaceShip' => FieldDef::reference(TestEntity\SpaceShip::class)
        ));
        
        $ship = $this->factory->getAsSingleton(TestEntity\SpaceShip::class);
        $p1 = $this->factory->get(TestEntity\Person::class);
        $p2 = $this->factory->get(TestEntity\Person::class);
        
        $this->assertTrue($ship->getCrew()->contains($p1));
        $this->assertTrue($ship->getCrew()->contains($p2));
    }
}
