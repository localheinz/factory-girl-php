<?php

namespace FactoryGirl\Tests\Provider\Doctrine\Fixtures;

use FactoryGirl\Provider\Doctrine\FieldDef;
use FactoryGirl\Tests\Provider\Doctrine\Fixtures\TestEntity;

class ReferencesTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->factory->defineEntity(TestEntity\SpaceShip::class, array(
            'crew' => FieldDef::references(TestEntity\Person::class)
        ));

        $this->factory->defineEntity(TestEntity\Person::class, array(
            'name' => 'Eve',
        ));
    }

    /**
     * @test
     */
    public function referencedObjectsShouldBeCreatedAutomatically()
    {
        /** @var TestEntity\SpaceShip $spaceShip */
        $spaceShip = $this->factory->get(TestEntity\SpaceShip::class);

        $crew = $spaceShip->getCrew();

        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $crew);
        $this->assertContainsOnly('FactoryGirl\Tests\Provider\Doctrine\Fixtures\TestEntity\Person', $crew);
        $this->assertCount(1, $crew);
    }

    /**
     * @test
     */
    public function referencedObjectsShouldBeOverrideable()
    {
        $count = 5;

        /** @var TestEntity\SpaceShip $spaceShip */
        $spaceShip = $this->factory->get(TestEntity\SpaceShip::class, array(
            'crew' => $this->factory->getList(TestEntity\Person::class, array(), $count),
        ));

        $crew = $spaceShip->getCrew();

        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $crew);
        $this->assertContainsOnly('FactoryGirl\Tests\Provider\Doctrine\Fixtures\TestEntity\Person', $crew);
        $this->assertCount($count, $crew);
    }

    /**
     * @test
     */
    public function referencedObjectsShouldBeNullable()
    {
        /** @var TestEntity\SpaceShip $spaceShip */
        $spaceShip = $this->factory->get(TestEntity\SpaceShip::class, array(
            'crew' => null,
        ));

        $crew = $spaceShip->getCrew();

        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $crew);
        $this->assertEmpty($crew);
    }

    /**
     * @test
     */
    public function referencedObjectsCanBeSingletons()
    {
        /** @var TestEntity\Person $person*/
        $person = $this->factory->getAsSingleton(TestEntity\Person::class);

        /** @var TestEntity\SpaceShip $spaceShip */
        $spaceShip = $this->factory->get(TestEntity\SpaceShip::class);

        $crew = $spaceShip->getCrew();

        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $crew);
        $this->assertContains($person, $crew);
        $this->assertCount(1, $crew);
    }
}
