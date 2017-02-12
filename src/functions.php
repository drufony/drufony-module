<?php

namespace Drufony;

use Drufony;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Boots the Symfony kernel and sets static container.
 */
function boot()
{
    if (!class_exists('Drufony', false)) {
        class_alias('Drupal\\drufony\\Drufony', 'Drufony');
    }

    $class = variable_get('drufony_kernel_class', 'Drupal\\drufony\\DrupalKernel');

    /** @var KernelInterface $kernel */
    $kernel = new $class(get_environment(), get_debug());
    $kernel->boot();
    $container = $kernel->getContainer();
    Drufony::setContainer($container);
}

/**
 * Kernel environment is derived from Acquia environment with 'dev' as default.
 *
 * @return string
 */
function get_environment($varname = 'AH_SITE_ENVIRONMENT', $default = 'local')
{
    return getenv($varname) ?: $default;
}

/**
 * Kernel debug is true unless this is the production environment.
 *
 * @return boolean
 */
function get_debug($varname = 'AH_PRODUCTION', $default = true)
{
    return getenv($varname) ? false : $default;
}
