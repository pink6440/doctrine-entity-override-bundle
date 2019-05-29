<?php

namespace Joschi127\DoctrineEntityOverrideBundle\Tests\Functional\src\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class EmbeddedAddress
 * @package Joschi127\DoctrineEntityOverrideBundle\Tests\Functional\src\Entity
 *
 * @ORM\Embeddable()
 */
class EmbeddedAddress {
    /**
     * @var string|null
     * @ORM\Column(type="string",nullable=true)
     */
    protected $city;

    /**
     * @return string|null
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string|null $city
     */
    public function setCity(?string $city)
    {
        $this->city = $city;
        return $this;
    }


}
