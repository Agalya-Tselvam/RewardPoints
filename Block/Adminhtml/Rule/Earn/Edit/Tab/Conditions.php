<?php

namespace Riverstone\RewardPoints\Block\Adminhtml\Rule\Earn\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Form\Renderer\Fieldset;
use Magento\Framework\Data\Form;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Rule\Model\Condition\AbstractCondition;
use Magento\Ui\Component\Layout\Tabs\TabInterface;
use Riverstone\RewardPoints\Model\EarnFactory;
use Riverstone\RewardPoints\Model\ResourceModel\RewardPoints;
use Riverstone\RewardPoints\Model\RewardPointsFactory;

class Conditions extends Generic implements TabInterface
{
    /**
     * @var \Magento\Rule\Block\Conditions
     */
    private $conditions;

    /**
     * Core registry
     *
     * @var Fieldset
     */
    private $rendererFieldset;

    /**
     * @var RewardPointsFactory
     */
    private $ruleFactory;

    /**
     * Constructor
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param \Magento\Rule\Block\Conditions $conditions
     * @param Fieldset $rendererFieldset
     * @param EarnFactory $ruleFactory
     * @param array $data
     */
    public function __construct(Context                        $context,
                                Registry                       $registry,
                                FormFactory                    $formFactory,
                                \Magento\Rule\Block\Conditions $conditions,
                                Fieldset                       $rendererFieldset,
                                RewardPointsFactory                    $ruleFactory,
                                array                          $data = [])
    {
        $this->rendererFieldset = $rendererFieldset;
        $this->conditions = $conditions;
        $this->ruleFactory = $ruleFactory;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_nameInLayout = 'conditions_apply_to';
        parent::_construct();
    }

    /**
     * {@inheritdoc}
     */
    public function getTabClass()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getTabUrl()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function isAjaxLoaded()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Conditions');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Conditions');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('current_model');
        $form = $this->addTabToForm($model);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Handles addition of conditions tab to supplied form.
     *
     * @param $model
     * @param string $fieldsetId
     * @param string $formName
     * @return Form
     * @throws LocalizedException
     */
    private function addTabToForm($model, $fieldsetId = 'conditions_fieldset', $formName = 'reward_form')
    {
        if (!$model) {
            $id = $this->getRequest()->getParam(RewardPoints::MAIN_TABLE_ID_FIELD_NAME);
            $model = $this->ruleFactory->create();
            $model->load($id);
        }

        $conditionsFieldSetId = $model->getConditionsFieldSetId($formName);
        $newChildUrl = $this->getUrl('*/*/newConditionHtml/form/' . $conditionsFieldSetId, ['form_namespace' => $formName]);

        /** @var Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('rule_earn_');
        $renderer = $this->rendererFieldset->setTemplate('Magento_CatalogRule::promo/fieldset.phtml')->setNewChildUrl($newChildUrl)->setFieldSetId($conditionsFieldSetId);

        $fieldset = $form->addFieldset($fieldsetId, ['legend' => __('Apply the rule only if the following conditions are met ' . '(leave blank for all products and customers).')])->setRenderer($renderer);

        $fieldset->addField('conditions', 'text', ['name' => 'conditions', 'label' => __('Conditions'), 'title' => __('Conditions'), 'required' => true, 'data-form-part' => $formName])->setRule($model)->setRenderer($this->conditions);

        $form->setValues($model->getData());
        $this->setConditionFormName($model->getConditions(), $formName, $conditionsFieldSetId);

        return $form;
    }

    /**
     * Handles addition of form name to condition and its conditions.
     *
     * @param AbstractCondition $conditions
     * @param string $formName
     * @param $fieldSetId
     * @return void
     * @throws LocalizedException
     */
    private function setConditionFormName(AbstractCondition $conditions, $formName, $fieldSetId)
    {
        $conditions->setFormName($formName);

        if ($conditions->getConditions() && is_array($conditions->getConditions())) {
            foreach ($conditions->getConditions() as $condition) {
                $condition->setJsFormObject($fieldSetId);
                $this->setConditionFormName($condition, $formName, $fieldSetId);
            }
        }
    }
}
