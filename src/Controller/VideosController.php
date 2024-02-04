<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Videos;
use App\Form\CategorieType;
use App\Form\VideosType;
use App\Repository\CategorieRepository;
use App\Repository\VideosRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/videos')]
class VideosController extends AbstractController
{
    #[Route('/', name: 'app_videos_index', methods: ['GET', 'POST'])]
    public function index(Request $request, VideosRepository $videosRepository, CategorieRepository $categorieRepository,EntityManagerInterface $entityManager): Response
    {

        return $this->render('videos/dashboard.html.twig', [
            'videos' => $videosRepository->findAll(),
            'categories' => $categorieRepository->findAll(),
        ]);

    }

    #[Route('update/{category_id}', name: 'dashboard_categories', methods: ['GET','POST'])]
    public function showShoesByStatus(Request $request,VideosRepository $videosRepository, CategorieRepository $categorieRepository,$category_id,EntityManagerInterface $entityManager): Response
    {

        $categorie = $categorieRepository->find($category_id);

        return $this->render('videos/dashboard.html.twig', [
            'videos' => $videosRepository->findBy(['categorie' => $categorie]),
            'categories' => $categorieRepository->findAll()
        ]);
    }

    #[Route('update/{status}/{id}', name: 'update_status', methods: ['GET','POST'])]
    public function updateStatus(VideosRepository $videosRepository,CategorieRepository $categorieRepository, $status, $id, EntityManagerInterface $entityManager) : Response
    {

        $id = (int)$id;
        $videos = $videosRepository->find($id);

        if (!$videos) {
            throw $this->createNotFoundException('Video not found with id ' . $id);
        }

        $videos->setStatus($status);

        $entityManager->flush();

        return $this->redirectToRoute('app_videos_index');

    }



    #[Route('/form', name: 'app_videos_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $video = new Videos();
        $form = $this->createForm(VideosType::class, $video);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $lien = $video->getEmbedVideoLink();
            preg_match('/\/embed\/([^?]+)/', $lien, $matches);

            if (isset($matches[1])) {
                $videoId = $matches[1];
                $video->setStatus($videoId);
            }

            $entityManager->persist($video);
            $entityManager->flush();

            return $this->redirectToRoute('app_videos_new', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('videos/new.html.twig', [
            'video' => $video,
            'form' => $form,
        ]);
    }




    #[Route('/{id}', name: 'app_videos_show', methods: ['GET'])]
    public function show(Videos $video): Response
    {
        return $this->render('videos/show.html.twig', [
            'video' => $video,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_videos_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Videos $video, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(VideosType::class, $video);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_videos_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('videos/edit.html.twig', [
            'video' => $video,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_videos_delete', methods: ['POST'])]
    public function delete(Request $request, Videos $video, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$video->getId(), $request->request->get('_token'))) {
            $entityManager->remove($video);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_videos_index', [], Response::HTTP_SEE_OTHER);
    }
}
