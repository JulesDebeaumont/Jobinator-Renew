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
     * @ORM\OneToMany(targetEntity=Job::class, mappedBy="recruter", orphanRemoval=true)
     */
    private $jobs;

    public function __construct()
    {
        $this->jobs = new ArrayCollection();
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

    /**
     * @return Collection|Job[]
     */
    public function getJobs(): Collection
    {
        return $this->jobs;
    }

    public function addJob(Job $job): self
    {
        if (!$this->jobs->contains($job)) {
            $this->jobs[] = $job;
            $job->setRecruter($this);
        }

        return $this;
    }

    public function removeJob(Job $job): self
    {
        if ($this->jobs->removeElement($job)) {
            // set the owning side to null (unless already changed)
            if ($job->getRecruter() === $this) {
                $job->setRecruter(null);
            }
        }

        return $this;
    }

}
