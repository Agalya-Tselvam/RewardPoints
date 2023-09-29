<?php

namespace Riverstone\RewardPoints\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

class CalculationType implements OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            [
                'value' => 'fixed',
                'label' => __('Fixed'),
            ],
            [
                'value' => 'percent',
                'label' => __('Percent'),
            ],
        ];
    }
}



