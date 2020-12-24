<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleController extends AbstractController
{

    /**
     * @Route("/", name="article_liste", methods={"GET"})
     */
    public function listAction(ArticleRepository $articleRepository, SerializerInterface $serialize)
    {
        $article = $articleRepository->findAll();        
        $json = $serialize->serialize($article,'json', ['groups' => 'show_article']);

        $response = new Response($json, 200, [
            "Content-Type" =>"application/json"
        ]);
        return $response;
    }

    /**
     * @Route("/articles/{id}", name="article_show", methods={"GET"})
     */
    public function showAction(Article $article)
    {  
        $data = $this->get('serializer')->serialize($article, 'json',['groups'=>'show_article']);

        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/articles", name="article_create", methods={"POST"})
     */
    public function createAction(Request $request, SerializerInterface $serializer, EntityManagerInterface $em)
    {
        $jsonRecu = $request->getContent();

        $article = $serializer->deserialize($jsonRecu, Article::class, 'json');

        $em->persist($article);
        $em->flush();

        return $this->json($article,201,[], ['groups'=>'show_article']);
    }

    /**
     * @Route("/articles/{id}", name="article_update", methods={"PUT"})
     */
    public function updateAction(int $id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $article = $entityManager->getRepository('App\Entity\Article')->find($id);
        $author = $entityManager->getRepository('App\Entity\Author')->find($id);
        
        $article->setTitle('titre cinq');
        $article->setContent('Le contenu du cinquième article');
        $author->setFullname('Grand Corps Malade');
        $author->setBiography('La vie scolaire');

        $entityManager->flush();
        return new Response('', Response::HTTP_CREATED);
    }

    /**
     * @Route("/articles/{id}", name="article_delete",methods={"DELETE"})
     */
    public function deleteAction(int $id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $article = $entityManager->getRepository('App\Entity\Article')->find($id);

        $entityManager->remove($article);
        $entityManager->flush();
        return new Response('', Response::HTTP_OK);
    }
}
