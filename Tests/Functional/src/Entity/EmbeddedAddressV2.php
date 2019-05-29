<?php

namespace Joschi127\DoctrineEntityOverrideBundle\Tests\Functional\src\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class EmbeddedAddressV2
 * @package Joschi127\DoctrineEntityOverrideBundle\Tests\Functional\src\Entity
 *
 * @ORM\Embeddable()
 */
class EmbeddedAddressV2 extends EmbeddedAddress {
    /**
     * @var string|null
     * @ORM\Column(type="string",nullable=true)
     */
    protected $zip;

    /**
     * @return string|null
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * @param string|null $zip
     */
    public function setZip(?string $zip)
    {
        $this->zip = $zip;
        return $this;
    }




}
