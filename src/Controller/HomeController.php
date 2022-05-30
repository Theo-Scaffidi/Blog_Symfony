<?php

namespace App\Controller;

use App\Repository\RecipesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(RecipesRepository $recipesRepository): Response
    {
        $recipes = $recipesRepository->findAll();
        $recipesDate = $recipesRepository->findBy(array(), array('date'=>'desc'));
        return $this->render('home/index.html.twig', [
            'recipes' => $recipes,
            'recipesDate' => $recipesDate,
        ]);
    }
}
