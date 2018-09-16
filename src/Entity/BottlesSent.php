<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BottlesSentRepository")
 */
class BottlesSent
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Bottles", inversedBy="bottlesSent", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $bottle;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Users", inversedBy="bottlesSents")
     */
    private $receivers;

    /**
     * @ORM\Column(type="boolean")
     */
    private $received;

    public function __construct()
    {
        $this->receivers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBottle(): ?Bottles
    {
        return $this->bottle;
    }

    public function setBottle(Bottles $bottle): self
    {
        $this->bottle = $bottle;

        return $this;
    }

    /**
     * @return Collection|Users[]
     */
    public function getReceivers(): Collection
    {
        return $this->receivers;
    }

    public function addReceiver(Users $receiver): self
    {
        if (!$this->receivers->contains($receiver)) {
            $this->receivers[] = $receiver;
        }

        return $this;
    }

    public function removeReceiver(Users $receiver): self
    {
        if ($this->receivers->contains($receiver)) {
            $this->receivers->removeElement($receiver);
        }

        return $this;
    }

    public function getReceived(): ?bool
    {
        return $this->received;
    }

    public function setReceived(bool $received): self
    {
        $this->received = $received;

        return $this;
    }
}
