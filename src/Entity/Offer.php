<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\ExistsFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Entity\Trait\SlugTrait;
use App\Repository\OfferRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: OfferRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection()],
    normalizationContext: ['groups' => ['read']],
    order: ['published_at' => 'DESC'],
    paginationEnabled: true)]
#[ApiFilter(SearchFilter::class, properties: ['slug' => 'partial', 'City.label' => 'exact'])]
#[ApiFilter(ExistsFilter::class, properties: ['published_at'])]
class Offer
{
    use SlugTrait;

    #[Groups('read')]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups('read')]
    #[ORM\Column(length: 150)]
    private ?string $title = null;

    #[Groups('read')]
    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[Groups('read')]
    #[ORM\Column]
    private ?int $salary = null;

    #[Groups('read')]
    #[ORM\Column(length: 3)]
    private ?string $contrat_type = null;

    #[ORM\Column(options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeImmutable $created_at = null;

    #[Groups('read')]
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $published_at = null;

    #[ORM\Column]
    private ?bool $published = null;

    #[ORM\Column]
    private ?bool $closed = null;

    #[Groups('read')]
    #[ORM\ManyToOne(inversedBy: 'offers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Recruiter $recruiter = null;

    #[ORM\OneToMany(mappedBy: 'offer', targetEntity: Application::class, orphanRemoval: true)]
    private Collection $applications;

    #[Groups('read')]
    #[ORM\Column]
    private ?int $positions = null;

    #[Groups('read')]
    #[ORM\ManyToOne(inversedBy: 'offers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?City $City = null;

    public function __construct()
    {
        $this->applications = new ArrayCollection();
        $this->created_at = new \DateTimeImmutable();
    }

    public function getId(): ?int
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

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }


    public function getSalary(): ?int
    {
        return $this->salary;
    }

    public function setSalary(int $salary): self
    {
        $this->salary = $salary;

        return $this;
    }

    public function getContratType(): ?string
    {
        return $this->contrat_type;
    }

    public function setContratType(string $contrat_type): self
    {
        $this->contrat_type = $contrat_type;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getPublishedAt(): ?\DateTimeImmutable
    {
        return $this->published_at;
    }

    public function setPublishedAt(?\DateTimeImmutable $published_at): self
    {
        $this->published_at = $published_at;

        return $this;
    }

    public function isClosed(): ?bool
    {
        return $this->closed;
    }

    public function setClosed(bool $closed): self
    {
        $this->closed = $closed;

        return $this;
    }

    public function isPublished(): ?bool
    {
        return $this->published;
    }

    public function setPublished(bool $published): self
    {
        $this->published = $published;

        return $this;
    }

    public function getRecruiter(): ?Recruiter
    {
        return $this->recruiter;
    }

    public function setRecruiter(?Recruiter $recruiter): self
    {
        $this->recruiter = $recruiter;

        return $this;
    }

    /**
     * @return Collection<int, Application>
     */
    public function getApplications(): Collection
    {
        return $this->applications;
    }

    public function addApplication(Application $application): self
    {
        if (!$this->applications->contains($application)) {
            $this->applications->add($application);
            $application->setOffer($this);
        }

        return $this;
    }

    public function removeApplication(Application $application): self
    {
        if ($this->applications->removeElement($application)) {
            // set the owning side to null (unless already changed)
            if ($application->getOffer() === $this) {
                $application->setOffer(null);
            }
        }

        return $this;
    }

    public function getPositions(): ?int
    {
        return $this->positions;
    }

    public function setPositions(int $positions): self
    {
        $this->positions = $positions;

        return $this;
    }

    public function getCity(): ?City
    {
        return $this->City;
    }

    public function setCity(?City $City): self
    {
        $this->City = $City;

        return $this;
    }
}
