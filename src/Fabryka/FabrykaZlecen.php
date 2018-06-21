<?php namespace App\Fabryka;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;

class FabrykaZlecen
{
    public static function dlaApi(Request $request, User $uzytkownik) {
        $data  = json_decode($request->getContent(), true);
        $job = new \App\Entity\Job();
        $job->setTitle($data['title']);
        $job->setDescription($data['description']);
        $job->setUser($uzytkownik);
        $job->setLat($data['lat']);
        $job->setLng($data['lng']);
        $job->setCreatedAt(new \DateTime());

        return $job;
    }
}