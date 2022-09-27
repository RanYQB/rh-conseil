<?php

namespace App\Entity\Trait;

use Doctrine\ORM\Mapping as ORM;



trait CivilityTrait
{
    #[ORM\Column(type: 'string' , length: 3)]
    private $civility;

    public function getCivility():string
    {
        return $this->civility;
    }

    public function setCivility(string $civility):self
    {
        $this->civility = $civility;
        return $this;
    }



}
