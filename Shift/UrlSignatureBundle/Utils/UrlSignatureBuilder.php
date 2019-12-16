<?php

namespace Shift\UrlSignatureBundle\Utils;

use DateTimeInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use UrlSignature\Builder;
use UrlSignature\Exception\TimeoutException;

class UrlSignatureBuilder
{

    /** @var Builder */
    private $builder;

    /** @var RouterInterface */
    private $router;

    public function __construct(Builder $builder, RouterInterface $router)
    {
        $this->builder = $builder;
        $this->router = $router;
    }

    /**
     * @param string $url
     * @param mixed  $timeout
     *
     * @return string
     * @throws TimeoutException
     */
    public function signUrl(string $url, $timeout = null): string
    {
        return $this->builder->signUrl($url, $timeout);
    }

    /**
     * @param string                            $name
     * @param array                             $parameters
     * @param null|int|DateTimeInterface|string $timeout
     *
     * @return string
     * @throws TimeoutException
     */
    public function signUrlFromPath($name, array $parameters = [], $timeout = null): string
    {
        $url = $this->router->generate($name, $parameters, UrlGeneratorInterface::ABSOLUTE_URL);
        return $this->signUrl($url, $timeout);
    }
}
