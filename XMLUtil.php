<?php

class XMLUtil {

    /** @var  XMLReader */
    protected $XMLReaderInstance;

    protected $asObject = null;
    protected $asArray = null;
    /**
     * (PHP 5 &gt;= 5.1.2)<br/>
     * Set the URI containing the XML to parse
     * @link http://php.net/manual/en/xmlreader.open.php
     * @param string $URI <p>
     * URI pointing to the document.
     * </p>
     * @param string $encoding [optional] <p>
     * The document encoding or <b>NULL</b>.
     * </p>
     * @param int $options [optional] <p>
     * A bitmask of the LIBXML_*
     * constants.
     * </p>
     * @return XMLUtil
     */

    public function __construct($file, $encoding = null, $options = 0)
    {
        $this->XMLReaderInstance = new XMLReader();
        $this->XMLReaderInstance->open($file, $encoding, $options);
        return $this;
    }

    /**
     * @param $file
     * @param bool $clean
     * @return stdClass|string
     */
    static public function getAsObject($file, $clean = true) {
        $XMLUtil = new XMLUtil($file);
        return $XMLUtil->toObject();
    }

    /**
     * @param $file
     * @param bool $clean
     * @return array|string
     */
    static public function getAsArray($file, $clean = true) {
        $XMLUtil = new XMLUtil($file);
        return $XMLUtil->toArray();
    }

    /**
     * @param XMLReader $xml
     * @param bool $clean
     * @return stdClass|string
     */
    protected function toObject($clean = true) {
        $xml = $this->XMLReaderInstance;
        $tree = new stdClass();
        while($xml->read())
            switch ($xml->nodeType) {
                case XMLReader::END_ELEMENT:
                    return $tree;
                case XMLReader::ELEMENT:
                    $key = $clean ? $this->cleanNamespace($xml->name) : $xml->name;
                    if (isset($tree->$key)) {
                        if (!is_array($tree->$key)) {
                            $value = $tree->$key;
                            $tree->$key = array();
                            array_push($tree->$key,$value);
                        }
                        $value = $xml->isEmptyElement ? '' : $this->toObject($xml);
                        array_push($tree->$key,$value);
                    } else {
                        $tree->$key = $xml->isEmptyElement ? '' : $this->toObject($xml);
                    }
                    break;
                case XMLReader::TEXT:
                case XMLReader::CDATA:
                    $tree = $xml->value;
            }
        return $tree;
    }

    /**
     * @param XMLReader $xml
     * @param bool $clean
     * @return array|string
     */
    protected function toArray($clean = true) {
        $xml = $this->XMLReaderInstance;
        $tree = array();
        while($xml->read())
            switch ($xml->nodeType) {
                case XMLReader::END_ELEMENT:
                    return $tree;
                case XMLReader::ELEMENT:
                    $key = $clean ? $this->cleanNamespace($xml->name) : $xml->name;
                    if (array_key_exists($key, $tree)) {
                        if (!is_array($tree[$key])) {
                            $value = $tree[$key];
                            $tree[$key] = array();
                            array_push($tree[$key],$value);
                        }
                        $value = $xml->isEmptyElement ? '' : $this->toArray($xml);
                        array_push($tree[$key],$value);
                    } else {
                        $tree[$key] = $xml->isEmptyElement ? '' : $this->toArray($xml);
                    }
                    break;
                case XMLReader::TEXT:
                case XMLReader::CDATA:
                    $tree = $xml->value;
            }
        return $tree;
    }

    /**
     * Clean namespaces from key
     * @param $key
     * @return mixed
     */
    protected function cleanNamespace($key)
    {
        if (preg_match('#^(([a-zA-Z\d]+):)?([a-zA-Z\d]+)$#', $key , $matches))
        {
            $key = $matches[3];
        }
        return $key;
    }
}