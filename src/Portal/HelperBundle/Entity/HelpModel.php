<?php

namespace Portal\HelperBundle\Entity;

class HelpModel
{
    public function setOptions(array $options)
    {
        $_classMethods = get_class_methods($this);
        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (in_array($method, $_classMethods)) {
                $this->$method($value);
            }
        }
        return $this;
    }

    public function setOption($key, $value){
        return $this->setOptions(array($key => $value));
    }

    public function getOptions($key)
    {
        $_classMethods = get_class_methods($this);
        $method = 'get' . ucfirst($key);
        if (in_array($method, $_classMethods)) {
            return $this->$method();
        }
        return 0;
    }

    public function getOption($key){
        return $this->getOptions($key);
    }
}