<?php
namespace App\Controller;

use Carbon\Carbon;
use App\Entity\Article;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class IndexController extends AbstractController
{
    /**
     * @Route("/")
     * @Method({"GET"})
     */
    public function index(ManagerRegistry $doctrine, Request $request): Response
    {
        $articles = $doctrine->getManager()->getRepository(Article::class)->findAll();
        
        return $this->render('/article/index.html.twig', ['articles' => $articles]);
    }

    /**
     * @Route("/article/save")
     */
    public function save(ManagerRegistry $doctrine)
    {
        $entityManager = $doctrine->getManager();
        $articleRepository = $entityManager->getRepository(Article::class);

        $article = new Article();
        $article->setTitle('First Title 2');
        $article->setBody('First Body 2');
        $article->setCreatedAt(Carbon::now());

        $articleRepository->add($article, true);

        return new Response('Added article ' . $article->getId());
    }
}