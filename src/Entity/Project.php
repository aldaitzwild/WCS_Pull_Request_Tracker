<?php

namespace App\Entity;

use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
class Project
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $githubLink = null;

    #[ORM\ManyToMany(targetEntity: Contributor::class, inversedBy: 'projects', orphanRemoval: false)]
    private Collection $contributors;

    #[ORM\OneToMany(mappedBy: 'project', targetEntity: PullRequest::class)]
    private Collection $pullRequests;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;
    #[ORM\Column(length: 255)]
    private ?string $fullName = null;

    #[ORM\Column]
    private bool $isFollowed = true;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->contributors = new ArrayCollection();
        $this->pullRequests = new ArrayCollection();
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

    public function getGithubLink(): ?string
    {
        return $this->githubLink;
    }

    public function setGithubLink(string $githubLink): self
    {
        $this->githubLink = $githubLink;

        return $this;
    }

    /**
     * @return Collection<int, Contributor>
     */
    public function getContributors(): Collection
    {
        return $this->contributors;
    }

    public function addContributor(Contributor $contributor): self
    {
        if (!$this->contributors->contains($contributor)) {
            $this->contributors->add($contributor);
        }

        return $this;
    }

    public function removeContributor(Contributor $contributor): self
    {
        $this->contributors->removeElement($contributor);

        return $this;
    }

    /**
     * @return Collection<int, PullRequest>
     */
    public function getPullRequests(): Collection
    {
        return $this->pullRequests;
    }

    public function addPullRequest(PullRequest $pullRequest): self
    {
        if (!$this->pullRequests->contains($pullRequest)) {
            $this->pullRequests->add($pullRequest);
            $pullRequest->setProject($this);
        }

        return $this;
    }

    public function removePullRequest(PullRequest $pullRequest): self
    {
        if ($this->pullRequests->removeElement($pullRequest)) {
            // set the owning side to null (unless already changed)
            if ($pullRequest->getProject() === $this) {
                $pullRequest->setProject(null);
            }
        }

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

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): self
    {
        $this->fullName = $fullName;
        return $this;
    }

    public function isIsFollowed(): ?bool
    {
        return $this->isFollowed;
    }

    public function setIsFollowed(bool $isFollowed): self
    {
        $this->isFollowed = $isFollowed;

        return $this;
    }
}
