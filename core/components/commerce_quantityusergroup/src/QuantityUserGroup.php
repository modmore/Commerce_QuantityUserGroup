<?php

namespace modmore\Commerce_QuantityUserGroup;

use comCurrency;
use modmore\Commerce\Pricing\Interfaces\PriceInterface;
use modmore\Commerce\Pricing\Price;
use modmore\Commerce\Pricing\PriceType\Interfaces\ItemPriceTypeInterface;
use modmore\Commerce\Pricing\PriceType\Interfaces\PriceTypeInterface;

final class QuantityUserGroup implements PriceTypeInterface, ItemPriceTypeInterface
{
    /**
     * @var comCurrency
     */
    private comCurrency $currency;

    /**
     * @var array [int $min, int $max, int $mount, string $label, int $usergroup]
     */
    private array $prices = [];
    private int $usergroup = 0;

    public function __construct(comCurrency $currency)
    {
        $this->currency = $currency;
    }

    /**
     * @param int $minQuantity Integer for the minimum
     * @param int|null $maxQuantity Either an integer or a literal `null` to not have a maximum quantity for the bracket
     * @param int $amount Price in cents
     * @param string $label Optional label for quantity pricing
     * @return self
     */
    public function add($minQuantity, $maxQuantity, $amount, string $label = '')
    {
        $this->prices[] = [
            'min' => (int)$minQuantity,
            'max' => $maxQuantity === null ? null : (int)$maxQuantity,
            'amount' => (int)$amount,
            'label' => $label,
            'usergroup' => $this->usergroup,
        ];

        return $this;
    }

    /**
     * @param \comOrderItem $item
     * @return PriceInterface|false
     */
    public function getPriceForItem(\comOrderItem $item)
    {
        $quantity = $item->get('quantity');
        $matchedPrice = false;
        $label = '';
        foreach ($this->prices as $option) {
            // Check if there are enough products for this brackets
            if ($quantity < $option['min']) {
                continue;
            }
            // If we have a max, check if we're still below it
            if ($option['max'] !== null && $quantity > $option['max']) {
                continue;
            }
            $matchedPrice = $option['amount'];
            $label = $option['label'];
        }

        if ($matchedPrice === false) {
            return false;
        }

        $p = new Price($this->currency, $matchedPrice);
        $p->setLabel($label);
        return $p;
    }

    public function getPrices()
    {
        return $this->prices;
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    public function serialize()
    {
        return json_encode($this->prices);
    }

    public static function unserialize(comCurrency $currency, $data)
    {
        $instance = new static($currency);

        $priceTypeData = json_decode($data, true);
        if (!is_array($priceTypeData)) {
            return $instance;
        }

        $usergroup = 0;
        foreach ($priceTypeData as $option) {
            if (!$usergroup && !empty($option['usergroup'])) {
                $usergroup = (int)$option['usergroup'];
                $instance->setUsergroup($usergroup);
            }
            $min = array_key_exists('min', $option) ? (int)$option['min'] : 0;
            $max = array_key_exists('max', $option) ? $option['max'] : 0;
            $max = $max === null ? null : (int)$option['max'];
            $amount = array_key_exists('amount', $option) ? (int)$option['amount'] : 0;
            $label = array_key_exists('label', $option) ? $option['label'] : '';

            $instance->add($min, $max, $amount, $label);
        }

        return $instance;
    }

    public static function getTitle()
    {
        return 'commerce.price_type.quantity';
    }

    public static function getFields(\Commerce $commerce)
    {
        $class = 'c-price-type--quantity';
        $options = [];
        foreach ($commerce->adapter->getIterator('modUserGroup') as $ug) {
            $options[$ug->get('id')] = $ug->get('name');
        }
        return [
            [
                'name' => 'usergroup',
                'type' => 'select',
                'classes' => 'c-price-type--quantity-usergroup',
                'options' => $options,
            ],
            [
                'name' => 'amount',
                'type' => 'currency',
                'classes' => $class,
            ],
            [
                'name' => 'min',
                'type' => 'number',
                'min' => 0,
                'classes' => $class,
            ],
            [
                'name' => 'max',
                'type' => 'number',
                'min' => 0,
                'classes' => $class,
            ],
            [
                'name' => 'label',
                'type' => 'text',
                'classes' => $class,
            ],
        ];

        /**
         [data-price-type="modmore\\Commerce\\Pricing\\PriceType\\Quantity"] .c-price-type--fields:last-of-type .c-price-type-select {
          display: none;
         }
         */
    }

    public static function doFieldsRepeat()
    {
        return true;
    }

    public static function allowMultiple()
    {
        return true;
    }

    public function __debugInfo()
    {
        return [
            'currency' => $this->currency->toArray(),
            'prices' => $this->prices,
            'usergroup' => $this->usergroup,
        ];
    }

    public function setUsergroup(int $usergroupId): void
    {
        $this->usergroup = $usergroupId;
    }

    public function getUsergroup(): int
    {
        if (!$this->usergroup) {
            foreach ($this->prices as $price) {
                if (!empty($price['usergroup'])) {
                    $this->usergroup = (int)$price['usergroup'];
                    break;
                }
            }
        }
        return $this->usergroup;
    }
}
