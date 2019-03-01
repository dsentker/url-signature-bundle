<?php

namespace Shift\UrlSignatureBundle\Controller;

use Shift\UrlSignatureBundle\Utils\RequestValidator;
use Shift\UrlSignatureBundle\Utils\UrlSignatureBuilder;

trait UrlSignatureTrait
{
    /**
     * @var UrlSignatureBuilder
     */
    private $builder;

    /**
     * @var RequestValidator
     */
    private $validator;

    public function __construct(UrlSignatureBuilder $builder, RequestValidator $validator)
    {
        $this->builder = $builder;
        $this->validator = $validator;
    }

    /**
     * @return UrlSignatureBuilder
     */
    public function getBuilder(): UrlSignatureBuilder
    {
        return $this->builder;
    }

    /**
     * @return RequestValidator
     */
    public function getValidator(): RequestValidator
    {
        return $this->validator;
    }

}