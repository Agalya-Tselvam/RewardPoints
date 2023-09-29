<?php

namespace Riverstone\RewardPoints\Controller\Adminhtml\Index;

use Exception;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Filter\FilterInput;
use Magento\Framework\Stdlib\DateTime\Filter\Date;
use Riverstone\RewardPoints\Model\ResourceModel\RewardPoints;
use Riverstone\RewardPoints\Model\RewardPointsFactory;


class Save extends Action
{
    protected $rewardPointsFactory;
    protected $dataPersistor;
    protected $request;
    protected $resourceModel;
    protected $dateFilter;

    public function __construct(Context                $context,
                                RewardPointsFactory    $rewardPointsFactory,
                                DataPersistorInterface $dataPersistor,
                                RequestInterface       $request,
                                RewardPoints           $resourceModel,
                                Date                   $dateFilter)
    {
        $this->resourceModel = $resourceModel;
        $this->rewardPointsFactory = $rewardPointsFactory;
        $this->resourceModel = $resourceModel;
        $this->dateFilter = $dateFilter;
        $this->dataPersistor = $dataPersistor;
        parent::__construct($context);
    }

    public function execute()
    {
        try {
            $data = $this->getRequest()->getPostValue();
            $id = $this->getRequest()->getParam('id');
            if ($id) {
                $model = $this->rewardPointsFactory->create()->load($id);
            } else {
                $model = $this->rewardPointsFactory->create();
            }
            $websites = implode(', ', $data['websites']);
            $customerGroups = implode(', ', $data['customer_group']);

            if (!empty($data['rule']['conditions'])) {
                $data['conditions'] = $data['rule']['conditions'];
            }
            if (!empty($data['rule']['actions'])) {
                $data['actions'] = $data['rule']['actions'];
            }
            unset($data['rule']);
            $model->loadPost($data);
            /* Filter from and to dates */
            $filterValues = ['from_date' => $this->dateFilter];
            if ($this->getRequest()->getParam('to_date')) {
                $filterValues['to_date'] = $this->dateFilter;
            }
            if (class_exists(FilterInput::class)) {
                $inputFilter = new FilterInput($filterValues, [], $data);
            } else {
                // For Magento older than 2.4.6
                $zendFilterClassName = 'Zend_Filter' . '_Input';
                $inputFilter = new $zendFilterClassName($filterValues, [], $data);
            }
            $data = $inputFilter->getUnescaped();
            $model->setData($data);
            $model->setData('websites', $websites);
            $model->setData('customer_group', $customerGroups);
            $this->resourceModel->save($model);
            $this->messageManager->addSuccessMessage(__('Data saved successfully.'));
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage(__('Error: %1', $e->getMessage()));
        }
        $this->_redirect('*/*/index');
    }

}
