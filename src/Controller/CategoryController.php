<?php

namespace App\Controller;




use App\Form\CategoryType;

use Doctrine\Persistence\ManagerRegistry;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use function Symfony\Component\Form\handleRequest;
use function Symfony\Config\Monolog\persistent;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use App\Entity\Product;
use App\Entity\User;
use App\Entity\Category;
use App\Entity\Review;
use App\Form\ProductType;

class CategoryController extends AbstractController
{
    #[Route('/category', name: 'app_category')]
    public function index(): Response
    {
        return $this->render('category/index.html.twig', [
            'controller_name' => 'CategoryController',
        ]);
    }
    /**
     * @Route("Admin/category/", name="category_list")
     */

    public function listcategoryAction(ManagerRegistry $doctrine ): Response{
        $products = $doctrine->getRepository('App\Entity\Product')->findAll();
        $categories = $doctrine->getRepository('App\Entity\category')->findAll();

        return $this->render('Admin/category/index.html.twig', ['products' => $products, 'categories' => $categories]);
    }
    /**

     * @Route("Admin/category/details/{id}", name="category_details")
     */
    public
    function detailscategoryAction(ManagerRegistry $doctrine,$id): Response
    {
        $category = $doctrine->getRepository('App\Entity\Category')->find($id);


        return $this->render('Admin/category/details.html.twig', [
            'category' => $category]);
    }

    /**
     * @Route("Admin/category/delete/{id}", name="category_delete")
     */
    public function deleteAction(ManagerRegistry $doctrine,$id):Response
    {$em = $doctrine->getManager();
        $categories = $em->getRepository('App\Entity\Category')->find($id);

        $em->remove($categories);
        $em->flush();


        $this->addFlash(
            'error',
            'categories deleted'
        );

        return $this->redirectToRoute('category_list');
    }

    /**
     * @Route("Admin/category/create", name="category_create", methods={"GET","POST"})
     */
    public function createcategoryAction(Request $request, ManagerRegistry $doctrine)
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->persist($category);
            $em->flush();
            return $this->redirectToRoute('category_list',
                ['id' => $category->getId()]);

        }

        return $this->render('Admin/category/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("Admin/category/edit/{id}", name="category_edit")
     */
    public function editcategoryAction(ManagerRegistry $doctrine,int $id, Request $request):Response
    {
        $em = $doctrine->getManager();
        $category = $em->getRepository('App\Entity\Category')->find($id);

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $em = $doctrine->getManager();
            $em->persist($category);
            $em->flush();
            return $this->redirectToRoute('category_list',
                ['id' => $category->getId()]);

        }
        return $this->renderForm('Admin/category/edit.html.twig', ['form' => $form,]);


    }

}
