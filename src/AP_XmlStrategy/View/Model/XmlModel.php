<?php
/**
 * @package AP_XmlStrategy (Zend Framework 2 Extensions)
 * @author Alessandro Pietrobelli <alessandro.pietrobelli@gmail.com>
 */

namespace AP_XmlStrategy\View\Model;

use Zend\View\Model\ViewModel;
use Zend\Stdlib\ArrayUtils;
use AP_XmlStrategy\Xml\Xml;

/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage Model
 */
class XmlModel extends ViewModel
{
    /**
     * Xml probably won't need to be captured into a
     * a parent container by default.
     *
     * @var string
     */
    protected $captureTo = null;

    /**
     * Xml is usually terminal
     *
     * @var bool
     */
    protected $terminate = true;

    /**
     * @var string
     */
    protected $encoding = 'utf-8';

    /**
     * @var string
     */
    protected $version = '1.0';

    /**
     * @var string
     */
    protected $rootNode = 'response';

    /**
     * @param string $encoding
     *
     * @return XmlModel
     */
    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;

        return $this;
    }

    /**
     * @return string
     */
    public function getEncoding()
    {
        return $this->encoding;
    }

    /**
     * @param string $encoding
     *
     * @return XmlModel
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param string $rootNode
     */
    public function setRootNode($rootNode)
    {
        $this->rootNode = $rootNode;
    }

    /**
     * @return string
     */
    public function getRootNode()
    {
        return $this->rootNode;
    }

    /**
     * Override serialize()
     *
     * Tests for the special top-level variable "payload", set by ZF\Rest\RestController.
     *
     * If discovered, the value is pulled and used as the variables to serialize.
     *
     * A further check is done to see if we have a ZF\Hal\Entity or
     * ZF\Hal\Collection, and, if so, we pull the top-level entity or
     * collection and serialize that.
     *
     * @return string
     */
    public function serialize()
    {
        $variables = $this->getVariables();

        // 'payload' == ZF\Rest\RestController payload
        if (isset($variables['payload'])) {
            $variables = $variables['payload'];
        }

        // Use ZF\Hal\Entity's composed entity
        if ($variables instanceof \ZF\Hal\Entity) {
            $variables = $variables->entity;
        }

        // Use ZF\Hal\Collection's composed collection
        if ($variables instanceof \ZF\Hal\Collection) {
            $variables = $variables->getCollection();
        }

        if (method_exists($variables, 'getArrayCopy')) {
            $variables = $variables->getArrayCopy();
        }

        if ($variables instanceof \Traversable) {
            $variables = ArrayUtils::iteratorToArray($variables);
        }

        $xml = new Xml();
        return $xml->serialize($variables, 'root');
    }
}
