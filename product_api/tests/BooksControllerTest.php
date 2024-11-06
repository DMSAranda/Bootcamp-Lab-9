<?php

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\BookRepository;
class BooksControllerTest extends WebTestCase
{

    public function testIndex()
    {
        $client = static::createClient(array(), array('HTTP_HOST' => 'localhost:84'));

        $client->request('GET', '/api/books');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertIsArray(json_decode($client->getResponse()->getContent(), true));
    }

    public function testCreate()
    {
        $client = static::createClient(array(), array('HTTP_HOST' => 'localhost:84'));

        $client->request('POST', '/api/books/new', [], [], ['CONTENT_TYPE' => 'application/json'], '{"title": "Libro prueba", "author": "autor", "price": 10.99}');

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
    }

    public function testShow()
    {
        $client = static::createClient(array(), array('HTTP_HOST' => 'localhost:84'));

        $bookRepository = static::getContainer()->get(BookRepository::class);
        $books = $bookRepository->findAll();

        $client->request('GET', '/api/books/'.$books[0]->getId());

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testUpdate()
    {
        $client = static::createClient(array(), array('HTTP_HOST' => 'localhost:84'));

        $bookRepository = static::getContainer()->get(BookRepository::class);
        $books = $bookRepository->findAll();

        $client->request('PUT', '/api/books/'.$books[0]->getId().'/edit', [], [], ['CONTENT_TYPE' => 'application/json'], '{"title": "Updated Book", "price": 19.99}');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testDelete()
    {
        $client = static::createClient(array(), array('HTTP_HOST' => 'localhost:84'));

        $bookRepository = static::getContainer()->get(BookRepository::class);
        $books = $bookRepository->findAll();

        $client->request('DELETE', '/api/books/'.$books[0]->getId());

        $this->assertEquals(204, $client->getResponse()->getStatusCode());
    }
}
