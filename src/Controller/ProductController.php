<?php

namespace App\Controller;


use App\Entity\Category;
use App\Entity\ContactForm;
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
use App\Entity\Review;
use App\Form\ProductType;
use App\Repository\ReviewRepository;






class ProductController extends AbstractController
{
    #[Route('/product', name: 'app_product')]
//    public function index(): Response
//    {
//        return $this->render('product/index.html.twig', [
//            'controller_name' => 'ProductController',
//        ]);
//    }

    /**
     * @Route("Admin/", name="product_list")
     */

    public function listproduct(ManagerRegistry $doctrine): Response
    { $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $products = $doctrine->getRepository('App\Entity\Product')->findAll();
        $categories = $doctrine->getRepository('App\Entity\category')->findAll();
        $reviews = $doctrine->getRepository('App\Entity\Review')->findAll();
        return $this->render('Admin/product/index.html.twig', ['products' => $products, 'categories' => $categories, 'reviews' => $reviews]);
    }

//    /**
//     * @Route("Admin/user", name="user_list")
//     */
//
//    public function listUser(ManagerRegistry $doctrine): Response
//    {
//        $users = $doctrine->getRepository('App\Entity\User')->findAll();
//
//        return $this->render('Admin/user/index.html.twig', ['users' => $users]);
//    }


    /**
     * @Route("Admin/product/details/{id}", name="product_details")
     */
    public
    function detailsAction(ManagerRegistry $doctrine, $id): Response
    {
        $products = $doctrine->getRepository('App\Entity\Product')->find($id);


        return $this->render('Admin/product/details.html.twig', [
            'products' => $products]);
    }
    /**
     * @Route("Admin/product/review_details/{id}", name="review_details")
     */
    public
    function detailsreviewAction(ManagerRegistry $doctrine, ReviewRepository $reviews,$id): Response
    {
        $products = $doctrine->getRepository('App\Entity\Product')->find($id);
        return $this->render('Admin/product/review_details.html.twig', [
             'reviews' => $reviews->showReviewWithIdProduct($id),
            'products'=>$products]);
    }




    /**
     * @Route("Admin/product/delete/{id}", name="product_delete")
     */
    public function deleteAction(ManagerRegistry $doctrine,$id):Response
    {$em = $doctrine->getManager();
        $product = $em->getRepository('App\Entity\Product')->find($id);
        $review = $em->getRepository('App\Entity\Review')->find($id);

        $em->remove($product);
        $em->flush();
        $em->remove($review);
        $em->flush();


        $this->addFlash(
            'error',
            'Product deleted'
        );

        return $this->redirectToRoute('product_list');
    }
    /**
     * @Route("Admin/product/create", name="product_create", methods={"GET","POST"})
     */
    public function createAction(ManagerRegistry $doctrine,Request $request, SluggerInterface $slugger)
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // upload file
            $productImage = $form->get('ProductImages')->getData();
            if ($productImage) {
                $originalFilename = pathinfo($productImage->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $productImage->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $productImage->move(
                        $this->getParameter('productImages_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash(
                        'error',
                        'Cannot upload'
                    );// ... handle exception if something happens during file upload
                }
                $product->setProductImages($newFilename);
            }else{
                $this->addFlash(
                    'error',
                    'Cannot upload'
                );// ... handle exception if something happens during file upload
            }
            $em = $doctrine->getManager();
            $em->persist($product);
            $em->flush();

            $this->addFlash(
                'notice',
                'Product Added'
            );
            return $this->redirectToRoute('product_list');
        }
        return $this->renderForm('Admin/product/create.html.twig', ['form' => $form,]);
    }



    /**
     * @Route("Admin/product/edit/{id}", name="product_edit")
     */
    public function editproduct(ManagerRegistry $doctrine, int $id, Request $request): Response
    {
        $em = $doctrine->getManager();
        $product = $em->getRepository('App\Entity\Product')->find($id);


        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $em = $doctrine->getManager();
            $em->persist($product);
            $em->flush();
            return $this->redirectToRoute('product_list',
                ['id' => $product->getId()]);

        }
        return $this->renderForm('Admin/product/edit.html.twig', ['form' => $form,]);
    }

}




