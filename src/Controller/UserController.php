<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\User1Type;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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

    /**
     * @Route("/new", name="user_new", methods="GET|POST")
     */
    public function new(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $user = new User();

        $data  = json_decode($request->getContent(), true);

        $now = new \DateTime();


        $user->setUsername($data['username']);
        $user->setFirstName($data['first_name']);
        $user->setEmail($data['email']);
        $user->setLat($data['lat']);
        $user->setLng($data['lng']);
        $user->setAccountType($data['rodzaj_konta']);
        $user->setCreatedAt($now);
        $user->setUpdatedAt($now);

        $encoded = $encoder->encodePassword($user, $data['password']);

        $user->setPassword($encoded);
//        return new JsonResponse(['userpw'=>$user->getPassword(), 'spacing'=>'sssssssssssssssssssssssssss sssssssssssssssssssssssssss', "$encoded" => $encoded]);

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        return new JsonResponse($data);
    }

    public function login(Request $request): Response
    {
        $data  = json_decode($request->getContent(), true);

        /** @var User $user */
        $user = $this->getDoctrine()->getRepository('App\Entity\User')
                ->findOneBy(['username'=>$data['username']]);

//        $encoded = $encoder->encodePassword($user, $user->getPassword());
        $user->setPassword($data['password']);
//        $encoded_login = $encoder->encodePassword($user, $data['password']);

//        $validPassword = $encoder->isPasswordValid($user, $encoded_login);

        return new JsonResponse($user->toJson());

        if($user) {
            $encoded_new = $encoder->encodePassword($user, $data['password']);


        }

        return new JsonResponse([ 'status'=> $validPassword, 'password' => $data['password'], 'encpassed' => $encoded_pass, 'orgpass' => $user->getPassword()]);
    }

    private function login_good_response()
    {
        return [
            'status' => 'bad'
        ];
    }

    /**
     * @Route("/{id}", name="user_show", methods="GET")
     */
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', ['user' => $user]);
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods="GET|POST")
     */
    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(User1Type::class, $user);
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
