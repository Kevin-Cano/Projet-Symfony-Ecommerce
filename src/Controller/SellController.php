<?php

namespace App\Controller;

use App\Entity\Watch;
use App\Form\WatchType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class SellController extends AbstractController
{
    #[Route('/sell', name: 'app_sell')]
    public function sell(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $watch = new Watch();
        $form = $this->createForm(WatchType::class, $watch);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pictureFile = $form->get('picture')->getData();
            if ($pictureFile) {
                $originalFilename = pathinfo($pictureFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$pictureFile->guessExtension();

                try {
                    $pictureFile->move(
                        $this->getParameter('watch_pictures_directory'),
                        $newFilename
                    );
                    $watch->setPicture($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image.');
                }
            }

            $em->persist($watch);
            $em->flush();

            $this->addFlash('success', 'Votre montre a été mise en vente avec succès !');
            return $this->redirectToRoute('app_sell');
        }

        return $this->render('sell/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

