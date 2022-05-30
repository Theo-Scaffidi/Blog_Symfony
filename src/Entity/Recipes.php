<?php

namespace App\Entity;

use App\Repository\RecipesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RecipesRepository::class)]
class Recipes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $title;

    #[ORM\Column(type: 'string', length: 255)]
    private $content;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $picture;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'recipes')]
    #[ORM\JoinColumn(nullable: false)]
    private $category;

    #[ORM\OneToMany(mappedBy: 'recipes', targetEntity: Messenger::class)]
    private $messengers;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'recipes')]
    #[ORM\JoinColumn(nullable: false)]
    private $pseudo;

    #[ORM\Column(type: 'datetime')]
    private $date;

    public function __construct()
    {
        $this->messengers = new ArrayCollection();
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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): self
    {
        $this->picture = $picture;

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

    /**
     * @return Collection<int, Messenger>
     */
    public function getMessengers(): Collection
    {
        return $this->messengers;
    }

    public function addMessenger(Messenger $messenger): self
    {
        if (!$this->messengers->contains($messenger)) {
            $this->messengers[] = $messenger;
            $messenger->setRecipes($this);
        }

        return $this;
    }

    public function removeMessenger(Messenger $messenger): self
    {
        if ($this->messengers->removeElement($messenger)) {
            // set the owning side to null (unless already changed)
            if ($messenger->getRecipes() === $this) {
                $messenger->setRecipes(null);
            }
        }

        return $this;
    }

    public function getPseudo(): ?User
    {
        return $this->pseudo;
    }

    public function setPseudo(?User $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }
}
