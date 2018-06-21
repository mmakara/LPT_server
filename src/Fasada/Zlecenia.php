<?php namespace App\Fasada;


use App\Fabryka\FabrykaZlecen;
use App\Repository\JobRepository;
use Symfony\Component\HttpFoundation\Request;

class Zlecenia
{
    /** @var FabrykaZlecen */
    private $fabryka;

    /** @var JobRepository */
    private $repozytorium;

    /**
     * @param FabrykaZlecen $fabryka
     * @param JobRepository $repozytorium
     */
    public function __constructor(FabrykaZlecen $fabryka, JobRepository $repozytorium)
    {
        $this->fabryka = $fabryka;
        $this->repozytorium = $repozytorium;
    }

    public function znajdz_w_poblizu(Request $request)
    {
        $kryteria = $request->request->all();

        return $this->repozytorium->znajdz_w_poblizu($kryteria['lat'], $kryteria['lng'], $kryteria['dystans']);
    }

    public function apiFabryka()
    {
        return $this->fabryka;
    }

    public function apiRepozytorium()
    {
        return $this->repozytorium;
    }

}