<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\Message1Type;
use App\Repository\MessageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("/message")
 */
class MessageController extends Controller
{
    /**
     * @Route("/", name="message_index", methods="GET")
     */
    public function index(MessageRepository $messageRepository): Response
    {
        return $this->render('message/index.html.twig', ['messages' => $messageRepository->findAll()]);
    }

    /**
     * @Route("/new", name="message_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $message = new Message();
        $form = $this->createForm(Message1Type::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($message);
            $em->flush();

            return $this->redirectToRoute('message_index');
        }

        return $this->render('message/new.html.twig', [
            'message' => $message,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/get_to_user/{user_id}", name="get_messages_for_user", methods="GET")
     */
    public function get_for_user(MessageRepository $jobRepository, $user_id)
    {
//        $user_jobs = $jobRepository->findBy(['user' => $user_id]);
        $repository = $this->getDoctrine()->getRepository('App\Entity\Message');

        $query = $repository->createQueryBuilder('a')
            ->join('a.to_user', 'd')
            ->where('d.id= :id')
            ->setParameter('id', $user_id)
            ->getQuery();

        $messages = $query->getResult();

        $messages_to_send = [];

        foreach($messages as $job){
            $messages_to_send[] = $job->toJson();
        }

        return new JsonResponse($messages_to_send);
    }

    /**
     * @Route("/get_from_user/{user_id}", name="get_messages_from_user", methods="GET")
     */
    public function get_sent_for_user($user_id): Response
    {
        $repository = $this->getDoctrine()->getRepository('App\Entity\Message');

        $query = $repository->createQueryBuilder('a')
            ->join('a.to_user', 'd')
            ->where('d.id= :id')
            ->setParameter('id', $user_id)
            ->getQuery();

        $messages = $query->getResult();

        $messages_to_send = [];

        foreach($messages as $job){
            $messages_to_send[] = $job->toJson();
        }

        return new JsonResponse($messages_to_send);
    }

    /**
     * @Route("/{id}", name="message_show", methods="GET")
     */
    public function show(Message $message): Response
    {
        return $this->render('message/show.html.twig', ['message' => $message]);
    }

    /**
     * @Route("/{id}/edit", name="message_edit", methods="GET|POST")
     */
    public function edit(Request $request, Message $message): Response
    {
        $form = $this->createForm(Message1Type::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('message_edit', ['id' => $message->getId()]);
        }

        return $this->render('message/edit.html.twig', [
            'message' => $message,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="message_delete", methods="DELETE")
     */
    public function delete(Request $request, Message $message): Response
    {
        if ($this->isCsrfTokenValid('delete'.$message->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($message);
            $em->flush();
        }

        return $this->redirectToRoute('message_index');
    }
}
