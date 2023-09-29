<?php

namespace Riverstone\RewardPoints\Controller\Adminhtml\Index;

use Riverstone\RewardPoints\Controller\Adminhtml\Rule\Earn;
use Riverstone\RewardPoints\Model\ResourceModel\RewardPoints as EarnResourceModel;
use Magento\Rule\Model\Condition\AbstractCondition;

class NewConditionHtml extends Earn
{
    /**
     * New condition html action
     *
     * @return void
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $formName = $this->getRequest()->getParam('form_namespace');
        $typeArr = explode('|', str_replace('-', '/', $this->getRequest()->getParam('type')));
        $type = $typeArr[0];

        $model = $this->_objectManager->create($type)->setId($id)->setType($type)->setRule($this->earnFactory->create())->setPrefix('conditions');
        if (!empty($typeArr[1])) {
            $model->setAttribute($typeArr[1]);
        }
        if ($model instanceof AbstractCondition) {
            $model->setJsFormObject($this->getRequest()->getParam('form'));
            $model->setFormName($formName);
            $html = $model->asHtmlRecursive();
        } else {
            $html = '';
        }
        $this->getResponse()->setBody($html);
    }
}
