<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Folder;
use App\Entity\Image;
use App\Form\FolderType;
use App\Form\ImageType;
use App\Repository\CommentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/image")
 */
class ImageController extends AbstractController
{
    /**
     * @Route("/edit/{id}", name="show_image", methods={"GET", "POST"})
     */
    public function show(Request $request, Image $image, CommentRepository $commentRepository): Response
    {
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        $comments = $commentRepository->findBy(['image' => $image]);

        $jsonContent = $serializer->serialize($comments, 'json', ['groups' => 'show_comment']);
        return $this->render('image/edit.html.twig', [
            'comts' => $comments,
            'comments' => $jsonContent,
            'image' => $image,
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete_image", methods={"GET", "POST"})
     */
    public function delete(Request $request, Image $image, CommentRepository $commentRepository): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $id = $image->getFolder()->getId();
        $entityManager->remove($image);
        $entityManager->flush();

        return $this->redirectToRoute('folder_show', ['id' => $id]);
    }

    /**
     *
     * @Route("/addComment/{id}", name="add_comment_ajax" , methods= {"GET", "POST"})
     */
    public function addCommentAjax(Request $request, Image $image, CommentRepository $commentRepository)
    {
        $note = $request->get('note');
        $x = $request->get('x');
        $y = $request->get('y');

        $c = $commentRepository->findOneBy(['x' => $x, 'y' => $y]);
        $entityManager = $this->getDoctrine()->getManager();
        if ($c) {
            $c->setNote($note);
            $json = json_encode($c);
        } else {
            $comment = new Comment($x, $y, $note, $image);
            $entityManager->persist($comment);

            $json = json_encode($comment);
        }
        $entityManager->flush();
        $response = new Response($json, 200);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     *
     * @Route("/deleteComment", name="delete_comment_ajax" , methods= {"GET", "POST"})
     */
    public function deleteCommentAjax(Request $request, CommentRepository $commentRepository)
    {
        $x = $request->get('x');
        $y = $request->get('y');
        $c = $commentRepository->findOneBy(['x' => $x, 'y' => $y]);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($c);
        $entityManager->flush();
        $response = new Response($c, 200);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }


}
