<?php

namespace Shift\UrlSignatureBundle\EventListener;

use Shift\UrlSignatureBundle\Annotation\RequiresSignatureVerification;
use Doctrine\Common\Annotations\Reader;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use UrlSignature\Exception\SignatureExpiredException;
use UrlSignature\Exception\SignatureInvalidException;
use UrlSignature\Exception\SignatureNotFoundException;
use UrlSignature\Validator;

class ControllerCheckRequestListener
{
    /** @var Reader */
    private $reader;

    /** @var Validator */
    private $validator;

    public function __construct(Reader $reader, Validator $validator)
    {
        $this->reader = $reader;
        $this->validator = $validator;
    }

    /**
     * {@inheritdoc}
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        if (!is_array($controllerData = $event->getController())) {
            return;
        }

        list($controller, $methodName) = $controllerData;

        $reflectionMethod = (new \ReflectionObject($controller))->getMethod($methodName);
        $methodAnnotation = $this->reader->getMethodAnnotation($reflectionMethod, RequiresSignatureVerification::class);

        if (!$methodAnnotation) {
            return;
        }

        try {
            $this->validator->verify($event->getRequest()->getUri());
        } catch (SignatureExpiredException $e) {
            throw new AccessDeniedHttpException('The requested URL has expired.');
        } catch (SignatureInvalidException $e) {
            throw new AccessDeniedHttpException('The signature you provided is invalid!');
        } catch (SignatureNotFoundException $e) {
            throw new AccessDeniedHttpException('This URL requires a signature, but was not found.');
        } catch (\Exception $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
    }
}
