<?php

namespace App\Controller;

use App\Entity\Folder;
use App\Entity\Image;
use App\Form\FolderType;
use App\Form\ImageType;
use App\Repository\FolderRepository;
use App\Repository\ImageRepository;
use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Twig\Mime\NotificationEmail;

/**
 * @Route("/folder")
 */
class FolderController extends AbstractController
{


    /**
     * @Route("/alertClient/{id}", name="alert_client", methods={"GET", "POST"} )
     *
     */
    public function alertClient( Request $request, $id,
                                 ProjectRepository $projectRepository, FolderRepository $folderRepository,
                                 MailerInterface $mailer)
    {
        $folder = $folderRepository->find($id);
        $project = $folder->getProject();
        $client = $project->getClient();
        $email = (new Email())
            ->from(new Address( $_ENV['EMAIL_BOT'] , 'CheckMyDesign Mail BOT'))
            ->to($client->getEmail())
            ->subject('Nouveau images disponbiles')
            ->html("Cher ". $client->getLastName() .'<br> Vous avez des nouveaux images téléchargés sur votre espace client <br> 
                    Veuillez les consulter et nous donner vos retours . <br>
                    Cordialement '. $this->getUser()->getFirstName() );

        $mailer->send($email);

        $json = json_encode($email);
        $response = new Response($json, 200);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }


    /**
     * @Route("/{id}", name="folder_show", methods={"GET", "POST"})
     */
    public function show(Request $request, FolderRepository $folderRepository, $id, ImageRepository $imageRepository): Response
    {
        $childFld = new Folder();
        $form = $this->createForm(FolderType::class, $childFld);
        $form->handleRequest($request);
        $entityManager = $this->getDoctrine()->getManager();

        $folder = $folderRepository->find($id);

        $arrayImgs = new Image();
        $formImage = $this->createForm(ImageType::class, $arrayImgs);
        $formImage->handleRequest($request);

        //submitting images form
        if ($formImage->isSubmitted() && $formImage->isValid()) {
            $images = $formImage->get('images')->getData();
            foreach ($images as $key => $img) {
                $fichier = md5(uniqid()) . '.' . $img->guessExtension();
                $img->move(
                    $this->getParameter('images_directory'),
                    $fichier
                );
                $i = new Image();
                $i->setImageName($fichier);
                $i->setUpdatedAt(new \DateTime('now'));
                $i->setFolder($folder);

                $entityManager->persist($i);
                $entityManager->flush();
            }

            return $this->redirectToRoute('folder_show', ['id' => $folder->getId()]);
        }

        //submitting new folder form
        if ($form->isSubmitted() && $form->isValid()) {
            $childFld->setParent($folder);
            $childFld->setProject($folder->getProject());
            $entityManager->persist($childFld);
            $entityManager->persist($folder);
            $entityManager->flush();

            return $this->redirectToRoute('folder_show', ['id' => $folder->getId()]);
        }

        return $this->render('folder/show.html.twig', [
            'childrens' => $folderRepository->findBy(['parent' => $folder]),
            'images' => $imageRepository->findBy(['folder' => $folder]),
            'folder' => $folder,
            'formPhoto' => $formImage->createView(),
            'form' => $form->createView(),
        ]);
    }



}
