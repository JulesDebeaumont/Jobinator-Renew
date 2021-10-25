<?php

namespace App\Entity;

use App\Repository\RecruterRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RecruterRepository::class)
 */
class Recruter extends User
{
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $company;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $roleInCompany;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $mailCompany;

    /**
     * @ORM\OneToMany(targetEntity=Job::class, mappedBy="recruter", orphanRemoval=true)
     */
    private $job;

    public function __construct()
    {
        $this->job = new ArrayCollection();
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

    public function getRoleInCompany(): ?string
    {
        return $this->roleInCompany;
    }

    public function setRoleInCompany(?string $roleInCompany): self
    {
        $this->roleInCompany = $roleInCompany;

        return $this;
    }

    public function getMailCompany(): ?string
    {
        return $this->mailCompany;
    }

    public function setMailCompany(?string $mailCompany): self
    {
        $this->mailCompany = $mailCompany;

        return $this;
    }

    /**
     * @return Collection|Job[]
     */
    public function getJob(): Collection
    {
        return $this->job;
    }

    public function addJob(Job $job): self
    {
        if (!$this->job->contains($job)) {
            $this->job[] = $job;
            $job->setRecruter($this);
        }

        return $this;
    }

    public function removeJob(Job $job): self
    {
        if ($this->job->removeElement($job)) {
            // set the owning side to null (unless already changed)
            if ($job->getRecruter() === $this) {
                $job->setRecruter(null);
            }
        }

        return $this;
    }
}
