<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;

class UserController extends AbstractController
{

    /**
     * @Route("/Admin/user", name="admin_index", methods={"GET"})
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        $users=$doctrine->getRepository('App\Entity\User')->findAll();


        return $this->render('Admin/user/index.html.twig', [
            'users' => $users,
        ]);
    }
    /**
     * @Route("Admin/user/delete/{id}", name="user_delete")
     */
    public function deleteAction(ManagerRegistry $doctrine,$id):Response
    {$em = $doctrine->getManager();
        $users = $em->getRepository('App\Entity\User')->find($id);

        $em->remove($users);
        $em->flush();


        $this->addFlash(
            'error',
            'users deleted'
        );

        return $this->redirectToRoute('admin_index');
    }


}
