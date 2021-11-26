<?php

namespace App\Entity;

use App\Repository\ApplicationRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ApplicationRepository::class)
 */
class Application
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text", length=255, nullable=true)
     */
    #[Assert\Length(['max' => 255])]
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity=Job::class, inversedBy="applications")
     * @ORM\JoinColumn(nullable=false)
     */
    #[Assert\NotNull()]
    private $job;

    /**
     * @ORM\ManyToOne(targetEntity=Candidat::class, inversedBy="applications")
     * @ORM\JoinColumn(nullable=false)
     */
    #[Assert\NotNull()]
    private $candidat;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    #[Assert\DateTime()]
    private $createdAt;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getJob(): ?Job
    {
        return $this->job;
    }

    public function setJob(?Job $job): self
    {
        $this->job = $job;

        return $this;
    }

    public function getCandidat(): ?Candidat
    {
        return $this->candidat;
    }

    public function setCandidat(?Candidat $candidat): self
    {
        $this->candidat = $candidat;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
