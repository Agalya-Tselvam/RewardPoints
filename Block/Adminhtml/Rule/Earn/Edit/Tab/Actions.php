<?php

namespace Riverstone\RewardPoints\Block\Adminhtml\Rule\Earn\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Form\Renderer\Fieldset;
use Magento\Config\Model\Config\Source\Yesno;
use Magento\Framework\Data\Form;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Rule\Model\Condition\AbstractCondition;
use Magento\Ui\Component\Layout\Tabs\TabInterface;
use Riverstone\RewardPoints\Model\RewardPoints;
use Riverstone\RewardPoints\Model\RewardPointsFactory;

class Actions extends Generic implements TabInterface
{
    /**
     * Core registry
     *
     * @var Fieldset
     */
    protected $_rendererFieldset;

    /**
     * @var \Magento\Rule\Block\Actions
     */
    protected $_ruleActions;

    /**
     * @var Yesno
     * @deprecated 100.1.0
     */
    protected $_sourceYesno;

    /**
     * @var string
     */
    protected $_nameInLayout = 'actions_apply_to';

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
     * @param Yesno $sourceYesno
     * @param \Magento\Rule\Block\Actions $ruleActions
     * @param Fieldset $rendererFieldset
     * @param array $data
     * @param RewardPointsFactory|null $ruleFactory
     */
    public function __construct(Context $context, Registry $registry, FormFactory $formFactory, Yesno $sourceYesno, \Magento\Rule\Block\Actions $ruleActions, Fieldset $rendererFieldset, RewardPointsFactory $ruleFactory, array $data = [])
    {
        $this->_rendererFieldset = $rendererFieldset;
        $this->_ruleActions = $ruleActions;
        $this->_sourceYesno = $sourceYesno;
        $this->ruleFactory = $ruleFactory;
        parent::__construct($context, $registry, $formFactory, $data);
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
        return __('Actions');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Actions');
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
     * Prepare form before rendering HTML
     *
     * @return $this
     * @throws LocalizedException
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('current_model');
        $form = $this->addTabToForm($model);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Handles addition of actions tab to supplied form.
     *
     * @param $model
     * @param string $fieldsetId
     * @param string $formName
     * @return Form
     * @throws LocalizedException
     */
    protected function addTabToForm($model, $fieldsetId = 'actions_fieldset', $formName = 'reward_form')
    {
        if (!$model) {
            $id = $this->getRequest()->getParam('id');
            /** @var RewardPoints $model */
            $model = $this->ruleFactory->create();
            $model->load($id);
        }

        $actionsFieldSetId = $model->getActionsFieldSetId($formName);

        $newChildUrl = $this->getUrl('*/*/newActionHtml/form/' . $actionsFieldSetId, ['form_namespace' => $formName]);

        /** @var Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('rule_earn_');
        $renderer = $this->_rendererFieldset->setTemplate('Magento_CatalogRule::promo/fieldset.phtml')->setNewChildUrl($newChildUrl)->setFieldSetId($actionsFieldSetId);

        $fieldset = $form->addFieldset($fieldsetId, ['legend' => __('Apply the rule only to cart items matching the following conditions ' . '(leave blank for all items).')])->setRenderer($renderer);

        $fieldset->addField('actions', 'text', ['name' => 'apply_to', 'label' => __('Apply To'), 'title' => __('Apply To'), 'required' => true, 'data-form-part' => $formName])->setRule($model)->setRenderer($this->_ruleActions);

        $form->setValues($model->getData());
        $this->setActionFormName($model->getActions(), $formName, $actionsFieldSetId);

        return $form;
    }

    /**
     * Handles addition of form name to action and its actions.
     *
     * @param AbstractCondition $actions
     * @param string $formName
     * @param $fieldSetId
     * @return void
     * @throws LocalizedException
     */
    private function setActionFormName(AbstractCondition $actions, $formName, $fieldSetId)
    {
        $actions->setFormName($formName);
        if ($actions->getActions() && is_array($actions->getActions())) {
            foreach ($actions->getActions() as $condition) {
                $condition->setJsFormObject($fieldSetId);
                $this->setActionFormName($condition, $formName, $fieldSetId);
            }
        }
    }
}
