<?php

namespace Shift\UrlSignatureBundle\Utils;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use UrlSignature\Validator;

class RequestValidator
{
    /** @var RequestStack */
    private $requestStack;

    /** @var Validator */
    private $validator;

    /**
     * RequestValidator constructor.
     *
     * @param RequestStack $requestStack
     * @param Validator    $validator
     */
    public function __construct(RequestStack $requestStack, Validator $validator)
    {
        $this->requestStack = $requestStack;
        $this->validator = $validator;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        $request = $this->getRequest();
        if(empty($request)) {
            throw new \RuntimeException(sprintf('Can not validate the URL because the request does not exist.'));
        }
        return $this->validator->isValid($request->getUri());
    }

    /**
     * @return bool
     *
     * @throws \UrlSignature\Exception\SignatureExpiredException
     * @throws \UrlSignature\Exception\SignatureInvalidException
     * @throws \UrlSignature\Exception\SignatureNotFoundException
     */
    public function verify()
    {
        $request = $this->getRequest();
        if(empty($request)) {
            throw new \RuntimeException(sprintf('Can not validate the URL because the request does not exist.'));
        }
        return $this->validator->verify($request->getUri());
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Request|null
     */
    public function getRequest() : ?Request
    {
        return $this->requestStack->getCurrentRequest();
    }

    /**
     * @return Validator
     */
    public function getValidator(): Validator
    {
        return $this->validator;
    }
 }