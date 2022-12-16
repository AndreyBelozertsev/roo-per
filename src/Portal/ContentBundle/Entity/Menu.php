<?php

namespace Portal\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Menu
 *
 * @ORM\Table(name="menu")
 * @ORM\Entity(repositoryClass="Portal\ContentBundle\Repository\MenuRepository")
 */
class Menu
{
    const FIRST_TOP_MENU = "first_top_menu";
    const SECOND_TOP_MENU = "second_top_menu";
    const TOP_MENU_INSTANCE = "top_menu_instance";
    const STRUCTURE_MENU = "structure_menu";
    const OFFICIAL_RESOURCES_MENU = "official_resources_menu";

    const PERMISSIONS_MENU = [
        'create' => 'create_menu_category',
        'edit' => 'edit_menu_category',
        'delete' => 'delete_menu_category'
    ];

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     *
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=150)
     */
    private $code;

//    /**
//     * @var User
//     *
//     * @ORM\ManyToOne(targetEntity="Portal\UserBundle\Entity\User")
//     * @ORM\JoinColumn(name="author_id", referencedColumnName="id")
//     *
//     * */
    
    /**
     * @var integer
     *
     * @ORM\Column(name="author_id", type="integer", nullable=false)
     */
    private $author;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Menu
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return Menu
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set author
     *
     * @param integer $author
     *
     * @return Menu
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return integer
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Menu
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
    
    public function __toString() {
        if ( isset($this->title) ) {
            return $this->title;
        } else {
            return '';
        }
    }
}

