<?php

namespace App\Controller;

use App\Entity\Smartphone;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Form\SmartphoneType;
use App\Repository\SmartphoneRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



#[Route('/smartphone')]
class SmartphoneController extends AbstractController
{
    #[Route('/', name: 'app_smartphone_index', methods: ['GET'])]
    public function index(SmartphoneRepository $smartphoneRepository): Response
    {
        return $this->render('smartphone/index.html.twig', [
            'smartphones' => $smartphoneRepository->findAll(),
        ]);
    }

    public function calculatePrice($ram, $stockage, $etat)
    {
        
        $ramEnGo = (int)$ram;
        $stockageEnGo = (int)$stockage;
        $prix = $ramEnGo * 30 + $stockageEnGo * 31;
        if($etat === 'DEEE') $prix = ($prix-$prix*0.25);
        if($etat === 'REPARABLE') $prix = ($prix-$prix*0.5);
        if($etat === 'BLOQUE') $prix = ($prix-$prix*0.6);
        if($etat === 'RECONDITIONNE') $prix = ($prix-$prix*0.2);
        if($etat === 'RECONDITIONABLE') $prix = ($prix-$prix*0.6);


        return "$prix euros";
    }

    #[Route('/new', name: 'app_smartphone_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SmartphoneRepository $smartphoneRepository,SluggerInterface $slugger): Response
    {
        $smartphone = new Smartphone();
        $form = $this->createForm(SmartphoneType::class, $smartphone);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
           
            $ram = $form->get('ram')->getData();
            $stockage = $form->get('stockage')->getData();
            $etat = $form->get('etat')->getData();
            $imageFile = $form->get('photo')->getData();

            $prix = $this->calculatePrice($ram, $stockage, $etat);

    
            $smartphone->setPrix($prix);

            
            if ($imageFile) {
                $originalImageName = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeImageName = 'uploads/images/'. $slugger->slug($originalImageName) . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $safeImageName
                    );

                    $smartphone->setPhoto($safeImageName);
                } catch (FileException $e) {
                    die("erreur de chargement de l'image !!");
                }
            }
            $smartphoneRepository->save($smartphone, true);

            return $this->redirectToRoute('app_smartphone_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('smartphone/new.html.twig', [
            'smartphone' => $smartphone,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_smartphone_show', methods: ['GET'])]
    public function show(Smartphone $smartphone): Response
    {
        return $this->render('smartphone/show.html.twig', [
            'smartphone' => $smartphone,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_smartphone_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Smartphone $smartphone, SmartphoneRepository $smartphoneRepository): Response
    {
        $form = $this->createForm(SmartphoneType::class, $smartphone);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $smartphoneRepository->save($smartphone, true);

            return $this->redirectToRoute('app_smartphone_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('smartphone/edit.html.twig', [
            'smartphone' => $smartphone,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_smartphone_delete', methods: ['POST'])]
    public function delete(Request $request, Smartphone $smartphone, SmartphoneRepository $smartphoneRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$smartphone->getId(), $request->request->get('_token'))) {
            $smartphoneRepository->remove($smartphone, true);
        }

        return $this->redirectToRoute('app_smartphone_index', [], Response::HTTP_SEE_OTHER);
    }
}
