<?php

namespace App\Controller;

use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\VarDumper\VarDumper;

class CategoryController extends AbstractController
{
    /**
     * @Route("/category", name="category_show", methods={"GET"})
     */
    public function show(): Response
    {
        $categoryArray = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findAll();

        if (!$categoryArray) {
            throw $this->createNotFoundException(
                'No category found.'
            );
        }

        $data = array();

        foreach($categoryArray as $category) {
            array_push($data, $category->inicializar());
        }

        return new Response(json_encode($data));
    }

    /**
     * @Route("/category/{codigo}", name="category_index", methods={"GET"})
     */
    public function index(int $codigo): Response
    {
        $category = $this->getDoctrine()
            ->getRepository(Category::class)
            ->find($codigo);

        if (!$category) {
            throw $this->createNotFoundException(
                'No category found.'
            );
        }

        return new Response(json_encode($category->inicializar()));
    }

    /**
     * @Route("/category/{codigo}", name="category_delete", methods={"DELETE"})
     */
    public function delete(int $codigo): Response
    {
        $category = $this->getDoctrine()
            ->getRepository(Category::class)
            ->find($codigo);

        if (!$category) {
            throw $this->createNotFoundException(
                'There is no category with id: '.$codigo
            );
        }
        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->remove($category);
        $entityManager->flush();

        return new Response('The category with id '.$codigo.' was successfully deleted.');
    }

    /**
     * @Route("/category", name="create_category", methods={"POST"})
     */
    public function createCategory(Request $request): Response
    {
        $parameters = json_decode($request->getContent(), true);

        $entityManager = $this->getDoctrine()->getManager();
        $category = new Category();
        $category->setName($parameters['name']);

        // tell Doctrine you want to (eventually) save the Category (no queries yet)
        $entityManager->persist($category);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        return new Response('Saved new category with id '.$category->getId());
    }

    /**
     * @Route("/category/{codigo}", name="update_category", methods={"PUT"})
     */
    public function updateCategory(int $codigo, Request $request): Response
    {
        $parameters = json_decode($request->getContent(), true);

        // Verificar se já existe o identificador informado registrado
        $category = $this->getDoctrine()
            ->getRepository(Category::class)
            ->find($codigo);
        if (!$category) {
            throw $this->createNotFoundException(
                'There is no category with the id: '.$codigo
            );
        }

        $entityManager = $this->getDoctrine()->getManager();
        $category->setName($parameters['name']);

        // tell Doctrine you want to (eventually) save the Category (no queries yet)
        $entityManager->persist($category);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        return new Response('The category with id '.$codigo.' was successfully updated.');
    }
}
