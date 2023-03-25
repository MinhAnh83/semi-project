<?php

namespace App\Controller;

use App\Entity\Author;
use App\Form\AuthorType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Form\CategoryType;

use function Symfony\Component\Form\handleRequest;
use function Symfony\Config\Monolog\persistent;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use App\Entity\Product;
use App\Entity\User;
use App\Entity\Category;
use App\Entity\Review;
use App\Form\ProductType;

class AuthorController extends AbstractController
{
    #[Route('/author', name: 'app_author')]
    public function index(): Response
    {
        return $this->render('author/index.html.twig', [
            'controller_name' => 'AuthorController',
        ]);
    }

    /**
     * @Route("Admin/author/", name="author_list")
     */

    public function listauthorAction(ManagerRegistry $doctrine ): Response{
        $products = $doctrine->getRepository('App\Entity\Product')->findAll();
        $authors = $doctrine->getRepository('App\Entity\Author')->findAll();

        return $this->render('Admin/author/index.html.twig', ['products' => $products, 'authors' => $authors]);
    }
    /**

     * @Route("Admin/author/details/{id}", name="author_details")
     */
    public
    function detailsauthorAction(ManagerRegistry $doctrine,$id): Response
    {
        $author = $doctrine->getRepository('App\Entity\Author')->find($id);


        return $this->render('Admin/author/details.html.twig', [
            'author' => $author]);
    }

    /**
     * @Route("Admin/author/delete/{id}", name="author_delete")
     */
    public function deleteauthorAction(ManagerRegistry $doctrine,$id):Response
    {$em = $doctrine->getManager();
        $authors = $em->getRepository('App\Entity\Author')->find($id);

        $em->remove($authors);
        $em->flush();


        $this->addFlash(
            'error',
            'authors deleted'
        );

        return $this->redirectToRoute('author_list');
    }

    /**
     * @Route("Admin/author/create", name="author_create", methods={"GET","POST"})
     */
    public function createauthorAction(Request $request, ManagerRegistry $doctrine)
    {
        $author = new Author();
        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->persist($author);
            $em->flush();
            return $this->redirectToRoute('author_list',
                ['id' => $author->getId()]);

        }

        return $this->render('Admin/author/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("Admin/author/edit/{id}", name="author_edit")
     */
    public function editauthorAction(ManagerRegistry $doctrine,int $id, Request $request):Response
    {
        $em = $doctrine->getManager();
        $author = $em->getRepository('App\Entity\Author')->find($id);

        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $em = $doctrine->getManager();
            $em->persist($author);
            $em->flush();
            return $this->redirectToRoute('author_list',
                ['id' => $author->getId()]);

        }
        return $this->renderForm('Admin/author/edit.html.twig', ['form' => $form,]);


    }
}
