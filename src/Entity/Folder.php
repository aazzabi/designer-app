<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use App\Repository\FolderRepository;

/**
 * @ORM\Entity(repositoryClass=FolderRepository::class)
 */
class Folder
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
     * @ORM\Column(type="string", length=555555)
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

//
//    /**
//     * @ORM\OneToMany(targetEntity="Image", mappedBy="folder", orphanRemoval=true, )
//     */
//    private $images;

//    /**
//     * @ORM\OneToMany(targetEntity="Folder", mappedBy="parent")
//     */
//    private $children;

    /**
     * @ORM\ManyToOne(targetEntity="Folder", inversedBy="children", cascade={"remove"})
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id",  onDelete="CASCADE")
     */
    private $parent;

    /**
     * @ORM\ManyToOne(targetEntity="Project", cascade={"remove"})
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id",  onDelete="CASCADE")
     */
    private $project;

    public function __construct()
    {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
        $this->images = new ArrayCollection();
        $this->createdAt = new \DateTime();
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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
//
//    /**
//     * @return mixed
//     */
//    public function getImages()
//    {
//        return $this->images;
//    }
//
//    /**
//     * @param mixed $images
//     */
//    public function setImages($images): void
//    {
//        $this->images = $images;
//    }
//
//    public function addImage(Image $img)
//    {
//        if (!$this->images->contains($img)) {
//            $this->images->add($img);
//            $img->setFolder($this);
//        }
//        return $this;
//    }
//
//    public function removeImage($img)
//    {
//        $this->images->removeElement($img);
//    }
//
//    /**
//     * @return mixed
//     */
//    public function getChildren()
//    {
//        return $this->children;
//    }
//
//    /**
//     * @param mixed $children
//     */
//    public function setChildren($children): void
//    {
//        $this->children = $children;
//    }
//
//    public function addChildren($fld)
//    {
//        $this->children->add($fld);
//        $fld->setParent($this);
//        return $this;
//
//    }
//
//    public function removeChildren($img)
//    {
//        $this->children->removeElement($img);
//    }

    /**
     * @return mixed
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param mixed $parent
     */
    public function setParent($parent): void
    {
        $this->parent = $parent;
    }

    /**
     * @return mixed
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * @param mixed $project
     */
    public function setProject($project): void
    {
        $this->project = $project;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description): void
    {
        $this->description = $description;
    }

    public function __toString()
    {
        return $this->getName();
    }


}
