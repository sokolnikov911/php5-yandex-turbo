<?php

namespace sokolnikov911\YandexTurboPages;

/**
 * Class Item
 * @package sokolnikov911\YandexTurboPages
 */
class Item implements ItemInterface
{
    /** @var string */
    protected $turbo;

    /** @var string */
    protected $title;

    /** @var string */
    protected $link;

    /** @var string */
    protected $category;

    /** @var int */
    protected $pubDate;

    /** @var string */
    protected $author;

    /** @var string */
    protected $turboContent;

    /** @var RelatedItemsListInterface */
    protected $relatedItemsList;

    public function __construct($turbo = true)
    {
        $this->turbo = $turbo;
    }

    public function title($title)
    {
        $this->title = $title;
        return $this;
    }

    public function link($link)
    {
        $this->link = $link;
        return $this;
    }

    public function category($category)
    {
        $this->category = $category;
        return $this;
    }

    public function pubDate($pubDate)
    {
        $this->pubDate = $pubDate;
        return $this;
    }

    public function turboContent($turboContent)
    {
        $this->turboContent = $turboContent;
        return $this;
    }

    public function author($author)
    {
        $this->author = $author;
        return $this;
    }

    public function appendTo(ChannelInterface $channel)
    {
        $channel->addItem($this);
        return $this;
    }

    public function addRelatedItemsList(RelatedItemsListInterface $relatedItemsList)
    {
        $this->relatedItemsList = $relatedItemsList;
        return $this;
    }

    public function asXML()
    {
        $isTurboEnabled = $this->turbo ? 'true' : 'false';

        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?><item turbo="' . $isTurboEnabled
            . '"></item>', LIBXML_NOERROR | LIBXML_ERR_NONE | LIBXML_ERR_FATAL);

        $xml->addChild('title', $this->title);
        $xml->addChild('link', $this->link);
        $xml->addCdataChild('turbo:content', $this->turboContent, 'http://turbo.yandex.ru');
        $xml->addChild('pubDate', date(DATE_RSS, $this->pubDate));

        if (!empty($this->category)) {
            $xml->addChild('category', $this->category);
        }

        if (!empty($this->author)) {
            $xml->addChild('author', $this->author);
        }

        if ($this->relatedItemsList) {
            $toDom = dom_import_simplexml($xml);
            $fromDom = dom_import_simplexml($this->relatedItemsList->asXML());
            $toDom->appendChild($toDom->ownerDocument->importNode($fromDom, true));
        }

        return $xml;
    }
}
