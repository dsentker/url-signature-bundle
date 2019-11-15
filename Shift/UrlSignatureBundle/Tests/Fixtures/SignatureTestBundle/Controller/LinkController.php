<?php

namespace Tests\Fixtures\SignatureTestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class LinkController extends AbstractController
{
    /**
     * @Route("/test-link", name="test_link")
     */
    public function showSignatureLinkAction()
    {
        return $this->render('@UrlSignatureTest/signature-link.html.twig');
    }

    /**
     * @Route("/test-timeout-link", name="test_link_timeout")
     */
    public function showSignatureLinkWithTimeoutAction()
    {
        return $this->render('@UrlSignatureTest/signature-expiring-link.html.twig');
    }

    /**
     * @Route("/test-timeout-link", name="test_link_timeout_query")
     */
    public function showSignatureLinkWithTimeoutAndQueryAction()
    {
        return $this->render('@UrlSignatureTest/signature-expiring-link-with-query.html.twig');
    }
}
