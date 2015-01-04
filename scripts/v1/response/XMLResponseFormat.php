<?php
namespace meteor\response;

use meteor\core\ResponseFormat;

class XMLResponseFormat extends ResponseFormat
{

    public function getContentType()
    {
        return "application/xml";
    }

    public function render(array $data)
    {
        $xml = new SimpleXMLElement('<response/>');
        $this->array_to_xml($data, $xml);
        return $xml->asXML();
    }

    public function array_to_xml(array $arr, SimpleXMLElement $xml)
    {
        foreach ($arr as $k => $v) {
            is_array($v) ? $this->array_to_xml($v, $xml->addChild($k)) : $xml->addChild($k, $v);
        }

        return $xml;
    }
}