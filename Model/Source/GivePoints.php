<?php

namespace Riverstone\RewardPoints\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

class GivePoints implements OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            [
                'value' => 'x_points',
                'label' => __('Get X Points'),
            ],
            [
                'value' => 'xpoints_yspent',
                'label' => __('Get X Points for every Y spent'),
            ],
            [
                'value' => 'xpoints_yspent_zspend',
                'label' => __('Get X Points for every Y spent starting from Z spend'),
            ],
            [
                'value' => 'xpoints_yqty',
                'label' => __('Get X Points for every Y quantity'),
            ],
            [
                'value' => 'xpoints_yqty_zqty',
                'label' => __('Get X Points for every Y quantity starting from Z quantity'),
            ],
        ];
    }
}



