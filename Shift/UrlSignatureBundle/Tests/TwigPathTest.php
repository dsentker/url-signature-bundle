<?php

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class TwigPathTest extends WebTestCase
{

    /** @var Client */
    private $client;

    protected function setUp()
    {
        parent::setUp();
        $this->client = self::createClient();

    }

    public function testLinkContainsSignature()
    {
        $url = $this->client->getKernel()->getContainer()->get('router')->generate('test_link', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $crawler = $this->client->request('GET', $url);

        $signatureQueryKey = $this->client->getKernel()->getContainer()->getParameter('shift_url_signature.query_signature_name');

        $anchorLink = $crawler->filter('a#link')->eq(0)->attr('href');
        $expectedLinkPattern = sprintf('~\/test-annotation\?%s=~', $signatureQueryKey);
        $this->assertRegExp($expectedLinkPattern, $anchorLink);
    }

    public function testLinkIsVerified()
    {
        $url = $this->client->getKernel()->getContainer()->get('router')->generate('test_link', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $crawler = $this->client->request('GET', $url);

        $link = $crawler->filter('a#link')->eq(0)->attr('href');
        $crawler = $this->client->request('GET', $link);

        $this->assertEquals('Hello, World!', $crawler->filter('p#welcomeMessage')->eq(0)->text());
    }

    public function testLinkContainsExpiration()
    {
        $url = $this->client->getKernel()->getContainer()->get('router')->generate('test_link_timeout', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $crawler = $this->client->request('GET', $url);

        $timeoutQueryKey = $this->client->getKernel()->getContainer()->getParameter('shift_url_signature.query_expires_name');

        $anchorLink = $crawler->filter('a#link')->eq(0)->attr('href');
        $expectedLinkPattern = sprintf('~\/test-annotation.*(&|\?)%s=~', $timeoutQueryKey);
        $this->assertRegExp($expectedLinkPattern, $anchorLink);
    }

    public function testLinkContainsExpirationIsVerified() {
        $url = $this->client->getKernel()->getContainer()->get('router')->generate('test_link_timeout', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $crawler = $this->client->request('GET', $url);

        $link = $crawler->filter('a#link')->eq(0)->attr('href');
        $crawler = $this->client->request('GET', $link);
        #echo $link;
        #echo $crawler->html();
        $this->assertEquals('Hello, World!', $crawler->filter('p#welcomeMessage')->eq(0)->text());
    }




}
