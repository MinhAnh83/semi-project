<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Review;
use App\Form\ReviewType;
use App\Repository\ReviewRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


use function Symfony\Component\Form\handleRequest;
use function Symfony\Config\Monolog\persistent;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;



class ReviewController extends AbstractController
{
    /**
     * @Route("Admin/product/review", name="review_list")
     */

    public function listcategoryAction(ManagerRegistry $doctrine ): Response{
        $products = $doctrine->getRepository('App\Entity\Product')->findAll();
        $reviews = $doctrine->getRepository('App\Entity\Review')->findAll();

        return $this->render('Admin/category/index.html.twig', ['products' => $products, 'reviews' => $reviews]);
    }

    /**
     * @Route("home/review/create", name="review_create", methods={"GET","POST"})
     */
    public function createreviewAction(Request $request, ManagerRegistry $doctrine)
    { $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $review = new Review();
        $form = $this->createForm(ReviewType::class, $review);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->persist($review);
            $em->flush();
            return $this->redirectToRoute('get_product',
                ['id' => $review->getId()]);

        }

        return $this->render('review/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("home/review_details/{id}", name="review_product")
     */
    public
    function reviewproductAction(ManagerRegistry $doctrine, ReviewRepository $reviews,$id): Response
    {
        $products = $doctrine->getRepository('App\Entity\Product')->find($id);

        return $this->render('review/index.html.twig', [
            'reviews' => $reviews->showReviewWithIdProduct($id),
            'products'=>$products]);
    }

//public
//function reviewproductAction(ManagerRegistry $doctrine, ReviewRepository $reviews,$id): Response
//

//    $reviews= $doctrine->getRepository(Review :: class)->find($id);
//    $products=$reviews->getRanking();
//    $reviews=$doctrine->getRepository('App\Entity\Review')->findAll();
//
//
//    return $this->render('review/index.html.twig', [
//        'reviews' => $reviews,
//        'products'=>$products]);
//}
//    /**
//     * @Route("Admin/category", name="productbyCat")
//     */

//public
//function productbyCatAction(ManagerRegistry $doctrine, ReviewRepository $reviews,$id): Response
//

//    $category= $doctrine->getRepository(Category :: class)->find($id);
//    $products=$category->getProduct();
//    $xategories=$doctrine->getRepository('App\Entity\Category')->findAll();
//
//
//    return $this->render('Admin/category/index.html.twig', [
//        'categories => $categories,
//        'products'=>$products]);
//}
}
