<?php

namespace App\ReadModel;

use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\VirtualProperty;

class ListingResult
{
    /**
     * Items on listing result.
     *
     * @var mixed[]
     * @Type("array")
     */
    private $items;

    /**
     * Items total count on listing result.
     *
     * @var int
     * @Type("integer")
     */
    private $totalCount;

    /**
     * Items per page on listing result.
     *
     * @var int
     * @Type("integer")
     */
    private $itemsPerPage;

    /**
     * @param mixed[] $items
     * @param int $totalCount
     * @param int $itemsPerPage
     */
    public function __construct(array $items, int $totalCount, int $itemsPerPage)
    {
        $this->setItems($items);
        $this->setTotalCount($totalCount);
        $this->setItemsPerPage($itemsPerPage);
    }

    /**
     * Get items.
     *
     * @return mixed[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * Set items.
     *
     * @param mixed[] $items
     */
    public function setItems(array $items)
    {
        $this->items = $items;
    }

    /**
     * Add item.
     *
     * @param mixed $item
     */
    public function addItem($item)
    {
        $this->items[] = $item;
    }

    /**
     * Get items total count.
     *
     * @return int
     */
    public function getTotalCount(): int
    {
        return $this->totalCount;
    }

    /**
     * Set items total count.
     *
     * @param int $totalCount
     */
    public function setTotalCount(int $totalCount)
    {
        $this->totalCount = $totalCount;
    }

    /**
     * Get items per page.
     *
     * @return int
     */
    public function getItemsPerPage(): int
    {
        return $this->itemsPerPage;
    }

    /**
     * Set items per page.
     *
     * @param int $itemsPerPage
     */
    public function setItemsPerPage(int $itemsPerPage)
    {
        $this->itemsPerPage = $itemsPerPage;
    }

    /**
     * Get items per page.
     *
     * @return int
     *
     * @VirtualProperty
     * @SerializedName("pages_count")
     */
    public function getPagesCount(): int
    {
        if ($this->itemsPerPage < 1) {
            return 1;
        }

        return ceil($this->totalCount / $this->itemsPerPage);
    }
}
