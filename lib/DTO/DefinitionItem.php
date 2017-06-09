<?php
/**
 * @package store
 * @author Joachim Doerr
 * @copyright (C) mail@doerr-softwaredevelopment.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class DefinitionItem implements JsonSerializable
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $basePath;

    /**
     * @var string
     */
    private $dataFilePath;

    /**
     * @var array
     */
    private $ymlFiles = array();

    /**
     * @var array
     */
    private $payload = array();

    /**
     * @var array
     */
    private $definitions = array();

    /**
     * @return string
     * @author Joachim Doerr
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return $this
     * @author Joachim Doerr
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     * @author Joachim Doerr
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     * @author Joachim Doerr
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     * @author Joachim Doerr
     */
    public function getBasePath()
    {
        return $this->basePath;
    }

    /**
     * @param string $basePath
     * @return $this
     * @author Joachim Doerr
     */
    public function setBasePath($basePath)
    {
        $this->basePath = $basePath;
        return $this;
    }

    /**
     * @return string
     * @author Joachim Doerr
     */
    public function getDataFilePath()
    {
        return $this->dataFilePath;
    }

    /**
     * @param string $dataFilePath
     * @return $this
     * @author Joachim Doerr
     */
    public function setDataFilePath($dataFilePath)
    {
        $this->dataFilePath = $dataFilePath;
        return $this;
    }

    /**
     * @return array
     * @author Joachim Doerr
     */
    public function getYmlFiles()
    {
        return $this->ymlFiles;
    }

    /**
     * @param array $ymlFiles
     * @return $this
     * @author Joachim Doerr
     */
    public function setYmlFiles($ymlFiles)
    {
        $this->ymlFiles = $ymlFiles;
        return $this;
    }

    /**
     * @param null|string $key
     * @return array
     * @author Joachim Doerr
     */
    public function getPayload($key = null)
    {
        if (!is_null($key) && array_key_exists($key, $this->payload)) {
            return $this->payload[$key];
        }
        return $this->payload;
    }

    /**
     * @param array $payload
     * @author Joachim Doerr
     * @return $this
     */
    public function setPayload(array $payload)
    {
        $this->payload = $payload;
        return $this;
    }

    /**
     * @param $key
     * @param $value
     * @author Joachim Doerr
     * @return $this
     */
    public function addPayload($key, $value)
    {
        if (!array_key_exists($key, $this->payload)) {
            $this->payload[$key] = $value;
        } else {
            // todo exception
        }
        return $this;
    }

    /**
     * @param null|string $key
     * @return mixed
     * @author Joachim Doerr
     */
    public function getDefinitions($key = null)
    {
        if (!is_null($key) && array_key_exists($key, $this->definitions)) {
            return $this->definitions[$key];
        }
        return $this->definitions;
    }

    /**
     * @param stdClass|array $definitions
     * @return $this
     * @author Joachim Doerr
     */
    public function setDefinitions($definitions)
    {
        $this->definitions = $definitions;
        return $this;
    }

    /**
     * @param $key
     * @param $value
     * @author Joachim Doerr
     * @return $this
     */
    public function addDefinition($key, $value)
    {
        if (!array_key_exists($key, $this->definitions)) {
            $this->definitions[$key] = $value;
        } else {
            // todo exception
        }
        return $this;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    function jsonSerialize()
    {
        return get_object_vars($this);
    }
}