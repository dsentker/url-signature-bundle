<?php

namespace Shift\UrlSignatureBundle\Controller;

use Shift\UrlSignatureBundle\Utils\RequestValidator;
use Shift\UrlSignatureBundle\Utils\UrlSignatureBuilder;

/**
 * This trait might help you for a simpler access to UrlSignatureBuilder and RequestValidator.
 * @deprecated This trait will be removed with version 2.0.0. Use a service injection instead.
 */
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

    public function getBuilder(): UrlSignatureBuilder
    {
        return $this->builder;
    }

    public function getValidator(): RequestValidator
    {
        return $this->validator;
    }
}
