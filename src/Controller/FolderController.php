<?php

namespace App\Controller;

use App\Entity\Folder;
use App\Entity\Image;
use App\Form\FolderType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/folder")
 */
class FolderController extends AbstractController
{
    /**
     * @Route("/", name="folder_index")
     */
    public function index()
    {
        return $this->render('folder/index.html.twig', [
            'controller_name' => 'FolderController',
        ]);
    }

    /**
     * @Route("/new", name="new")
     */
    public function new(Request $request): Response
    {
        $folder = new Folder();
        $form = $this->createForm(FolderType::class, $folder);
        $form->handleRequest($request);
        $entityManager = $this->getDoctrine()->getManager();

        if ($form->isSubmitted() && $form->isValid()) {
            $images = $form->get('images')->getData();

            foreach ($images as $key => $img) {
                $fichier = md5(uniqid()) . '.' . $img->guessExtension();
                $img->move(
                    $this->getParameter('images_directory'),
                    $fichier
                );
                $i = new Image();
                $i->setImageName($fichier);
                $i->setUpdatedAt(new \DateTime('now'));
                $folder->addImage($i);
                $entityManager->persist($i);
            }

            $entityManager->persist($folder);
            $entityManager->flush();

            return $this->redirectToRoute('folder_index');
        }

        return $this->render('folder/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="folder_show", methods={"GET", "POST"})
     */
    public function show(Request $request, Folder $folder): Response
    {
        $childFld = new Folder();
        $form = $this->createForm(FolderType::class, $childFld);
        $form->handleRequest($request);
        $entityManager = $this->getDoctrine()->getManager();

        if ($form->isSubmitted() && $form->isValid()) {
            $folder->addChildren($childFld);
            $entityManager->persist($childFld);
            $entityManager->persist($folder);
            $entityManager->flush();

            return $this->redirectToRoute('folder_show', ['id' => $folder->getId()]);
        }

        return $this->render('folder/show.html.twig', [
            'folder' => $folder,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}/edit", name="folder_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Folder $folder): Response
    {
        $form = $this->createForm(FolderType::class, $folder);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $images = $folder->getImages();
            foreach ($images as $key => $img) {
                $img->setFolder($folder);
                $img->set($key, $folder);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($folder);
            $entityManager->flush();

            return $this->redirectToRoute('folder_index');
        }

        return $this->render('product/edit.html.twig', [
            'folder' => $folder,
            'form' => $form->createView(),
        ]);
    }
}
