<?php

namespace App\Entity;

use App\Entity\Activite;
use App\Entity\Structure;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\DifficulteRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=DifficulteRepository::class)
 */
class Difficulte
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     * @Groups({"difficulte:read" , "structure:diff" ,"structure:active"})
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     * @var string A "d-m-y " formatted value
     * @Groups({"difficulte:read"})
     */
    private $createdAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"difficulte:read"})
     */
    private $cause;

    /**
     * @ORM\ManyToOne(targetEntity=Activite::class, inversedBy="difficulte" ,cascade="persist")
     * @Groups({"difficulte:read"})
     */
    private $activite;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"structure:diff"})
     */
    private $semaine;

    /**
     * @ORM\ManyToOne(targetEntity=Structure::class, inversedBy="difficulte")
     */
    private $structure;

    public function __constructor()
    {
        //$this->createdAt = new \Datetime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCause(): ?string
    {
        return $this->cause;
    }

    public function setCause(string $cause): self
    {
        $this->cause = $cause;

        return $this;
    }

    public function getActivite(): ?Activite
    {
        return $this->activite;
    }

    public function setActivite(?Activite $activite): self
    {
        $this->activite = $activite;

        return $this;
    }

    public function getSemaine(): ?int
    {
        return $this->semaine;
    }

    public function setSemaine(?int $semaine): self
    {
        $this->semaine = $semaine;

        return $this;
    }

    public function getStructure(): ?Structure
    {
        return $this->structure;
    }

    public function setStructure(?Structure $structure): self
    {
        $this->structure = $structure;

        return $this;
    }
}
