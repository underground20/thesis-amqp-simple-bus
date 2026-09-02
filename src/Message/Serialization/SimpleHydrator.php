<?php

declare(strict_types=1);

namespace SimpleBus\Message\Serialization;

/** @internal */
final class SimpleHydrator
{
    /**
     * @throws \ReflectionException
     * @template T of object
     * @param array<string, mixed> $data
     * @param class-string<T> $class
     * @return T
     */
    public static function hydrate(array $data, string $class): object
    {
        $reflection = new \ReflectionClass($class);
        $constructor = $reflection->getConstructor();
        if ($constructor === null) {
            throw new \ReflectionException("Class $class does not has constructor");
        }

        $args = [];
        foreach ($constructor->getParameters() as $parameter) {
            $name = $parameter->getName();
            if (array_key_exists($name, $data)) {
                $value = $data[$name];
            } elseif ($parameter->isDefaultValueAvailable()) {
                $value = $parameter->getDefaultValue();
            } elseif ($parameter->allowsNull()) {
                $value = null;
            } else {
                throw new \InvalidArgumentException('Missing required parameter: ' . $parameter->getName());
            }

            $args[] = $value;
        }

        return $reflection->newInstanceArgs($args);
    }
}
