<?php

namespace Riverstone\RewardPoints\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class RewardPoints extends AbstractDb
{
    const MAIN_TABLE_NAME = 'reward_points_rule';

    /**
     * Name of Primary Column
     */
    const MAIN_TABLE_ID_FIELD_NAME = 'id';

    const FORM_SESSION_KEY = 'prcredit_rule_earn_form_data';

    /**
     * Entity Type Identifier
     */
    const ENTITY_TYPE_ID = 1;

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('reward_points_rule', 'id');
    }
}
