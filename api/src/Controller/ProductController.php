<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\VarDumper\VarDumper;

class ProductController extends AbstractController
{
    /**
     * @Route("/product", name="product_show", methods={"GET"})
     */
    public function show(): Response
    {
        $productArray = $this->getDoctrine()
            ->getRepository(Product::class)
            ->findAll();

        if (!$productArray) {
            throw $this->createNotFoundException(
                'No product found.'
            );
        }

        $data = array();

        foreach($productArray as $product) {
            array_push($data, $product->inicializar());
        }

        return new Response(json_encode($data));
    }

    /**
     * @Route("/product/{codigo}", name="product_index", methods={"GET"})
     */
    public function index(string $codigo): Response
    {
        $product = $this->getDoctrine()
            ->getRepository(Product::class)
            ->findOneBy(array('identifier' => $codigo));

        if (!$product) {
            throw $this->createNotFoundException(
                'No product found.'
            );
        }

        return new Response(json_encode($product->inicializar()));
    }

    /**
     * @Route("/product/{codigo}", name="product_delete", methods={"DELETE"})
     */
    public function delete(string $codigo): Response
    {
        $product = $this->getDoctrine()
            ->getRepository(Product::class)
            ->findOneBy(array('identifier' => $codigo));

        if (!$product) {
            throw $this->createNotFoundException(
                'There is no product with the identifier: '.$codigo
            );
        }
        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->remove($product);
        $entityManager->flush();

        return new Response('The product with identifier '.$codigo.' was successfully deleted.');
    }

    /**
     * @Route("/product", name="create_product", methods={"POST"})
     */
    public function createProduct(Request $request): Response
    {
        $parameters = json_decode($request->getContent(), true);

        // Verificar se já existe o identificador informado registrado
        $product = $this->getDoctrine()
            ->getRepository(Product::class)
            ->findBy(array('identifier' => $parameters['identifier']));
        if ($product) {
            throw $this->createNotFoundException(
                'There is already a product with the identifier: '.$parameters['identifier']
            );
        }

        $category = $this->getDoctrine()
            ->getRepository(Category::class)
            ->find($parameters['category']);
        if (!$category) {
            throw $this->createNotFoundException(
                'There is no category with id: '.$parameters['category']
            );
        }

        $entityManager = $this->getDoctrine()->getManager();
        $product = new Product();
        $product
            ->setName($parameters['name'])
            ->setCategory($category)
            ->setIdentifier($parameters['identifier']);

        // tell Doctrine you want to (eventually) save the Product (no queries yet)
        $entityManager->persist($product);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        return new Response('Saved new product with identifier '.$product->getIdentifier());
    }

    /**
     * @Route("/product/{codigo}", name="update_product", methods={"PUT"})
     */
    public function updateProduct(string $codigo, Request $request): Response
    {
        $parameters = json_decode($request->getContent(), true);

        // Verificar se já existe o identificador informado registrado
        $product = $this->getDoctrine()
            ->getRepository(Product::class)
            ->findOneBy(array('identifier' => $codigo));
        if (!$product) {
            throw $this->createNotFoundException(
                'There is no product with the identifier: '.$codigo
            );
        }

        $category = $this->getDoctrine()
            ->getRepository(Category::class)
            ->find($parameters['category']);
        if (!$category) {
            throw $this->createNotFoundException(
                'There is no category with id: '.$parameters['category']
            );
        }

        $entityManager = $this->getDoctrine()->getManager();
        $product
            ->setName($parameters['name'])
            ->setCategory($category)
            ->setIdentifier($parameters['identifier']);

        // tell Doctrine you want to (eventually) save the Product (no queries yet)
        $entityManager->persist($product);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        return new Response('The product with identifier '.$codigo.' was successfully updated.');
    }
}
