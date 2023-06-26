<?php

namespace App\Entity;

use App\Repository\ContributorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContributorRepository::class)]
class Contributor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $githubAccount = null;

    #[ORM\Column(length: 255)]
    private ?string $githubName = null;

    #[ORM\ManyToMany(targetEntity: Project::class, mappedBy: 'contributors', orphanRemoval: false)]
    private Collection $projects;

    #[ORM\OneToMany(mappedBy: 'contributor', targetEntity: PullRequest::class)]
    private Collection $pullRequests;
    #[ORM\OneToMany(mappedBy: 'contributor', targetEntity: Note::class, orphanRemoval: true)]
    private Collection $notes;

    public function __construct()
    {
        $this->projects = new ArrayCollection();
        $this->pullRequests = new ArrayCollection();
        $this->notes = new ArrayCollection();
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

    public function getGithubAccount(): ?string
    {
        return $this->githubAccount;
    }

    public function setGithubAccount(?string $githubAccount): self
    {
        $this->githubAccount = $githubAccount;

        return $this;
    }

    public function getGithubName(): ?string
    {
        return $this->githubName;
    }

    public function setGithubName(string $githubName): self
    {
        $this->githubName = $githubName;

        return $this;
    }

    /**
     * @return Collection<int, Project>
     */
    public function getProjects(): Collection
    {
        return $this->projects;
    }

    public function addProject(Project $project): self
    {
        if (!$this->projects->contains($project)) {
            $this->projects->add($project);
            $project->addContributor($this);
        }

        return $this;
    }

    public function removeProject(Project $project): self
    {
        if ($this->projects->removeElement($project)) {
            $project->removeContributor($this);
        }

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
            $pullRequest->setContributor($this);
        }

        return $this;
    }

    public function removePullRequest(PullRequest $pullRequest): self
    {
        if ($this->pullRequests->removeElement($pullRequest)) {
            // set the owning side to null (unless already changed)
            if ($pullRequest->getContributor() === $this) {
                $pullRequest->setContributor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Note>
     */
    public function getNotes(): Collection
    {
        return $this->notes;
    }

    public function addNote(Note $note): self
    {
        if (!$this->notes->contains($note)) {
            $this->notes->add($note);
            $note->setContributor($this);
        }

        return $this;
    }

    public function removeNote(Note $note): self
    {
        if ($this->notes->removeElement($note)) {
// set the owning side to null (unless already changed)
            if ($note->getContributor() === $this) {
                $note->setContributor(null);
            }
        }

        return $this;
    }
}
