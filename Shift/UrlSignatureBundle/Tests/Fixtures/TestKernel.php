<?php

namespace Tests\Fixtures;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Used for functional tests.
 */
class TestKernel extends Kernel
{
    public function registerBundles()
    {
        return [
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new \Symfony\Bundle\TwigBundle\TwigBundle(),
            new \Shift\UrlSignatureBundle\ShiftUrlSignatureBundle(),
            new \Tests\Fixtures\SignatureTestBundle\SignatureTestBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/config/config.yml');
        $loader->load(function (ContainerBuilder $container) {
            // Register a NullLogger to avoid getting the stderr default logger of FrameworkBundle
            $container->register('logger', 'Psr\Log\NullLogger');
        });

    }

    public function getCacheDir()
    {
        // Symfony, Y U deprecate $this->rootDir ??
        // https://github.com/symfony/symfony/issues/29110
        return $this->getProjectDir() . '/Shift/UrlSignatureBundle/Tests/Fixtures/cache/' . $this->environment;
    }

    public function getLogDir()
    {
        return $this->getCacheDir();
    }


}

class_alias('Tests\Fixtures\TestKernel', 'TestKernel');
