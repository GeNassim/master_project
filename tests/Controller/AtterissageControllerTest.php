<?php

namespace App\Test\Controller;

use App\Entity\Atterissage;
use App\Repository\AtterissageRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AtterissageControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private AtterissageRepository $repository;
    private string $path = '/atterissage/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Atterissage::class);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Atterissage index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'atterissage[name]' => 'Testing',
            'atterissage[url]' => 'Testing',
            'atterissage[visuel]' => 'Testing',
            'atterissage[slug]' => 'Testing',
            'atterissage[tunnel]' => 'Testing',
        ]);

        self::assertResponseRedirects('/atterissage/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Atterissage();
        $fixture->setName('My Title');
        $fixture->setUrl('My Title');
        $fixture->setVisuel('My Title');
        $fixture->setSlug('My Title');
        $fixture->setTunnel('My Title');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Atterissage');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Atterissage();
        $fixture->setName('My Title');
        $fixture->setUrl('My Title');
        $fixture->setVisuel('My Title');
        $fixture->setSlug('My Title');
        $fixture->setTunnel('My Title');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'atterissage[name]' => 'Something New',
            'atterissage[url]' => 'Something New',
            'atterissage[visuel]' => 'Something New',
            'atterissage[slug]' => 'Something New',
            'atterissage[tunnel]' => 'Something New',
        ]);

        self::assertResponseRedirects('/atterissage/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getName());
        self::assertSame('Something New', $fixture[0]->getUrl());
        self::assertSame('Something New', $fixture[0]->getVisuel());
        self::assertSame('Something New', $fixture[0]->getSlug());
        self::assertSame('Something New', $fixture[0]->getTunnel());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Atterissage();
        $fixture->setName('My Title');
        $fixture->setUrl('My Title');
        $fixture->setVisuel('My Title');
        $fixture->setSlug('My Title');
        $fixture->setTunnel('My Title');

        $this->repository->add($fixture, true);

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/atterissage/');
    }
}
