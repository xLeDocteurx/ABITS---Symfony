<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UsersRepository")
 */
class Users
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
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $country;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $bio;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\BottlesSent", mappedBy="receivers")
     */
    private $bottlesSents;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Posts", mappedBy="author")
     */
    private $posts;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Bottles", mappedBy="author")
     */
    private $bottles;

    /**
     * @ORM\Column(type="boolean")
     */
    private $confirmed;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comments", mappedBy="author", orphanRemoval=true)
     */
    private $comments;

    public function __construct()
    {
        $this->bottlesSents = new ArrayCollection();
        $this->posts = new ArrayCollection();
        $this->bottles = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(?string $bio): self
    {
        $this->bio = $bio;

        return $this;
    }

    /**
     * @return Collection|BottlesSent[]
     */
    public function getBottlesSents(): Collection
    {
        return $this->bottlesSents;
    }

    public function addBottlesSent(BottlesSent $bottlesSent): self
    {
        if (!$this->bottlesSents->contains($bottlesSent)) {
            $this->bottlesSents[] = $bottlesSent;
            $bottlesSent->addReceiver($this);
        }

        return $this;
    }

    public function removeBottlesSent(BottlesSent $bottlesSent): self
    {
        if ($this->bottlesSents->contains($bottlesSent)) {
            $this->bottlesSents->removeElement($bottlesSent);
            $bottlesSent->removeReceiver($this);
        }

        return $this;
    }

    /**
     * @return Collection|Posts[]
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Posts $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->setAuthor($this);
        }

        return $this;
    }

    public function removePost(Posts $post): self
    {
        if ($this->posts->contains($post)) {
            $this->posts->removeElement($post);
            // set the owning side to null (unless already changed)
            if ($post->getAuthor() === $this) {
                $post->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Bottles[]
     */
    public function getBottles(): Collection
    {
        return $this->bottles;
    }

    public function addBottle(Bottles $bottle): self
    {
        if (!$this->bottles->contains($bottle)) {
            $this->bottles[] = $bottle;
            $bottle->setAuthor($this);
        }

        return $this;
    }

    public function removeBottle(Bottles $bottle): self
    {
        if ($this->bottles->contains($bottle)) {
            $this->bottles->removeElement($bottle);
            // set the owning side to null (unless already changed)
            if ($bottle->getAuthor() === $this) {
                $bottle->setAuthor(null);
            }
        }

        return $this;
    }

    public function getConfirmed(): ?bool
    {
        return $this->confirmed;
    }

    public function setConfirmed(bool $confirmed): self
    {
        $this->confirmed = $confirmed;

        return $this;
    }

    /**
     * @return Collection|Comments[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comments $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setAuthor($this);
        }

        return $this;
    }

    public function removeComment(Comments $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getAuthor() === $this) {
                $comment->setAuthor(null);
            }
        }

        return $this;
    }
}
