<?php

namespace Tests\Fixtures\SignatureTestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Shift\UrlSignatureBundle\Annotation\RequiresSignatureVerification;

class UrlSignatureController extends AbstractController
{
    /**
     * @RequiresSignatureVerification
     * @Route("/test-annotation", name="test_annotation")
     */
    public function testAnnotationAction()
    {
        return $this->render('@UrlSignatureTest/view.html.twig');
    }

    /**
     * @RequiresSignatureVerification
     * @Route("/test-annotation-param/{foo}", name="test_annotation_param")
     */
    public function testAnnotationWithParamAction($foo)
    {
        return $this->render('@UrlSignatureTest/view.html.twig', [
            'baz' => $foo
        ]);
    }
}
