<?php

namespace App\Controller;

use App\Entity\Folder;
use App\Entity\Project;
use App\Form\FolderType;
use App\Form\ProjectType;
use App\Repository\FolderRepository;
use App\Repository\ProjectRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/projects")
 */
class ProjectController extends AbstractController
{
    /**
     * @Route("/{id}", name="project_index", methods={"GET", "POST"})
     */
    public function index(Request $request, ProjectRepository $projectRepository, UserRepository $userRepository, $id): Response
    {
        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);
        $userLogged = $this->getUser();
        $client = $userRepository->find($id);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $project->setClient($client);
            $project->setCreatedBy($userLogged);
            $entityManager->persist($project);
            $entityManager->flush();

            return $this->redirectToRoute('project_index', ['id' => $id]);
        }

        return $this->render('project/index.html.twig', [
            'client' => $client,
            'form' => $form->createView(),
            'projects' => $projectRepository->findBy(['client' => $client]),
        ]);
    }


    /**
     * @Route("/detail/{id}", name="project_show", methods={"GET", "POST"})
     */
    public function show(Request $request, Project $project, FolderRepository $folderRepository): Response
    {
        $folder = new Folder();
        $form = $this->createForm(FolderType::class, $folder);
        $form->handleRequest($request);
        $entityManager = $this->getDoctrine()->getManager();

        // si le client visite le projet pour la premiére fois
        // si client + projet.seen == false
        if ($this->getUser()->hasRole('ROLE_CLIENT') && $project->getSeen() === false) {
            $project->setSeen(1);
            $entityManager->flush();
        }


        if ($form->isSubmitted() && $form->isValid()) {
            $folder->setProject($project);
            $entityManager->persist($project);
            $entityManager->persist($folder);
            $entityManager->flush();

            return $this->redirectToRoute('project_show', ['id' => $project->getId()]);
        }

        return $this->render('project/show.html.twig', [
            'folders' => $folderRepository->findBy(['project' => $project, 'parent' => null]),
            'project' => $project,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="project_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Project $project): Response
    {
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('project_index', ['id' => $project->getClient()->getId()]);
        }

        return $this->render('project/edit.html.twig', [
            'project' => $project,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="project_delete")
     */
    public function delete(Request $request, Project $project): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $id = $project->getClient()->getId();
        $entityManager->remove($project);
        $entityManager->flush();

        return $this->redirectToRoute('project_index', ['id' => $id]);
    }
}
