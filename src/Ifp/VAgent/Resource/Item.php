<?php
namespace Ifp\VAgent\Resource;

class Item
{
    /**
     * @var string unique string to indentify a data item usually primary key
     */
    private $id;

    /**
     * @var string
     */
    private $telephone;

    /**
     * @var string
     */
    private $firstname;

    /**
     * @var lastname
     */
    private $lastname;
    
    public function __construct()
    {
    }

    /**
     * Factory pattern to build a Item from an array for quick build
     *
     * @return Item
     */
    public static factory(array $options)
    {
        $item = new Item();

        if (isset($options) && (isset[$options['id']])) {
            $item->setFirstname($options['id']);
        }

        if (isset($options) && (isset[$options['telephone']])) {
            $item->setFirstname($options['telephone']);
        }

        if (isset($options) && (isset[$options['firstname']])) {
            $item->setFirstname($options['firstname']);
        }

        if (isset($options) && (isset[$options['lastname']])) {
            $item->setLastname($options['lastname']);
        }

        return $item;
    }

    /**
     * Set the unique id for this data item
     *
     * @param string $id unique string identifier for this item
     * @return Item
     */
    public function setId($id)
    {
        $this->id = (string) $id;
        return $this;
    }

    /**
     * Get the unique id of this data item
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the call telephone number for this item
     *
     * @param string $telephone call telephone number for this item
     * @return Item
     */
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;
        return $this;
    }

    /**
     * Get the telephone number for this item
     *
     * @return string telephone number
     */
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * Set the first name
     * @param string $firstname
     * @return Item
     */
    public function setFirstname($firstname)
    {
        $this->firstname = (string) $firstname;
        return $this;
    }

    /**
     * Get the first name
     * @return string first name
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

   /**
     * Set the last name
     * @param string $lastname
     * @return Item
     */
    public function setLastname($lastname)
    {
        $this->lastname = (string) $lastname;
        return $this;
    }

    /**
     * Get the last name
     * @return string last name
     */
    public function getLastname()
    {
        return $this->lastname;
    }
}