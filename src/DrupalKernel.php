<?php

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

class DrupalKernel extends Kernel
{
    use Bangpound\Kernel\YamlEnvironmentTrait;

    /**
     * List of discovered services.yml pathnames.
     *
     * This is a nested array whose top-level keys are 'app' and 'site', denoting
     * the origin of a service provider. Site-specific providers have to be
     * collected separately, because they need to be processed last, so as to be
     * able to override services from application service providers.
     *
     * @var array
     */
    protected $serviceYamls;

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
            $this->initializeServiceProviders();

            foreach ($this->serviceYamls['app'] as $filename) {
                $loader->load($filename);
            }
            // Register site-specific service overrides.
            foreach ($this->serviceYamls['site'] as $filename) {
                $loader->load($filename);
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
        require_once DRUPAL_ROOT . '/includes/file.inc';

        return file_directory_temp() . '/' . $this->getName() . '/cache/' . $this->getEnvironment();
    }

    public function getLogDir()
    {
        require_once DRUPAL_ROOT . '/includes/file.inc';

        return file_directory_temp() . '/' . $this->getName() . '/logs';
    }

    /**
     * Returns the kernel parameters.
     *
     * @return array An array of kernel parameters
     */
    protected function getKernelParameters()
    {
        $parameters = parent::getKernelParameters();
        $parameters['kernel.drupal_root'] = DRUPAL_ROOT;
        $parameters['kernel.conf_path'] = conf_path();
        $parameters['kernel.conf_dir'] = basename(conf_path());

        return $parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function discoverServiceProviders()
    {
        $this->serviceYamls = array(
          'app' => array(),
          'site' => array(),
        );

        // Retrieve enabled modules.
        $module_filenames = $this->getModuleFileNames();

        // Load each module's services configuration.
        foreach ($module_filenames as $module => $filename) {
            $filename = dirname($filename)."/$module.services.yml";
            if (file_exists($filename)) {
                $this->serviceYamls['app'][$module] = $filename;
            }
        }

        if (!empty($GLOBALS['conf']['container_yamls'])) {
            $this->addServiceFiles($GLOBALS['conf']['container_yamls']);
        }
    }

    /**
     * Registers all service providers to the kernel.
     *
     * @throws \LogicException
     */
    protected function initializeServiceProviders()
    {
        $this->discoverServiceProviders();
    }

    /**
     * Gets the file name for each enabled module.
     *
     * @return array
     *               Array where each key is a module name, and each value is a path to the
     *               respective *.module or *.profile file.
     */
    protected function getModuleFileNames()
    {
        $results = db_query('SELECT name, filename FROM {system} WHERE status = 1 ORDER BY weight ASC, name ASC')->fetchAllAssoc('name');

        return array_map(function ($value) {
            return DRUPAL_ROOT.DIRECTORY_SEPARATOR.$value->filename;
        }, $results);
    }

    /**
     * Add service files.
     *
     * @param string[] $service_yamls
     *                                A list of service files.
     */
    protected function addServiceFiles(array $service_yamls)
    {
        $this->serviceYamls['site'] = array_filter($service_yamls, 'file_exists');
    }
}
