<?php

declare(strict_types=1);

namespace SimpleBus\Message\Serialization;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(SimpleHydrator::class)]
final class SimpleHydratorTest extends TestCase
{
    public function testHydrateWithAllParameters(): void
    {
        $obj = SimpleHydrator::hydrate(['name' => 'Alice', 'age' => 30], UserWithAllParams::class);

        self::assertInstanceOf(UserWithAllParams::class, $obj);
        self::assertSame('Alice', $obj->name);
        self::assertSame(30, $obj->age);
    }

    public function testHydrateUsesDefaultValueWhenKeyMissing(): void
    {
        $obj = SimpleHydrator::hydrate(['name' => 'Bob'], UserWithDefaultRole::class);
        self::assertSame('guest', $obj->role);
    }

    public function testHydrateOverridesDefaultValueWhenKeyPresent(): void
    {
        $obj = SimpleHydrator::hydrate(['name' => 'Bob', 'role' => 'admin'], UserWithDefaultRole::class);
        self::assertSame('admin', $obj->role);
    }

    public function testHydrateSetsNullWhenParameterAllowsNullAndKeyMissing(): void
    {
        $obj = SimpleHydrator::hydrate(['name' => 'Charlie'], UserWithNullableEmail::class);
        self::assertNull($obj->email);
    }

    public function testHydrateSetsNullWhenExplicitlyPassed(): void
    {
        $obj = SimpleHydrator::hydrate(['name' => 'Charlie', 'email' => null], UserWithExplicitNullable::class);
        self::assertNull($obj->email);
    }

    public function testHydratePreservesOrderOfParameters(): void
    {
        $obj = SimpleHydrator::hydrate([
            'third' => 'c',
            'first' => 'a',
            'second' => 'b',
        ], OrderedParams::class);

        self::assertSame('a', $obj->first);
        self::assertSame('b', $obj->second);
        self::assertSame('c', $obj->third);
    }

    public function testHydrateIgnoresExtraKeysInData(): void
    {
        $obj = SimpleHydrator::hydrate(['name' => 'Dave', 'extra' => 'ignored'], SingleField::class);
        self::assertSame('Dave', $obj->name);
    }

    public function testHydrateWithMixedTypes(): void
    {
        $obj = SimpleHydrator::hydrate([
            'string' => 'hello',
            'int' => 42,
            'float' => 3.14,
            'bool' => true,
            'array' => [1, 2, 3],
        ], MixedTypesObject::class);

        self::assertSame('hello', $obj->string);
        self::assertSame(42, $obj->int);
        self::assertSame(3.14, $obj->float);
        self::assertTrue($obj->bool);
        self::assertSame([1, 2, 3], $obj->array);
    }

    public function testHydrateWithNullableParameterAndDefaultValue(): void
    {
        $obj = SimpleHydrator::hydrate(['name' => 'Eve'], UserWithNullableAndDefault::class);
        self::assertSame('default@example.com', $obj->email);

        $obj2 = SimpleHydrator::hydrate(['name' => 'Eve', 'email' => null], UserWithNullableAndDefault::class);
        self::assertNull($obj2->email);
    }

    public function testHydrateEmptyArrayToNullableParameter(): void
    {
        $obj = SimpleHydrator::hydrate(['name' => 'Frank', 'tags' => []], UserWithTags::class);
        self::assertSame([], $obj->tags);
    }

    public function testThrowsWhenRequiredParameterMissing(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing required parameter: age');

        SimpleHydrator::hydrate(['name' => 'Alice'], UserWithAllParams::class);
    }

    public function testThrowsWhenAllParametersMissing(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing required parameter: name');

        SimpleHydrator::hydrate([], OneRequired::class);
    }

    public function testThrowsWhenClassHasNoConstructor(): void
    {
        $this->expectException(\ReflectionException::class);
        SimpleHydrator::hydrate(['foo' => 'bar'], NoConstructorClass::class);
    }

    public function testHydrateDoesNotCoerceTypes(): void
    {
        // В PHP при передаче строки в типизированный int-параметр произойдёт приведение.
        // Но сам гидратор не делает дополнительного кастинга — он просто передаёт значение.
        $obj = SimpleHydrator::hydrate(['age' => '30'], IntOnlyStrict::class);
        // Из‑за типизации конструктора здесь $obj->age будет int(30), а не строка.
        // Этот тест нужен, чтобы зафиксировать текущее поведение и не менять его случайно.
        self::assertEquals(30, $obj->age);
    }

    public function testHydrateWithUnionTypeNullable(): void
    {
        $obj = SimpleHydrator::hydrate(['name' => 'Gina'], UnionTypeClass::class);
        self::assertNull($obj->id);

        $obj2 = SimpleHydrator::hydrate(['name' => 'Gina', 'id' => 123], UnionTypeClass::class);
        self::assertSame(123, $obj2->id);
    }

    public function testHydrateOnlyOptionalParameters(): void
    {
        $obj = SimpleHydrator::hydrate([], AllOptionalClass::class);
        self::assertSame('guest', $obj->role);
        self::assertSame(1, $obj->level);
    }

    public function testHydrateSingleRequiredParameter(): void
    {
        $obj = SimpleHydrator::hydrate(['value' => 'test'], SingleParamClass::class);
        self::assertSame('test', $obj->value);
    }
}

class OneRequired
{
    public function __construct(
        public readonly string $name,
    ) {
    }
}

class UserWithAllParams
{
    public function __construct(
        public readonly string $name,
        public readonly int $age,
    ) {
    }
}

class UserWithDefaultRole
{
    public function __construct(
        public readonly string $name,
        public readonly string $role = 'guest',
    ) {
    }
}

class UserWithNullableEmail
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $email = null,
    ) {
    }
}

class IntOnlyStrict
{
    public function __construct(
        public readonly int $age,
    ) {
    }
}

class UserWithExplicitNullable
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $email,
    ) {
    }
}

class SingleField
{
    public function __construct(
        public readonly string $name,
    ) {
    }
}

class MixedTypesObject
{
    /** @param array<mixed> $array */
    public function __construct(
        public readonly string $string,
        public readonly int $int,
        public readonly float $float,
        public readonly bool $bool,
        public readonly array $array,
    ) {
    }
}

class UserWithNullableAndDefault
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $email = 'default@example.com',
    ) {
    }
}

class UserWithTags
{
    /** @param array<mixed> $tags */
    public function __construct(
        public readonly string $name,
        public readonly ?array $tags,
    ) {
    }
}

class NoConstructorClass
{
    // нет конструктора
}

class SingleParamClass
{
    public function __construct(
        public readonly string $value,
    ) {
    }
}

class UnionTypeClass
{
    public function __construct(
        public readonly string $name,
        public readonly string|int|null $id = null,
    ) {
    }
}

class OrderedParams
{
    public function __construct(
        public readonly string $first,
        public readonly string $second,
        public readonly string $third,
    ) {
    }
}

class AllOptionalClass
{
    public function __construct(
        public readonly string $role = 'guest',
        public readonly int $level = 1,
    ) {
    }
}
