<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\Review;
use App\Form\ReviewType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(): Response
    {
        return $this->render('contact/index.html.twig', [
            'controller_name' => 'ContactController',
        ]);
    }

    /**
     * @Route("Admin/contact", name="contact_list")
     */

    public function listcontactAction(ManagerRegistry $doctrine): Response
    {
        $contacts = $doctrine->getRepository('App\Entity\Contact')->findAll();


        return $this->render('Admin/contact/contact.html.twig', ['contacts' => $contacts]);
    }
    /**
     * @Route("Admin/contact/delete/{id}", name="contact_delete")
     */
    public function deletecontactAction(ManagerRegistry $doctrine,$id):Response
    {$em = $doctrine->getManager();
        $contacts = $em->getRepository('App\Entity\Contact')->find($id);

        $em->remove($contacts);
        $em->flush();


        $this->addFlash(
            'error',
            'contacts deleted'
        );

        return $this->redirectToRoute('contact_list');
    }


}
