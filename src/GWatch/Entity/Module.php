<?php
namespace GWatch\Entity;
use Symfony\Component\HttpFoundation\File\UploadedFile;
class Module  
{
    /**
     * User id.
     *
     * @var integer
     */
    protected $id;
    /**
     * Module name.
     *
     * @var string
     */
    protected $name;
    /**
     * Module description.
     *
     * @var string
     */
    protected $description;
    /**
     * Chromosomes.
     *
     * @array
     */
    protected $chr;
    /**
     * Tests.
     *
     * @array
     */
    protected $test;
   
   
    
   
    /**
     *  
     */
    public function getId()
    {
        return $this->id;
    }
    public function setId($id)
    {
        $this->id = $id;
    }
    /**
     *  
     */
    public function getName()
    {
        return $this->name;
    }
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     *  
     */
    public function getDescription()
    {
        return $this->description;
    }
    public function setDescription($description)
    {
        $this->description = $description;
    }

        public function getTest()
    {
        return $this->test;
    }
    public function setTest($test)
    {
        $this->test = $test;
    }
    /**
     * 
    */
    public function getChr()
    {
        return $this->chr;
    }
    public function setChr($chr)
    {
        $this->chr = $chr;
    }
 
}