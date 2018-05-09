<?php

namespace sokolnikov911\YandexTurboPages;

use SimpleXMLElement as SimpleXMLE;

/**
 * Class SimpleXMLElement
 * @package sokolnikov911\YandexTurboPages
 */
class SimpleXMLElement extends SimpleXMLE
{
    /**
     * @param string $name
     * @param string $value
     * @param string $namespace
     * @return SimpleXMLE
     */
    public function addCdataChild($name, $value = null, $namespace = null)
    {
        $element = $this->addChild($name, null, $namespace);
        $dom = dom_import_simplexml($element);
        $elementOwner = $dom->ownerDocument;
        $dom->appendChild($elementOwner->createCDATASection($value));
        return $element;
    }

    /**
     * Create Child with required Value
     * @param string $name
     * @param string $value
     * @param string|null $namespace
     * @return SimpleXMLE|bool
     */
    public function addChildWithValueChecking($name, $value = null, $namespace = null)
    {
        if ($value !== null) {
            return $this->addChild($name, $value, $namespace);
        }

        return false;
    }
}
