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
    private $phoneNumber;

    /**
     * @var string
     */
    private $firstName;

    /**
     * @var string
     */
    private $lastName;

    /**
     * @var array
     */
    private $customParams;
    
    public function __construct()
    {
    }

    /**
     * Factory pattern to build a Item from an array for quick build
     *
     * @return Item
     */
    public static function factory(array $options)
    {
        $item = new Item();

        if (isset($options) && (isset($options['id']))) {
            $item->setId($options['id']);
        }

        if (isset($options) && (isset($options['phone_number']))) {
            $item->setTelephone($options['phone_number']);
        }

        if (isset($options) && (isset($options['first_number']))) {
            $item->setFirstname($options['first_name']);
        }

        if (isset($options) && (isset($options['last_name']))) {
            $item->setLastname($options['last_name']);
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
     * Set the call phone number for this item
     *
     * @param string $phoneNumber call phone number for this item
     * @return Item
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    /**
     * Get the phone number for this item
     *
     * @return string phone number
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * Set the first name
     * @param string $firstName
     * @return Item
     */
    public function setFirstName($firstName)
    {
        $this->firstName = (string) $firstName;
        return $this;
    }

    /**
     * Get the first name
     * @return string first name
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

   /**
     * Set the last name
     * @param string $lastName
     * @return Item
     */
    public function setLastName($lastName)
    {
        $this->lastName = (string) $lastName;
        return $this;
    }

    /**
     * Get the last name
     * @return string last name
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Get an associative array of custom parameters
     *
     * @return array associative array of custom parameters
     */
    public function getCustomParams()
    {
        return $this->customParams;
    }

    public function setCustomParams(array $params)
    {
        $this->customParams = $params;
        return $this;
    }
}