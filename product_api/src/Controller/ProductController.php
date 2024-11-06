<?php

namespace App\Controller;

use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
   
    public function index(): JsonResponse
    {
        $products = $this->getDoctrine()->getRepository(Product::class)->findAll();

        $data = [];

        foreach ($products as $product) {
            $data[] = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'stock' => $product->getStock(),
                'price' => $product->getPrice()
            ];
        }

        return $this->json($data, JsonResponse::HTTP_OK);
    }

    public function create(Request $request) : JsonResponse
    {
        $request = json_decode($request->getContent(), true);

        $product = new Product();
        $product->setName($request['name']);
        $product->setStock($request['stock']);
        $product->setPrice($request['price']);
        $product->setDescription($request['description'] ?? '');

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($product);
        $entityManager->flush();

        return $this->json(['message' => 'Product created!'], JsonResponse::HTTP_CREATED);
        //return $this->json($product, Response::HTTP_CREATED, [], ['groups' => 'api']);
    }

    
    public function show(int $id) : JsonResponse
    {
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);

        if (!$product) {
            return $this->json(['message' => 'Product not found!'], JsonResponse::HTTP_NOT_FOUND);
        }

        $data = [
            'id' => $product->getId(),
            'name' => $product->getName(),
            'stock' => $product->getStock(),
            'price' => $product->getPrice(),
            'description' => $product->getDescription()
        ];

        return $this->json($data, JsonResponse::HTTP_OK);
    }

    public function update(int $id, Request $request) : JsonResponse
    {
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);

        if (!$product) {
            return $this->json(['message' => 'Product not found!'], JsonResponse::HTTP_NOT_FOUND);
        }

        $request = json_decode($request->getContent(), true);

        $product->setName($request['name'] ?? $product->getName());
        $product->setStock($request['stock'] ?? $product->getStock());
        $product->seTDescription($request['description'] ?? $product->getDescription());
        $product->setPrice($request['price'] ?? $product->getPrice());

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();

        return $this->json(['message' => 'Product updated!'], JsonResponse::HTTP_OK);
    }

    public function delete(int $id): JsonResponse
    {
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);

        if (!$product) {
            return $this->json(['message' => 'Product not found!'], JsonResponse::HTTP_NOT_FOUND);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($product);
        $entityManager->flush();

        return $this->json(['message' => 'Product deleted!'], JsonResponse::HTTP_OK);
    }
}
