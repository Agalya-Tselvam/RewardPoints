<?php

namespace Riverstone\RewardPoints\Controller\Adminhtml\Rule;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Stdlib\DateTime\Filter\Date;
use Plumrocket\StoreCredit\Model\ResourceModel\Rule\EarnFactory;
use Riverstone\RewardPoints\Model\ResourceModel\RewardPoints as EarnResourceModel;
use Riverstone\RewardPoints\Model\ResourceModel\RewardPointsFactory;
use Riverstone\RewardPoints\Model\RewardPoints as EarnModel;

class Earn extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Riverstone_RewardPoints::reward_rules';

    /**
     * @var \Plumrocket\StoreCredit\Model\Rule\EarnFactory
     */
    public $earnFactory;

    /**
     * @var RewardPointsFactory
     */
    public $earnResourceFactory;

    /**
     * @var Date
     */
    public $dateFilter;

    /**
     * Earn constructor.
     *
     * @param Context $context
     * @param \Riverstone\RewardPoints\Model\RewardPointsFactory $earnFactory
     * @param RewardPointsFactory $earnResourceFactory
     * @param Date $dateFilter
     */
    public function __construct(Context                                            $context,
                                \Riverstone\RewardPoints\Model\RewardPointsFactory $earnFactory,
                                RewardPointsFactory                                $earnResourceFactory,
                                Date                                               $dateFilter)
    {
        $this->earnFactory = $earnFactory;
        $this->earnResourceFactory = $earnResourceFactory;
        $this->dateFilter = $dateFilter;
        $this->_formSessionKey = EarnResourceModel::FORM_SESSION_KEY;
        $this->_modelClass = EarnModel::class;
        $this->_activeMenu = self::ADMIN_RESOURCE;
        $this->_objectTitle = 'Earn Rule';
        $this->_objectTitles = 'Earn Rules';
        $this->_idKey = EarnResourceModel::MAIN_TABLE_ID_FIELD_NAME;
        $this->_statusField = 'status';
        parent::__construct($context);
    }

    public function execute()
    {
        // TODO: Implement execute() method.
    }
}
