<?php

use Symfony\Bundle\FrameworkBundle\Client;
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

    /** @var Client */
    private $client;

    protected function setUp()
    {
        parent::setUp();
        $this->client = self::createClient();
        $this->builder = $this->client->getKernel()->getContainer()->get('shift_url_signature.builder');
        $this->actionUrl = $this->client->getKernel()->getContainer()->get('router')->generate(
            'test_annotation',
            [],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }


    public function testThrowExceptionOnMissingSignature()
    {
        #$this->expectException(AccessDeniedHttpException::class);
        $client = self::createClient();
        $client->request('GET', $this->actionUrl);
        #$crawler = $client->request('GET', '/test-annotation');
        #$this->assertEquals($checkHtml, $crawler->filterXPath('//body')->html());
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testThrowExceptionOnInvalidSignature()
    {
        $signatureQueryKey = $this->client->getKernel()->getContainer()->getParameter('shift_url_signature.query_signature_name');
        $route = sprintf('/test-annotation?%s=invalid-signature-hash', $signatureQueryKey);
        $this->client->request('GET', $route);
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testControllerActionIsCalledOnValidSignature()
    {
        $client = self::createClient();

        $signatureUrl = $this->builder->signUrl($this->actionUrl);
        $client->request('GET', $signatureUrl);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
