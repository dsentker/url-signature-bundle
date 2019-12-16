<?php

namespace Shift\UrlSignatureBundle\Twig;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use UrlSignature\Builder;
use UrlSignature\Exception\TimeoutException;

final class SignedPathExtension extends AbstractExtension
{

    /** @var Builder */
    private $builder;

    /** @var UrlGeneratorInterface */
    private $generator;

    public function __construct(UrlGeneratorInterface $generator, Builder $builder)
    {
        $this->builder = $builder;
        $this->generator = $generator;
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

    /**
     * @param string $name       The route name
     * @param array  $parameters the parameters for the URL generation
     * @param mixed  $expire     A DateTime-parsable string, a timestamp or a DateTimeInterface instance.
     *
     * @return string
     * @throws TimeoutException
     */
    public function getUrlWithSignature(string $name, array $parameters = [], $expire = null): string
    {
        $url = $this->generator->generate($name, $parameters, UrlGeneratorInterface::ABSOLUTE_URL);
        return $this->builder->signUrl($url, $expire);
    }
}
