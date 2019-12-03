<?php

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use UrlSignature\Builder;

class BuilderTest extends WebTestCase
{
    private $actionUrl;

    /** @var Builder $builder */
    private $builder;

    /** @var KernelBrowser */
    private $client;

    protected function setUp()
    {

        $this->client = self::createClient();
        $this->builder = $this->client->getKernel()->getContainer()->get('shift_url_signature.builder');
        $this->actionUrl = $this->client->getKernel()->getContainer()->get('router')->generate(
            'test_annotation',
            [],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $this->bootKernel();
        parent::setUp();
    }


    public function testThrowExceptionOnMissingSignature()
    {
        #$this->expectException(AccessDeniedHttpException::class);
        #$client = self::createClient();
        #$this->bootKernel();
        $this->client->request('GET', $this->actionUrl);
        #$crawler = $client->request('GET', '/test-annotation');
        #$this->assertEquals($checkHtml, $crawler->filterXPath('//body')->html());
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testThrowExceptionOnInvalidSignature()
    {
        //$signatureQueryKey = $this->client->getKernel()->getContainer()->getParameter('shift_url_signature.query_signature_name');
        $signatureQueryKey = '_signature';
        $route = sprintf('/test-annotation?%s=invalid-signature-hash', $signatureQueryKey);
        $this->client->request('GET', $route);
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testControllerActionIsCalledOnValidSignature()
    {
        $signatureUrl = $this->builder->signUrl($this->actionUrl);
        $this->client->request('GET', $signatureUrl);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }
}
