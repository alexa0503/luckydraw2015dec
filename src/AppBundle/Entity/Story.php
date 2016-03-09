<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Story
 *
 * @ORM\Table(name="t_story")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\StoryRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Story
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var File
     *
     * @ORM\OneToMany(targetEntity="File", mappedBy="story", cascade={"persist", "remove"})
     *
     */
    private $files;

    /**
     * @var ArrayCollection
     */
    private $uploadedFiles;

     /**
     * @ORM\Column(name="username",type="string", length=40)
     */
    protected $username;
    /**
     * @ORM\Column(name="like_num",type="integer")
     */
    protected $likeNum;

    /**
     * @ORM\Column(name="head_img",type="string", length=200,nullable=true)
     * @Assert\File(
     *     maxSize = "5M",
     *     mimeTypes = {"image/jpeg", "image/gif", "image/png", "image/tiff"},
     *     maxSizeMessage = "图片最大只能为5M.",
     *     mimeTypesMessage = "只能上传图片."
     * )
     */
    protected $headImg;
     /**
     * @ORM\Column(name="wish_title",type="string", length=500, nullable = true)
     */
    protected $wishTitle;
    /**
     * @ORM\Column(name="wish_text",type="string", length=500, nullable = true)
     */
    protected $wishText;

    /**
     * @ORM\Column(name="create_time",type="datetime")
     */
    protected $createTime;
    /**
     * @ORM\Column(name="create_ip",type="string", length=60)
     */
    protected $createIp;

    public function __construct() {
        $this->files = new ArrayCollection();
        $this->uploadedFiles = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }


    public function getFiles() {
        return $this->files;
    }
    public function setFiles(array $files) {
        $this->files = $files;
    }

    /**
     * @return ArrayCollection
     */
    public function getUploadedFiles()
    {
        return $this->uploadedFiles;
    }

    /**
     * @param ArrayCollection $uploadedFiles
     */
    public function setUploadedFiles($uploadedFiles)
    {
        $this->uploadedFiles = $uploadedFiles;
    }

    /**
     * @ORM\PreFlush()
     */
    public function upload()
    {
        foreach($this->uploadedFiles as $uploadedFile)
        {
            if ($uploadedFile) {
                $file = new File($uploadedFile);

                $this->getFiles()->add($file);
                $file->setStory($this);

                unset($uploadedFile);
            }
        }
    }

    /**
     * Add files
     *
     * @param \AppBundle\Entity\File $files
     * @return Story
     */
    public function addFile(\AppBundle\Entity\File $files)
    {
        $this->files[] = $files;

        return $this;
    }

    /**
     * Remove files
     *
     * @param \AppBundle\Entity\File $files
     */
    public function removeFile(\AppBundle\Entity\File $files)
    {
        $this->files->removeElement($files);
    }

    /**
     * Set username
     *
     * @param string $username
     * @return Story
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string 
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set likeNum
     *
     * @param integer $likeNum
     * @return Story
     */
    public function setLikeNum($likeNum)
    {
        $this->likeNum = $likeNum;

        return $this;
    }

    /**
     * Get likeNum
     *
     * @return integer 
     */
    public function getLikeNum()
    {
        return $this->likeNum;
    }

    /**
     * Set headImg
     *
     * @param string $headImg
     * @return Story
     */
    public function setHeadImg($headImg)
    {
        $this->headImg = $headImg;

        return $this;
    }

    /**
     * Get headImg
     *
     * @return string 
     */
    public function getHeadImg()
    {
        return $this->headImg;
    }

    /**
     * Set wishTitle
     *
     * @param string $wishTitle
     * @return Story
     */
    public function setWishTitle($wishTitle)
    {
        $this->wishTitle = $wishTitle;

        return $this;
    }

    /**
     * Get wishTitle
     *
     * @return string 
     */
    public function getWishTitle()
    {
        return $this->wishTitle;
    }

    /**
     * Set wishText
     *
     * @param string $wishText
     * @return Story
     */
    public function setWishText($wishText)
    {
        $this->wishText = $wishText;

        return $this;
    }

    /**
     * Get wishText
     *
     * @return string 
     */
    public function getWishText()
    {
        return $this->wishText;
    }

    /**
     * Set createTime
     *
     * @param \DateTime $createTime
     * @return Story
     */
    public function setCreateTime($createTime)
    {
        $this->createTime = $createTime;

        return $this;
    }

    /**
     * Get createTime
     *
     * @return \DateTime 
     */
    public function getCreateTime()
    {
        return $this->createTime;
    }

    /**
     * Set createIp
     *
     * @param string $createIp
     * @return Story
     */
    public function setCreateIp($createIp)
    {
        $this->createIp = $createIp;

        return $this;
    }

    /**
     * Get createIp
     *
     * @return string 
     */
    public function getCreateIp()
    {
        return $this->createIp;
    }
}
