<?php

namespace sokolnikov911\YandexTurboPages;

/**
 * Interface RelatedItemsListInterface
 * @package sokolnikov911\YandexTurboPages
 */
interface RelatedItemsListInterface
{
    /**
     * Append related items list to the item
     * @param ItemInterface $item
     * @return RelatedItemsListInterface
     */
    public function appendTo(ItemInterface $item);

    /**
     * Add related item object
     * @param RelatedItem $relatedItem
     * @return RelatedItemsListInterface
     */
    public function addItem(RelatedItem $relatedItem);

    /**
     * Return XML object
     * @return SimpleXMLElement
     */
    public function asXML();
}
