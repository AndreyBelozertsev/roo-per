<?php

namespace Portal\HelperBundle\Model;

use Doctrine\Common\Proxy\Exception\InvalidArgumentException;

trait LoadsProperties
{
    /**
     * Fill's model properties
     *
     * @param array $properties Key-value pairs of properties
     */
    public final function fill($properties)
    {
        foreach($properties as $propertyName => $propertyValue) {
            $this->setProperty($propertyName, $propertyValue);
        }
    }

    /**
     * Sets property by prop name
     *
     * @param string $propertyName
     * @param mixed $propertyValue
     */
    protected function setProperty($propertyName, $propertyValue)
    {
        $setter = 'set' . $propertyName;
        if(in_array($setter, get_class_methods($this))) {
            $this->$setter($propertyValue);
        } else {
            throw new InvalidArgumentException('No such property: '. $propertyName);
        }
    }
}