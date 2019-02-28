<?php
/**
 * Created by PhpStorm.
 * User: Daniel
 * Date: 27.02.2019
 * Time: 16:53
 */

namespace Shift\UrlSignatureBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;

class UrlSignatureExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config/packages')
        );
        //$loader->load('services.yaml');
        $loader->load('shift_url_signature.yaml');
    }


}