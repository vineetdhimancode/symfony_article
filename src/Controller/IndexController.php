<?php
namespace App\Controller;

use Carbon\Carbon;

use App\Entity\Article;

use Doctrine\Persistence\ManagerRegistry;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index")
     * @Method({"GET"})
     */
    public function index(ManagerRegistry $doctrine, Request $request): Response
    {
        $articles = $doctrine->getManager()->getRepository(Article::class)->findAll();
        
        return $this->render('/article/index.html.twig', ['articles' => $articles]);
    }

    /**
     * @Route("/article/new")
     */
    public function new(ManagerRegistry $doctrine, Request $request)
    {
        $article = new Article();

        $form = $this->createFormBuilder($article)
            ->add('title', TextType::class, [
                'attr' => ['class' => 'form-control w-25 my-2']
            ])
            ->add('body', TextareaType::class, [
                'attr' => ['class' => 'form-control w-25 my-2']
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Save',
                'attr' => ['class' => 'btn btn-primary my-2']
            ])
            ->getForm();

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $article = $form->getData();
                $article->setCreatedAt(Carbon::now());
                $manager = $doctrine->getManager()->getRepository(Article::class);
                $manager->add($article, true);
                
                $this->addFlash('notice', 'Article added successfully');
                return $this->redirectToRoute('index');
            }

        return $this->render('/article/edit.html.twig', [
            'form' => $form->createView(),
            'title' => $article->getId() ? 'Edit' : 'New'
        ]);
    }

    /**
     * @Route("/article/{id}")
     */
    public function view(ManagerRegistry $doctrine, Request $request)
    {
        $article = $doctrine->getManager()->getRepository(Article::class)->find($request->get('id'));
        return $this->render('/article/view.html.twig', ['article' => $article]);
    }

    /**
     * @Route("/edit/{id}", name="edit_article")
     * @Method({"GET", "POST"})
     */
    public function edit(ManagerRegistry $doctrine, Request $request)
    {
        $article = $doctrine->getManager()->getRepository(Article::class)->find($request->get('id'));

        $form = $this->createFormBuilder($article)
            ->add('title', TextType::class, [
                'attr' => ['class' => 'form-control w-25 my-2']
            ])
            ->add('body', TextareaType::class, [
                'attr' => ['class' => 'form-control w-25 my-2']
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Save',
                'attr' => ['class' => 'btn btn-primary my-2']
            ])
            ->getForm();

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $article = $form->getData();
                $respository = $doctrine->getManager()->getRepository(Article::class);
                $respository->add($article, true);
                
                $this->addFlash('notice', 'Article updated successfully');
                return $this->redirectToRoute('index');
            }

        return $this->render('/article/edit.html.twig', [
            'form' => $form->createView(),
            'title' => $article->getId() ? 'Edit' : 'New'
        ]);
    }

    /**
     * @Route("/delete/{id}")
     * @Method()
     */
    public function delete(ManagerRegistry $doctrine, Request $request)
    {
        $id = $request->get('id');
        $respository = $doctrine->getManager()->getRepository(Article::class);
        $article = $respository->find((int) $id);
        $respository->remove($article, true);

        $this->addFlash('notice', 'Article deleted successfully');

        return $this->redirectToRoute('index');
    }
}