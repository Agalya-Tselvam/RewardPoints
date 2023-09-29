<?php

namespace Riverstone\RewardPoints\Model\ResourceModel\RewardPoints;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Riverstone\RewardPoints\Model\ResourceModel\RewardPoints as RewardsResourceModel;
use Riverstone\RewardPoints\Model\RewardPoints as RewardModel;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(\Riverstone\RewardPoints\Model\RewardPoints::class, \Riverstone\RewardPoints\Model\ResourceModel\RewardPoints::class);
    }
}
