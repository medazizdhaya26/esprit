<?php

namespace App\Entity;

use App\Repository\PersonnelRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PersonnelRepository::class)]
class Personnel extends User
{

}
