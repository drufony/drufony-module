<?php

namespace Drufony;

use Drufony;
use Drupal\drufony_monolog\MonologServiceProvider;
use Drupal\electronicintifada\ElectronicIntifadaServiceProvider;
use Pimple\Container;
use Pimple\Psr11;

/**
 * Boots the Symfony kernel and sets static container.
 */
function boot()
{
    if (!class_exists('Drufony', false)) {
        class_alias('Drupal\\drufony\\Drufony', 'Drufony');
    }

    $c = new Container([
        'env' => $_SERVER['APP_ENV'] ?? get_environment(),
        'debug' => $_SERVER['APP_DEBUG'] ?? get_debug(),
        'kernel.logs_dir' => DRUPAL_ROOT .'/../var/logs',
    ]);
    $c->register(new MonologServiceProvider(), [
        'monolog.logfile' => 'php://stderr',
    ]);
    $c->register(new Drufony\Bridge\Pimple\TwigServiceProvider(), [
        'twig.options' => ['autoescape' => false],
    ]);
    $c->register(new ElectronicIntifadaServiceProvider());

    $container = new Psr11\Container($c);

    Drufony::setContainer($container);
}

/**
 * Kernel environment is derived from Acquia environment with 'dev' as default.
 *
 * @return string
 */
function get_environment($varname = 'AH_SITE_ENVIRONMENT', $default = 'dev')
{
    return getenv($varname) ? 'prod' : $default;
}

/**
 * Kernel debug is true unless this is the test or prod environments.
 *
 * @return bool
 */
function get_debug($varname = 'AH_SITE_ENVIRONMENT', $default = true)
{
    return getenv($varname) === 'test' || getenv($varname) === 'prod' ? false : $default;
}
