<?php


namespace Landesadel\easyBlog\UnitTests\Container;


use Landesadel\easyBlog\Container\DIContainer;
use Landesadel\easyBlog\Exceptions\NotFoundException;
use Landesadel\easyBlog\Repositories\User\InMemoryUsersRepository;
use Landesadel\easyBlog\Repositories\User\UsersRepositoryInterface;
use PHPUnit\Framework\TestCase;

class DIContainerTests extends TestCase
{

    /**
     * @throws NotFoundException
     */
    public function testItResolvesClassByContract(): void
    {
        $container = new DIContainer();

        $container->bind(
            UsersRepositoryInterface::class,
            InMemoryUsersRepository::class
        );

        $object = $container->get(UsersRepositoryInterface::class);
        $this->assertInstanceOf(
            InMemoryUsersRepository::class,
            $object
        );


    }
    /**
     * @throws NotFoundException
     */
    public function testItResolvesClassWithoutDependencies(): void
    {
        $container = new DIContainer();

        $object = $container->get(SomeClassWithoutDependencies::class);

        $this->assertInstanceOf(
            SomeClassWithoutDependencies::class,
            $object
        );
    }

    public function testItThrowsAnExceptionIfCannotResolveType(): void
    {

        $container = new DIContainer();
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage(
            'Cannot resolve type: Landesadel\easyBlog\UnitTests\Container\SomeClass'
        );

        $container->get(SomeClass::class);
    }

    /**
     * @throws NotFoundException
     */
    public function testItReturnsPredefinedObject(): void
    {
        $container = new DIContainer();
        $container->bind(
            SomeClassWithParameter::class,
            new SomeClassWithParameter(42)
        );

        $object = $container->get(SomeClassWithParameter::class);
        $this->assertInstanceOf(
            SomeClassWithParameter::class,
            $object
        );

        $this->assertSame(42, $object->value());
    }

    public function testItResolvesClassWithDependencies(): void
    {
        $container = new DIContainer();
        $container->bind(
            SomeClassWithParameter::class,
            new SomeClassWithParameter(42)
        );
        $object = $container->get(ClassDependingOnAnother::class);
        $this->assertInstanceOf(
            ClassDependingOnAnother::class,
            $object
        );
    }

}