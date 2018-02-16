<?php

namespace AppBundle\Entity;

/**
 * OlympicsMedals
 */
class OlympicsMedals
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $title;

    /**
     * @var int
     */
    private $gold;

    /**
     * @var int
     */
    private $silver;

    /**
     * @var int
     */
    private $bronze;


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
     * @return OlympicsMedals
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
     * Set gold
     *
     * @param integer $gold
     *
     * @return OlympicsMedals
     */
    public function setGold($gold)
    {
        $this->gold = $gold;

        return $this;
    }

    /**
     * Get gold
     *
     * @return int
     */
    public function getGold()
    {
        return $this->gold;
    }

    /**
     * Set silver
     *
     * @param integer $silver
     *
     * @return OlympicsMedals
     */
    public function setSilver($silver)
    {
        $this->silver = $silver;

        return $this;
    }

    /**
     * Get silver
     *
     * @return int
     */
    public function getSilver()
    {
        return $this->silver;
    }

    /**
     * Set bronze
     *
     * @param integer $bronze
     *
     * @return OlympicsMedals
     */
    public function setBronze($bronze)
    {
        $this->bronze = $bronze;

        return $this;
    }

    /**
     * Get bronze
     *
     * @return int
     */
    public function getBronze()
    {
        return $this->bronze;
    }
}

