<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=CommentRepository::class)
 */
class Comment
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string")
     */
    private $id;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Groups({"show_comment"})
     */
    private $x;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Groups({"show_comment"})
     */
    private $y;

    /**
     * @ORM\Column(type="string", length=555555, nullable=true)
     * @Groups({"show_comment"})
     */
    private $note;

    /**
     * @ORM\ManyToOne(targetEntity="Image")
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id")
     */
    private $image;

    /**
     * Comment constructor.
     * @param $x
     * @param $y
     * @param $note
     * @param $image
     */
    public function __construct($x, $y, $note, $image)
    {
        $this->setId(uniqid());
        $this->x = $x;
        $this->y = $y;
        $this->note = $note;
        $this->image = $image;
    }


    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }


    /**
     * @return mixed
     */
    public function getX()
    {
        return $this->x;
    }

    /**
     * @param mixed $x
     */
    public function setX($x): void
    {
        $this->x = $x;
    }


    /**
     * @return mixed
     */
    public function getY()
    {
        return $this->y;
    }

    /**
     * @param mixed $y
     */
    public function setY($y): void
    {
        $this->y = $y;
    }


    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): self
    {
        $this->note = $note;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param mixed $image
     */
    public function setImage($image): void
    {
        $this->image = $image;
    }

    public function serializer()
    {
        $encoder    = new JsonEncoder();
        $normalizer = new ObjectNormalizer();

        $normalizer->setIgnoredAttributes(array(
            'whatever', 'attributes', 'you', 'want', 'to', 'ignore'
        ));

        // The setCircularReferenceLimit() method of this normalizer sets the number
        // of times it will serialize the same object
        // before considering it a circular reference. Its default value is 1.
        $normalizer->setCircularReferenceHandler(function ($object) {
            return $object->getImageName();
        });

        $serializer = new Serializer(array($normalizer), array($encoder));
        return $serializer->serialize($this, 'json');
    }

    public function __toString()
    {
        return $this->getId() . '-' .$this->getNote() . ' ('. $this->getX() .','. $this->getY(). ')' ;
    }


}
