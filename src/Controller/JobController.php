<?php

namespace App\Controller;

use App\Entity\Job;
use App\Form\Job1Type;
use App\Repository\JobRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("/job")
 */
class JobController extends Controller
{
    /**
     * @Route("/", name="job_index", methods="GET")
     */
    public function index(JobRepository $jobRepository): Response
    {
        return $this->render('job/index.html.twig', ['jobs' => $jobRepository->findAll()]);
    }

    /**
     * @Route("/new", name="job_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $job = new Job();
        $form = $this->createForm(Job1Type::class, $job);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($job);
            $em->flush();

            return $this->redirectToRoute('job_index');
        }

        return $this->render('job/new.html.twig', [
            'job' => $job,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/api/new", name="job_new_api", methods="GET|POST")
     */
    public function new_api(Request $request, UserRepository $userRepository)
    {
        $data  = json_decode($request->getContent(), true);
        $now = new \DateTime();

        $user = $userRepository->find($data['user_id']);
        $job = new Job();
        $job->setTitle($data['title']);
        $job->setDescription($data['description']);
        $job->setUser($user);
        $job->setCreatedAt(new \DateTime());

        $em = $this->getDoctrine()->getManager();
        $em->persist($job);
        $em->flush();

        return new JsonResponse($data);
    }


    /**
     * @Route("/{id}", name="job_show", methods="GET")
     */
    public function show(Job $job): Response
    {
        if(!$job->getCreatedAt()) {
            $job->setCreatedAt(new \DateTime());
        }
        return $this->render('job/show.html.twig', ['job' => $job]);
    }

    /**
     * @Route("/{id}/edit", name="job_edit", methods="GET|POST")
     */
    public function edit(Request $request, Job $job): Response
    {
        if(!$job->getCreatedAt()) {
            $job->setCreatedAt(new \DateTime());
        }
        $form = $this->createForm(Job1Type::class, $job);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('job_edit', ['id' => $job->getId()]);
        }

        return $this->render('job/edit.html.twig', [
            'job' => $job,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="job_delete", methods="DELETE")
     */
    public function delete(Request $request, Job $job): Response
    {
        if ($this->isCsrfTokenValid('delete'.$job->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($job);
            $em->flush();
        }

        return $this->redirectToRoute('job_index');
    }

    /**
     * @Route("/get_for_user/{user_id}", name="get_jobs_for_user", methods="GET")
     */
    public function get_for_user(JobRepository $jobRepository, $user_id)
    {
//        $user_jobs = $jobRepository->findBy(['user' => $user_id]);
        $repository = $this->getDoctrine()->getRepository('App\Entity\Job');

        $query = $repository->createQueryBuilder('a')
            ->join('a.user', 'd')
            ->where('d.id= :id')
            ->setParameter('id', $user_id)
            ->getQuery();

        $jobs = $query->getResult();

        $jobs_to_send = [];

        foreach($jobs as $job){
            $jobs_to_send[] = $job->toJson();
        }

        return new JsonResponse($jobs_to_send);
    }

    /**
     * @Route("/get_for_technician/{user_id}", name="get_jobs_for_technician", methods="GET")
     */
    public function get_for_technician(JobRepository $jobRepository, $user_id)
    {
//        $user_jobs = $jobRepository->findBy(['user' => $user_id]);
        $repository = $this->getDoctrine()->getRepository('App\Entity\Job');

        $query = $repository->createQueryBuilder('a')
            ->join('a.technician', 'd')
            ->where('d.id= :id')
            ->setParameter('id', $user_id)
            ->getQuery();

        $jobs = $query->getResult();

        $jobs_to_send = [];

        foreach($jobs as $job){
            $jobs_to_send[] = $job->toJson();
        }

        return new JsonResponse($jobs_to_send);
    }
}
