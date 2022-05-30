<?php

namespace App\Controller;

use App\Entity\Messenger;
use App\Entity\Recipes;
use App\Entity\User;
use App\Form\MessengerFormType;
use App\Repository\RecipesRepository;
use App\Repository\CategoryRepository;
use App\Repository\MessengerRepository;
use App\Form\RecipesType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class RecipesController extends AbstractController
{    
    #[Route('/recettes', name: 'recipes')]
    public function index(RecipesRepository $recipesRepository): Response
    {
        $recipes = $recipesRepository->findAll();
        return $this->render('recipes/index.html.twig', [
            'recipes' => $recipes,
        ]);
    }

    #[Route('/recettes/creation', name: 'create_recipes')]
    // #[IsGranted('ROLE_ADMIN')]
    #[Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_AUTEUR')")]
    public function createRecipes(Request $request, ManagerRegistry $doctrine, SluggerInterface $slugger){
        //afficher le form
        $recipes = new Recipes();
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
            
    
            return $this->redirectToRoute('home');
        }
    
    
        return $this->render('/recipes/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/recettes/{id}', name: 'detail_recipes')]
    public function recipesDetail($id, RecipesRepository $recipesRepository, CategoryRepository $categoryRepository, MessengerRepository $messengerRepository, Request $request, ManagerRegistry $doctrine): Response
    {
        $message = new Messenger();
        $message->setDate(new \DateTime('now'));
        /**@var User */
        $user = $this->getUser();
        
        $recipes = $recipesRepository->find($id);
        $recipesDate = $recipesRepository->findBy(array(), array('date'=>'desc'));
        $category = $categoryRepository->find($recipes->getCategory());
        $message->setRecipes($recipes);
        $message->setPseudo($user);

        $form = $this->createForm(MessengerFormType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //les envoyer a la bdd
            $manager = $doctrine->getManager();
            $manager->persist($message);
            $manager->flush();

            // return $this->redirectToRoute('acteur_list');
        }
        $messenger = $messengerRepository->findBy(array('recipes' => $id));
        // $messengerCount = count($messenger);

        return $this->render('/recipes/detail.html.twig', [
            'recipes' => $recipes,
            'recipesDate' => $recipesDate,
            'category' => $category,
            'messenger' => $messenger,
            // 'messengerCount' => $messengerCount,
            'messengerForm' => $form->createView(),
        ]);
    }
}
