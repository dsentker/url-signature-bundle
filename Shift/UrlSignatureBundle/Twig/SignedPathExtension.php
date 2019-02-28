<?php

namespace Shift\UrlSignatureBundle\Twig;

use Symfony\Bridge\Twig\Extension\RoutingExtension;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\TwigFunction;
use UrlSignature\Builder;

class SignedPathExtension extends RoutingExtension
{

    /** @var Builder */
    private $builder;

    public function __construct(UrlGeneratorInterface $generator, Builder $builder)
    {
        $this->builder = $builder;
        parent::__construct($generator);
    }


    public function getFunctions(): array
    {
        return [
            new TwigFunction('signed_url', [$this, 'getUrlWithSignature']),

            // provides a function similar to symfony path() function although path() functions are not usable for the
            // hash comparison
            new TwigFunction('signed_path', [$this, 'getUrlWithSignature']),
        ];
    }

    public function getUrlWithSignature($name, $parameters = [], $expire = null)
    {
        $url = parent::getUrl($name, $parameters, false);
        return $this->builder->signUrl($url, $expire);
    }

    /**
     * @deprecated A path without host is not useful for signature hash_equals. Use methods which references
     *             parent::getUrl() instead.
     *
     * @param string $name
     * @param array  $parameters
     * @param bool   $relative
     *
     * @return string
     * @throws \UrlSignature\Exception\TimeoutException
     */
    public function getPathWithSignature($name, $parameters = [], $relative = false)
    {
        $url = parent::getPath($name, $parameters, $relative);
        return $this->builder->signUrl($url);
    }

}