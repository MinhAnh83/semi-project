<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\Product;
use App\Form\ContactType;
use App\Form\CreateProductType;
use App\Repository\ReviewRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]

    /**
     * @Route("/home/", name="get_product")
     */

    public function getproduct(ManagerRegistry $doctrine):Response
    {
        $products = $doctrine->getRepository('App\Entity\Product')->findAll();
        $categories = $doctrine->getRepository('App\Entity\category')->findAll();
//        $category=$doctrine->getRepository('App\Entity\category')->find($id);





        return $this->render('home/index.html.twig', ['products' => $products, 'categories' => $categories]);
    }

    /**

     * @Route("category/details/{id}", name="category_pages")
     */
    public
    function categoryAction(ManagerRegistry $doctrine,$id): Response
    {
        $category = $doctrine->getRepository('App\Entity\Category')->find($id);


        return $this->render('home/category_page.html.twig',[
            'category' => $category]);
    }


        /**
         * @Route("home/contact", name="contact", methods={"GET","POST"})
         */
        public
        function contactAction(Request $request, ManagerRegistry $doctrine)
        {
            $contact = new Contact();
            $form = $this->createForm(ContactType::class, $contact);

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $em = $doctrine->getManager();
                $em->persist($contact);
                $em->flush();
                return $this->redirectToRoute('get_product',
                    ['id' => $contact->getId()]);

            }

            return $this->render('home/contact_page.html.twig', [
                'form' => $form->createView()
            ]);
        }







    /**
     * @Route("home/product/create", name="product_create_page", methods={"GET","POST"})
     */
    public function createproductAction(ManagerRegistry $doctrine,Request $request, SluggerInterface $slugger)
    { $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $product = new Product();
        $form = $this->createForm(CreateProductType::class, $product);

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
            return $this->redirectToRoute('get_product');
        }
        return $this->renderForm('home/product_create.html.twig', ['form' => $form,]);
    }
}

