<?php

namespace App\Controller;


use App\Entity\Contact;
use App\Form\ContactFormType;
use App\Repository\ContactRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact')]
    public function index(ContactRepository $contactRepository, Request $request, ManagerRegistry $doctrine): Response
    {
        $contact = new Contact();
        $contact->setDate(new \DateTime('now'));
        /**@var User */
        $user = $this->getUser();
        if($user){
            $contact->setEmail($user->getEmail());
        }
        $form = $this->createForm(ContactFormType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //les envoyer a la bdd
            $manager = $doctrine->getManager();
            $manager->persist($contact);
            $manager->flush();

            return $this->redirectToRoute('home');
        }
        return $this->render('contact/index.html.twig', [
            'controller_name' => 'ContactController',
            'form' => $form->createView(),
        ]);
    }
}
