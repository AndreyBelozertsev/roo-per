<?php

namespace Portal\ContentBundle\Model;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Portal\ContentBundle\Entity\Option;

class OptionManager
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    public $container;

    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    private $em;

    /**
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    private function getOptionRepo()
    {
        return $this->em->getRepository('PortalContentBundle:Option');
    }

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $em
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     *
     * @return void
     */
    public function __construct(ObjectManager $em, ContainerInterface $container)
    {
        $this->em = $em;
        $this->container = $container;
    }

    /**
     * @param array $array
     * @param array|null $orderBy
     * @param int|null   $limit
     * @param int|null   $offset
     *
     * @return \Portal\ContentBundle\Entity\Option
     */
    public function findBy($array, $orderBy = null, $limit = null, $offset = null)
    {
        return $this->getOptionRepo()->findBy($array, $orderBy, $limit, $offset);
    }

    /**
     *
     * @param array $data
     *
     * @return  Option
     */
    public function findOneBy($data)
    {
        return $this->getOptionRepo()->findOneBy($data);
    }

    /**
     * @param $id
     *
     * @return array|null|object
     */
    public function find($id = null)
    {
        if ($id) {
            return $this->getOptionRepo()->find($id);
        } else {
            return $this->getOptionRepo()->findAll();
        }
    }

    /**
     * Set option
     *
     * @param string $name
     * @param array $params
     * @param string $value
     * @return Option
     */
    public function setOption($name, $params = [], $value)
    {
        // Get Option
        $option = $this->getOption($name, $params);
        if (is_null($option)) {
            // create new
            $option = new Option();
            $option->setName(Option::OPTION_START_NAME . $name);
            foreach ($params as $fieldName => $param) {
                $option->setOption($fieldName, $param);
            }
        }
        $option->setValue($value);

        $this->em->persist($option);
        $this->em->flush();

        return $option;
    }

    /**
     * Get option
     *
     * @param string $name
     * @param array $params
     * @param bool $strong
     * @return Option
     */
    public function getOption($name, $params = [], $strong = true)
    {
        return $this->getOptionRepo()->getOption($name, $params, $strong);
    }

    /**
     * Get options
     *
     * @param string $name
     * @param array $params
     * @param bool $strong
     * @return array Option[]
     */
    public function getOptions($name, $params = [], $strong = true)
    {
        return $this->getOptionRepo()->getOptions($name, $params, $strong);
    }

    /**
     * get  options
     *
     * @return array
     */
    private function _getOptions($inputOptionName)
    {
        $options = $this->getOptions($inputOptionName, [], false);
        $result = [];
        foreach ($options as $option) {
            $paramNameArr = explode('.', $option->getName());
            $paramName = end($paramNameArr);
            $optinValue = $option->getValue();
            if (strpos($optinValue, 'array:') !== false) {
                // To array
                $optinValue = str_replace('array:', '', $optinValue);
                $optinValue = explode(',', $optinValue);
            }
            $result[$paramName] = $optinValue;
        }

        return $result;
    }

    /**
     * get  option value
     *
     * @return string
     */
    private function _getOptionValue($inputOptionName)
    {
        $option = $this->getOption($inputOptionName);
        if (!$option instanceof Option) {
            return null;
        }

        return $option->getValue();
    }

    /**
     * @param array $params
     * @param string $inputOptionName
     */
    private function _setOptions($params, $inputOptionName)
    {
        foreach ($params as $paramName => $paramValue) {
            // Get Option
            $optionName = $inputOptionName . '.' . $paramName;
            $option = $this->getOption($optionName);
            if (is_null($option)) {
                // create new
                $option = new Option();
                $option->setName(Option::OPTION_START_NAME . $optionName);
            }
            if (is_array($paramValue)) {
                // Array to string
                $paramValue = 'array:' . implode(',', $paramValue);
            }
            $option->setValue(trim($paramValue));

            $this->em->persist($option);
        }

        $this->em->flush();
    }

    /**
     * get  options
     *
     * @return array
     */
    public function getCommonParamsOptions()
    {
        return $this->_getOptions(Option::OPTION_COMMON);
    }

    /**
     * Set options
     *
     * @param array $params
     */
    public function setCommonParamsOptions($params)
    {
        $this->_setOptions($params, Option::OPTION_COMMON);
    }

    /**
     * get  common option value
     *
     * @return array
     */
    public function getCommonParamsOptionValue($inputOptionName)
    {
        $optionName = Option::OPTION_COMMON . '.' . $inputOptionName;
        $value = $this->_getOptionValue($optionName);

        return (is_null($value)) ? null : (int)$value;
    }
}
