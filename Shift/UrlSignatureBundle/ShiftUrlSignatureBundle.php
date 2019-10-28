<?php

namespace Shift\UrlSignatureBundle;

use Shift\UrlSignatureBundle\DependencyInjection\UrlSignatureExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ShiftUrlSignatureBundle extends Bundle
{
    public function getContainerExtension(): UrlSignatureExtension
    {
        return new UrlSignatureExtension();
    }
}
