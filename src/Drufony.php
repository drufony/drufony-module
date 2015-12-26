<?php

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * Static Service Container wrapper.
 */
class Drufony
{
    /**
     * The current system version.
     */
    const VERSION = VERSION;

    /**
     * Core API compatibility.
     */
    const CORE_COMPATIBILITY = DRUPAL_CORE_COMPATIBILITY;

    /**
     * Core minimum schema version.
     */
    const CORE_MINIMUM_SCHEMA_VERSION = 7000;

    /**
     * The currently active container object, or NULL if not initialized yet.
     *
     * @var ContainerInterface|null
     */
    protected static $container;

    /**
     * Sets a new global container.
     *
     * @param ContainerInterface $container
     *                                      A new container instance to replace the current.
     */
    public static function setContainer(ContainerInterface $container)
    {
        static::$container = $container;
    }

    /**
     * Unsets the global container.
     */
    public static function unsetContainer()
    {
        static::$container = null;
    }

    /**
     * Returns the currently active global container.
     *
     * @return ContainerInterface|null
     *
     * @throws RuntimeException
     */
    public static function getContainer()
    {
        if (static::$container === null) {
            throw new RuntimeException('\Drufony::$container is not initialized yet. \Drufony::setContainer() must be called with a real container.');
        }

        return static::$container;
    }

    /**
     * Returns TRUE if the container has been initialized, FALSE otherwise.
     *
     * @return bool
     */
    public static function hasContainer()
    {
        return static::$container !== null;
    }

    /**
     * Retrieves a service from the container.
     *
     * Use this method if the desired service is not one of those with a dedicated
     * accessor method below. If it is listed below, those methods are preferred
     * as they can return useful type hints.
     *
     * @param string $id
     *                   The ID of the service to retrieve.
     *
     * @return mixed
     *               The specified service.
     */
    public static function service($id)
    {
        return static::getContainer()->get($id);
    }

    /**
     * Indicates if a service is defined in the container.
     *
     * @param string $id
     *                   The ID of the service to check.
     *
     * @return bool
     *              TRUE if the specified service exists, FALSE otherwise.
     */
    public static function hasService($id)
    {
        // Check hasContainer() first in order to always return a Boolean.
        return static::hasContainer() && static::getContainer()->has($id);
    }
}
