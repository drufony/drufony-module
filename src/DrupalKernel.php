<?php

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

class DrupalKernel extends Kernel
{
    use Bangpound\Kernel\YamlEnvironmentTrait;

    /**
     * Returns an array of bundles to register.
     *
     * @return \Symfony\Component\HttpKernel\Bundle\BundleInterface[] An array of bundle instances.
     */
    public function registerBundles()
    {
        $bundles = [
        ];

        return $bundles;
    }

    /**
     * {@inheritdoc}
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(function (ContainerBuilder $container) use ($loader) {
            $container->addExpressionLanguageProvider(new ExpressionLanguageProvider());

            $results = db_query('SELECT filename FROM {system} WHERE status = 1 ORDER BY weight ASC, name ASC')->fetchCol();
            foreach ($results as $result) {
                $path = dirname(DRUPAL_ROOT.DIRECTORY_SEPARATOR.$result);
                if (file_exists($path.DIRECTORY_SEPARATOR.'services.yml')) {
                    $loader->load($path.DIRECTORY_SEPARATOR.'services.yml');
                }
            }

            $container->addObjectResource($this);
        });
    }

    public function getName()
    {
        if (null === $this->name) {
            $this->name = 'Drupal'.ucfirst(basename(conf_path()));
        }

        return $this->name;
    }

    public function getCacheDir()
    {
        return DRUPAL_ROOT.'/../var/'.basename(conf_path()).'/cache/'.$this->getEnvironment();
    }

    public function getLogDir()
    {
        return DRUPAL_ROOT.'/../var/'.basename(conf_path()).'/logs';
    }
}
