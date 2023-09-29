<?php

namespace Riverstone\RewardPoints\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

class CustomOptions implements OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            [
                'value' => 'place_order',
                'label' => __('Place Order'),
            ],
            [
                'value' => 'customer_bdy',
                'label' => __('Customer Birthday'),
            ],
            [
                'value' => 'customer_reg',
                'label' => __('Customer Registration'),
            ],
            [
                'value' => 'customer_review',
                'label' => __('Customer Review'),
            ],
            [
                'value' => 'news_sub',
                'label' => __('Newsletter Subscription'),
            ],
        ];
    }
}



