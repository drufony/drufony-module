<?php

namespace Drufony;

use Drufony as StaticHelper;
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
    $kernel = new $class('prod', false);
    $kernel->boot();
    $container = $kernel->getContainer();
    StaticHelper::setContainer($container);
}
