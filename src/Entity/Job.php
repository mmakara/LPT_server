<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\JobRepository")
 */
class Job
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     */
    private $technician;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User")
     */
    private $applicants;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $lat;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $lng;

    public function __construct()
    {
        $this->applicants = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getTechnician(): ?User
    {
        return $this->technician;
    }

    public function setTechnician(?User $technician): self
    {
        $this->technician = $technician;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getApplicants(): Collection
    {
        return $this->applicants;
    }

    public function addApplicant(User $applicant): self
    {
        if (!$this->applicants->contains($applicant)) {
            $this->applicants[] = $applicant;
        }

        return $this;
    }

    public function removeApplicant(User $applicant): self
    {
        if ($this->applicants->contains($applicant)) {
            $this->applicants->removeElement($applicant);
        }

        return $this;
    }

    public function toJson()
    {
        $json = [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'user' => $this->user->toJson() ?? null,
        ];

        if($this->technician) {
            $json['technician'] = $this->technician->toJson() ?? null;
        }

        foreach($this->applicants as $applicant) {
            $json['applicants'][] = $applicant->toJson();
        }
        return $json;
    }

    public function getLat(): ?float
    {
        return $this->lat;
    }

    public function setLat(float $lat): self
    {
        $this->lat = $lat;

        return $this;
    }

    public function getLng(): ?float
    {
        return $this->lng;
    }

    public function setLng(float $lng): self
    {
        $this->lng = $lng;

        return $this;
    }
}
