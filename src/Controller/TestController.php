<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TestController extends Controller
{
    /**
     * @Route("/test", name="test")
     */
    public function index()
    {
        dump("ss");

        $users = $this->getDoctrine()->getRepository('App\Entity\User')->findAll();

//        dump($users);
//
//        die;
//
//        new JsonResponse($users);
//
        return $this->render('test/index.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }
}
