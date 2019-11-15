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

            // provides a function similar to symfony's path() extension although path() function are not usable for
            // the hash comparison, because it does not generate an absolute URL.
            new TwigFunction('signed_path', [$this, 'getUrlWithSignature']),
        ];
    }

    public function getUrlWithSignature($name, $parameters = [], $expire = null): string
    {
        $url = parent::getUrl($name, $parameters, false);
        $url = $this->builder->signUrl($url, $expire);
        return $url;
    }
}
