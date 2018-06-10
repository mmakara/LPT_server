<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user")
 */
class UserController extends Controller
{
    /**
     * @Route("/", name="user_index", methods="GET")
     */
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', ['users' => $userRepository->findAll()]);
    }


    public function search(Request $request)
    {
        $data  = json_decode($request->getContent(), true);
        //lat lng :)
        $distance = 30; //km
        // 6371 earth circle in km/m ; )

        return new JsonResponse(['in_search' => true]);


        $sql = "
                SELECT
            first_name, lat, lng, id, (
              6371 * acos (
              cos ( radians($lat) )
              * cos( radians( lat ) )
              * cos( radians( lng ) - radians($lng) )
              + sin ( radians($lat) )
              * sin( radians( lat ) )
            )
                ) AS distance
                FROM user
                HAVING distance < $distance
                ORDER BY distance
                LIMIT 0 , 20;
        ";

        $em = $this->getDoctrine()->getManager();
        $stmt = $em->getConnection()->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }


    /**
     * @Route("/new", name="user_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $user = new User();

        $data  = json_decode($request->getContent(), true);

        $now = new \DateTime();
        $user->setUsername($data['username']);
        $user->setFirstName($data['first_name']);
        $user->setEmail($data['email']);
        $user->setPassword($data['password']);
        $user->setAccountType($data['rodzaj_konta']);
        $user->setCreatedAt($now);
        $user->setUpdatedAt($now);

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        return new JsonResponse($data);


        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

//        die("ok");
        if ($form->isSubmitted() && $form->isValid()) {
            return new JsonResponse(['username'=>'git3']);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('user_index');
        } else {
            return new JsonResponse($request->request->all());
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_show", methods="GET")
     */
    public function show(User $user)
//    : Response
    {

        return new JsonResponse($user->toJson());
//
//        var_dump($user);
//        die;
        return $this->render('user/show.html.twig', ['user' => $user]);
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods="GET|POST")
     */
    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_edit', ['id' => $user->getId()]);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_delete", methods="DELETE")
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();
        }

        return $this->redirectToRoute('user_index');
    }
}
