<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\User;
use App\Form\RecipesType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\RecipesRepository;
use App\Repository\ContactRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(RecipesRepository $recipesRepository): Response
    {
        $recipes = $recipesRepository->findAll();
        return $this->render('admin/index.html.twig', [
            'recipes' => $recipes,
        ]);
    }

    #[Route('/admin/recettes/modifier/{id}', name: 'modify_recipes')]
    #[IsGranted('ROLE_ADMIN')]
    public function modifyRecipes($id, RecipesRepository $recipesRepository, Request $request, ManagerRegistry $doctrine, SluggerInterface $slugger): Response
    {
        $recipes = $recipesRepository->find($id);
        $recipes->setDate(new \DateTime('now'));
        /**@var User */
        $user = $this->getUser();
        $recipes->setPseudo($user);
        $form = $this->createForm(RecipesType::class, $recipes);
        //recup des datas
        $form->handleRequest($request);
    
        //check si le form est submit
        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('picture')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $recipes->setPicture($newFilename);
            }
            //les envoyer a la bdd
            $manager = $doctrine->getManager();
            $manager->persist($recipes);
            $manager->flush();
            
    
            return $this->redirectToRoute('admin');
        }
        
        return $this->render('admin/modifRecipes.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/recette/supprimer/{id}', name: 'delete_recipes')]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteRecipes($id, RecipesRepository $recipesRepository): Response
    {
        $recipes = $recipesRepository->find($id);
        $recipesRepository->remove($recipes, true);
        return $this->redirectToRoute('admin');
    }

    #[Route('/admin/contact', name: 'admin_contact')]
    #[IsGranted('ROLE_ADMIN')]
    public function contact(ContactRepository $contactRepository): Response
    {
        $contact = $contactRepository->findAll();
        return $this->render('admin/contact.html.twig', [
            'contacts' => $contact,
        ]);
    }
}
