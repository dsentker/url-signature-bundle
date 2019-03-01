<?php

namespace Shift\UrlSignatureBundle\Utils;


use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use UrlSignature\Builder;

class UrlSignatureBuilder
{

    /** @var Builder */
    private $builder;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * UrlSignatureBuilder constructor.
     *
     * @param Builder         $builder
     * @param RouterInterface $router
     */
    public function __construct(Builder $builder, RouterInterface $router)
    {
        $this->builder = $builder;
        $this->router = $router;
    }

    /**
     * @param string                             $url
     * @param null|int|\DateTimeInterface|string $timeout
     *
     * @return string
     * @throws \UrlSignature\Exception\TimeoutException
     */
    public function signUrl(string $url, $timeout = null)
    {
        return $this->builder->signUrl($url, $timeout);
    }

    /**
     * @param string                             $name
     * @param array                              $parameters
     * @param null|int|\DateTimeInterface|string $timeout
     *
     * @return string
     * @throws \UrlSignature\Exception\TimeoutException
     */
    public function signUrlFromPath($name, $parameters = [], $timeout = null)
    {
        $url = $this->router->generate($name, $parameters, UrlGeneratorInterface::ABSOLUTE_URL);
        return $this->signUrl($url, $timeout);
    }

}