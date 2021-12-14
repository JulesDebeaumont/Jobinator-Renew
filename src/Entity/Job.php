<?php

namespace App\Entity;

use App\Repository\JobRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=JobRepository::class)
 */
class Job
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="text", length=1000)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $pay;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $location;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isRemote;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $experienceNeeded;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isSignaled = false;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="jobs")
     */
    private $category;

    /**
     * @ORM\ManyToOne(targetEntity=Type::class, inversedBy="jobs")
     */
    private $type;

    /**
     * @ORM\OneToMany(targetEntity=Application::class, mappedBy="job", orphanRemoval=true)
     */
    private $applications;

    /**
     * @ORM\ManyToOne(targetEntity=Recruter::class, inversedBy="jobs")
     * @Gedmo\Blameable(on="create")
     * @ORM\JoinColumn(nullable=false)
     */
    private $recruter;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
     */
    private $departement;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     * @Gedmo\Timestampable(on="update")
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $company;

    /**
     * @Gedmo\Slug(fields={"name", "company"})
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $slug;

    public function __construct()
    {
        $this->applications = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
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

    public function getPay(): ?string
    {
        return $this->pay;
    }

    public function setPay(?string $pay): self
    {
        $this->pay = $pay;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getIsRemote(): ?bool
    {
        return $this->isRemote;
    }

    public function setIsRemote(bool $isRemote): self
    {
        $this->isRemote = $isRemote;

        return $this;
    }

    public function getExperienceNeeded(): ?int
    {
        return $this->experienceNeeded;
    }

    public function setExperienceNeeded(?int $experienceNeeded): self
    {
        $this->experienceNeeded = $experienceNeeded;

        return $this;
    }

    public function getIsSignaled(): ?bool
    {
        return $this->isSignaled;
    }

    public function setIsSignaled(bool $isSignaled): self
    {
        $this->isSignaled = $isSignaled;

        return $this;
    }

    public function getDepartement(): ?string
    {
        return $this->departement;
    }

    public function setDepartement(?string $departement): self
    {
        $this->departement = $departement;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getType(): ?Type
    {
        return $this->type;
    }

    public function setType(?Type $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection|Application[]
     */
    public function getApplications(): Collection
    {
        return $this->applications;
    }

    public function addApplication(Application $application): self
    {
        if (!$this->applications->contains($application)) {
            $this->applications[] = $application;
            $application->setJob($this);
        }

        return $this;
    }

    public function removeApplication(Application $application): self
    {
        if ($this->applications->removeElement($application)) {
            // set the owning side to null (unless already changed)
            if ($application->getJob() === $this) {
                $application->setJob(null);
            }
        }

        return $this;
    }

    public function getRecruter(): ?Recruter
    {
        return $this->recruter;
    }

    public function setRecruter(?Recruter $recruter): self
    {
        $this->recruter = $recruter;

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

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(string $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

}
