<?php

namespace App\Controller;

use App\Entity\Author;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AuthorController extends AbstractController
{

    /**
     * @Route("/authors", name="all_authors", methods={"GET"})
     */
    public function listAction(AuthorRepository $authorRepository, SerializerInterface $serialize)
    {
        $article = $authorRepository->findAll();        
        $json = $serialize->serialize($article,'json', ['groups' => 'show_article']);

        $response = new Response($json, 200, [
            "Content-Type" =>"application/json"
        ]);
        return $response;
    }

    /**
     * @Route("/authors/{id}", name="author_show", methods={"GET"})
     */
    public function showAction(Author $author)
    {
        $data = $this->get('serializer')->serialize($author, 'json',['groups'=>'show_article']);
        
        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/authors", name="author_create", methods={"POST"})
     */
    public function createAction(Request $request, SerializerInterface $serialize, EntityManagerInterface $em)
    {
      $jsonRecu = $request->getContent();
      $author = $serialize->deserialize($jsonRecu,Author::class, 'json');

      $em->persist($author);
      $em->flush();

      return $this->json($author,201,[]);
    }

    /**
     * @Route("/authors/{id}", name="author_update", methods={"PUT"})
     */
    public function UpdateAction(int $id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $author = $entityManager->getRepository('App\Entity\Author')->find($id);
        
        $author->setFullname('Hermione');
        $author->setBiography('Dentiste qui opère grâce à la magie');

        $entityManager->flush();
        return new Response('', Response::HTTP_CREATED);        
    }

    /**
     * @Route("/authors/{id}", name="author_delete", methods={"DELETE"})
     */
    public function deleteAction(int $id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $author = $entityManager->getRepository('App\Entity\Author')->find($id);

        $entityManager->remove($author);
        $entityManager->flush();
        return new Response('', Response::HTTP_OK);
    }
}
