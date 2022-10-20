<?php

namespace App\Test\Controller;

use App\Entity\Campagne;
use App\Repository\CampagneRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CampagneControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private CampagneRepository $repository;
    private string $path = '/campagne/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Campagne::class);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Campagne index');

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
            'campagne[name]' => 'Testing',
            'campagne[description]' => 'Testing',
            'campagne[active]' => 'Testing',
            'campagne[createdAt]' => 'Testing',
        ]);

        self::assertResponseRedirects('/campagne/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Campagne();
        $fixture->setName('My Title');
        $fixture->setDescription('My Title');
        $fixture->setActive('My Title');
        $fixture->setCreatedAt('My Title');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Campagne');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Campagne();
        $fixture->setName('My Title');
        $fixture->setDescription('My Title');
        $fixture->setActive('My Title');
        $fixture->setCreatedAt('My Title');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'campagne[name]' => 'Something New',
            'campagne[description]' => 'Something New',
            'campagne[active]' => 'Something New',
            'campagne[createdAt]' => 'Something New',
        ]);

        self::assertResponseRedirects('/campagne/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getName());
        self::assertSame('Something New', $fixture[0]->getDescription());
        self::assertSame('Something New', $fixture[0]->getActive());
        self::assertSame('Something New', $fixture[0]->getCreatedAt());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Campagne();
        $fixture->setName('My Title');
        $fixture->setDescription('My Title');
        $fixture->setActive('My Title');
        $fixture->setCreatedAt('My Title');

        $this->repository->add($fixture, true);

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/campagne/');
    }
}
