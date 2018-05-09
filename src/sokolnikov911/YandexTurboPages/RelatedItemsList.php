<?php

namespace sokolnikov911\YandexTurboPages;

/**
 * Class RelatedItemsList
 * @package sokolnikov911\YandexTurboPages
 */
class RelatedItemsList implements RelatedItemsListInterface
{
    /** @var RelatedItemInterface[] */
    protected $relatedItems = [];

    public function appendTo(ItemInterface $item)
    {
        $item->addRelatedItemsList($this);
        return $this;
    }

    public function addItem(RelatedItem $relatedItem)
    {
        $this->relatedItems[] = $relatedItem;
        return $this;
    }

    public function asXML()
    {
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?><yandex:related xmlns:yandex="http://news.yandex.ru"></yandex:related>', LIBXML_NOERROR | LIBXML_ERR_NONE | LIBXML_ERR_FATAL);

        foreach ($this->relatedItems as $item) {
            $toDom = dom_import_simplexml($xml);
            $fromDom = dom_import_simplexml($item->asXML());
            $toDom->appendChild($toDom->ownerDocument->importNode($fromDom, true));
        }

        return $xml;
    }
}
