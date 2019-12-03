<?php

namespace Shift\UrlSignatureBundle\Utils;

use RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use UrlSignature\Exception\SignatureExpiredException;
use UrlSignature\Exception\SignatureInvalidException;
use UrlSignature\Exception\SignatureNotFoundException;
use UrlSignature\Validator;

class RequestValidator
{
    /** @var RequestStack */
    private $requestStack;

    /** @var Validator */
    private $validator;

    public function __construct(RequestStack $requestStack, Validator $validator)
    {
        $this->requestStack = $requestStack;
        $this->validator = $validator;
    }

    public function isValid(): bool
    {
        $request = $this->getRequest();
        if (empty($request)) {
            throw new RuntimeException(sprintf('Can not validate the URL because the request does not exist.'));
        }
        return $this->validator->isValid($request->getUri());
    }

    /**
     * @return bool
     *
     * @throws SignatureExpiredException
     * @throws SignatureInvalidException
     * @throws SignatureNotFoundException
     */
    public function verify(): bool
    {
        $request = $this->getRequest();
        if (empty($request)) {
            throw new RuntimeException(sprintf('Can not validate the URL because the request does not exist.'));
        }
        return $this->validator->verify($request->getUri());
    }

    public function getRequest(): ?Request
    {
        return $this->requestStack->getCurrentRequest();
    }

    public function getValidator(): Validator
    {
        return $this->validator;
    }
}
