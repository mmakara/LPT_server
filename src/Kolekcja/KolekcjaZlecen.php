<?php
/**
 * Created by PhpStorm.
 * User: HokuS
 * Date: 21/06/2018
 * Time: 23:41
 */

namespace App\Kolekcja;


use App\Entity\Job;

class KolekcjaZlecen
{
    private $zlecenia;

    public function dodaj($zlecenia)
    {
        if(is_array($zlecenia)) {
            foreach($zlecenia as $zlecenie) {
                $this->zlecenia[] = $zlecenie;
            }
        } else {
            $this->zlecenia[] = $zlecenia;
        }
    }

    public function json()
    {
        $zlecenia = [];

        /** @var Job $zlecenie */
        foreach($this->zlecenia as $zlecenie){
            $zlecenia[] = $zlecenie->toJson();
        }

        return $zlecenia;
    }
}