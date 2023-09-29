<?php

namespace Riverstone\RewardPoints\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Riverstone\RewardPoints\Model\ResourceModel\RewardPoints;
use Riverstone\RewardPoints\Model\RewardPointsFactory;

class Edit extends Action
{
    protected $resultPage;

    protected $resultPageFactory = false;

    protected $coreRegistry;

    protected $model;

    protected $resourceModel;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Registry $coreRegistry,
        RewardPointsFactory $model,
        RewardPoints $resourceModel
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->coreRegistry = $coreRegistry;
        $this->model = $model;
        $this->resourceModel = $resourceModel;
    }

    public function execute()
    {
        $rewardId = $this->getRequest()->getParam('id');
        $reward = $this->model->create();
        if ($rewardId) {
            $this->resourceModel->load($reward, $rewardId, "id");
            if ($reward->getId()) {
                $this->coreRegistry->register('rule', $reward);
            }
        }
        $resultPage = $this->getResultPage();
        $resultPage->getConfig()->getTitle()->prepend(
            $reward->getId() ? $reward->getName() : __('New Rule')
        );
        return $resultPage;
    }

    public function getResultPage()
    {
        $this->resultPage = $this->resultPageFactory->create();
        return $this->resultPage;
    }
}
