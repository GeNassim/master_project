<?php

namespace App\Test\Controller;

use App\Entity\Etape;
use App\Repository\EtapeRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EtapeControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EtapeRepository $repository;
    private string $path = '/etape/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Etape::class);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Etape index');

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
            'etape[user]' => 'Testing',
            'etape[email]' => 'Testing',
            'etape[sujet]' => 'Testing',
            'etape[message]' => 'Testing',
            'etape[delai]' => 'Testing',
            'etape[temps]' => 'Testing',
            'etape[date]' => 'Testing',
            'etape[heure]' => 'Testing',
            'etape[campagne]' => 'Testing',
        ]);

        self::assertResponseRedirects('/etape/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Etape();
        $fixture->setUser('My Title');
        $fixture->setEmail('My Title');
        $fixture->setSujet('My Title');
        $fixture->setMessage('My Title');
        $fixture->setDelai('My Title');
        $fixture->setTemps('My Title');
        $fixture->setDate('My Title');
        $fixture->setHeure('My Title');
        $fixture->setCampagne('My Title');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Etape');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Etape();
        $fixture->setUser('My Title');
        $fixture->setEmail('My Title');
        $fixture->setSujet('My Title');
        $fixture->setMessage('My Title');
        $fixture->setDelai('My Title');
        $fixture->setTemps('My Title');
        $fixture->setDate('My Title');
        $fixture->setHeure('My Title');
        $fixture->setCampagne('My Title');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'etape[user]' => 'Something New',
            'etape[email]' => 'Something New',
            'etape[sujet]' => 'Something New',
            'etape[message]' => 'Something New',
            'etape[delai]' => 'Something New',
            'etape[temps]' => 'Something New',
            'etape[date]' => 'Something New',
            'etape[heure]' => 'Something New',
            'etape[campagne]' => 'Something New',
        ]);

        self::assertResponseRedirects('/etape/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getUser());
        self::assertSame('Something New', $fixture[0]->getEmail());
        self::assertSame('Something New', $fixture[0]->getSujet());
        self::assertSame('Something New', $fixture[0]->getMessage());
        self::assertSame('Something New', $fixture[0]->getDelai());
        self::assertSame('Something New', $fixture[0]->getTemps());
        self::assertSame('Something New', $fixture[0]->getDate());
        self::assertSame('Something New', $fixture[0]->getHeure());
        self::assertSame('Something New', $fixture[0]->getCampagne());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Etape();
        $fixture->setUser('My Title');
        $fixture->setEmail('My Title');
        $fixture->setSujet('My Title');
        $fixture->setMessage('My Title');
        $fixture->setDelai('My Title');
        $fixture->setTemps('My Title');
        $fixture->setDate('My Title');
        $fixture->setHeure('My Title');
        $fixture->setCampagne('My Title');

        $this->repository->add($fixture, true);

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/etape/');
    }
}
