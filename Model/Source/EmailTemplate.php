<?php

namespace Riverstone\RewardPoints\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

class EmailTemplate implements OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            [
                'value' => 'use_config',
                'label' => __('Use config'),
            ],
            [
                'value' => 'reward_points_bal',
                'label' => __('Use the rule to update reward points balance (Default)'),
            ],
            [
                'value' => 'pickup_order',
                'label' => __('New Pickup Order'),
            ],
            [
                'value' => 'pickup_order_guest',
                'label' => __('New Pickup Order For Guest'),
            ],
        ];
    }
}



