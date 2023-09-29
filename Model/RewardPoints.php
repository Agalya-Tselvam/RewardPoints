<?php

namespace Riverstone\RewardPoints\Model;

use Riverstone\RewardPoints\Model\ResourceModel\RewardPoints as EarnResource;
use Magento\Quote\Model\Quote\Address;

/**
 * Class Earn
 * @package Riverstone\RewardPoints\Model\RewardPoints
 * @method bool hasStoreLabels()
 * @method int getRuleId()
 * @method \Riverstone\RewardPoints\Model\RewardPoints setName($name)
 * @method string getName()
 * @method \Riverstone\RewardPoints\Model\RewardPoints getStoreLabels()
 * @method string getSimpleAction()
 * @method \Riverstone\RewardPoints\Model\RewardPoints setSimpleAction(string $value)
 * @method float getPurchaseAmount()
 * @method \Riverstone\RewardPoints\Model\RewardPoints setPurchaseAmount(float $value)
 * @method int getPurchaseQty()
 * @method \Riverstone\RewardPoints\Model\RewardPoints setPurchaseQty(int $value)
 * @method float getCreditAmount()
 * @method \Riverstone\RewardPoints\Model\RewardPoints setCreditAmount(float $value)
 * @method float getMaximumCreditAmount()
 * @method \Riverstone\RewardPoints\Model\RewardPoints setMaximumCreditAmount(float $value)
 * @method int getSortOrder()
 * @method \Riverstone\RewardPoints\Model\RewardPoints setSortOrder(int $value)
 * @method int getStatus()
 * @method \Riverstone\RewardPoints\Model\RewardPoints setStatus(int $value)
 * @method int getStopRulesProcessing()
 * @method \Riverstone\RewardPoints\Model\RewardPoints setStopRulesProcessing(int $value)
 * @method string getFromDate()
 * @method \Riverstone\RewardPoints\Model\RewardPoints setFromDate(string $date)
 * @method string getToDate()
 * @method \Riverstone\RewardPoints\Model\RewardPoints setToDate(string $date)
 */

class RewardPoints extends \Magento\Rule\Model\AbstractModel
{
    /**
     * Rule simple actions
     */
    const BY_FIXED_ACTION = 'by_fixed';

    const BY_PERCENT_ACTION = 'by_percent';

    const BY_Y_AMOUNT_GET_X_ACTION = 'by_y_amount_get_x';

    const BY_Y_QTY_GET_X_ACTION = 'by_y_qty_get_x';

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\SalesRule\Model\Rule\Condition\CombineFactory
     */
    protected $_condCombineFactory;

    /**
     * @var \Magento\SalesRule\Model\Rule\Condition\Product\CombineFactory
     */
    protected $_condProdCombineF;

    /**
     * Store already validated addresses and validation results
     *
     * @var array
     */
    private $validatedAddresses = [];

    /**
     * List of actions that will be applied for whole cart
     *
     * @var array
     */
    private $wholeCartActions = [
        self::BY_FIXED_ACTION,
        self::BY_Y_AMOUNT_GET_X_ACTION,
        self::BY_Y_QTY_GET_X_ACTION,
    ];

    /**
     * AbstractModel constructor
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\SalesRule\Model\Rule\Condition\CombineFactory $condCombineFactory
     * @param \Magento\SalesRule\Model\Rule\Condition\Product\CombineFactory $condProdCombineF
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\SalesRule\Model\Rule\Condition\CombineFactory $condCombineFactory,
        \Magento\SalesRule\Model\Rule\Condition\Product\CombineFactory $condProdCombineF,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_condCombineFactory = $condCombineFactory;
        $this->_condProdCombineF = $condProdCombineF;
        $this->storeManager = $storeManager;
        parent::__construct(
            $context,
            $registry,
            $formFactory,
            $localeDate,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(EarnResource::class);
    }

    public function getConditionsInstance()
    {
        return $this->_condCombineFactory->create();
    }

    /**
     * Get rule condition product combine model instance
     *
     * @return \Magento\SalesRule\Model\Rule\Condition\Product\Combine
     */
    public function getActionsInstance()
    {
        return $this->_condProdCombineF->create();
    }

    /**
     * Retrieve Serialized Conditions
     * Deprecated and Need to be removed in future releases
     * added for compatibility with 2.1.x and 2.2.x
     * @return string
     */
    public function getConditionsSerialized()
    {
        $value = $this->getData('conditions_serialized');
        return $value;
    }

    /**
     * Retrieve Serialized Actions
     * Deprecated and Need to be removed in future releases
     * added for compatibility with 2.1.x and 2.2.x
     * @return string
     */
    public function getActionsSerialized()
    {
        $value = $this->getData('actions_serialized');
        return $value;
    }

    /**
     * Retrieve entity type id
     * @return int
     */
    public function getEntityTypeId()
    {
        return $this->getResource()->getEntityTypeId();
    }

    /**
     * Retrieve label by specific store ID
     * If storeId not specified current store ID will be used
     *
     * @param null $storeId
     * @return bool
     */
    public function getStoreLabel($storeId = null)
    {
        if ($this->hasStoreLabels()) {
            $labels = $this->getStoreLabels();
            $storeId = $this->storeManager->getStore((int)$storeId)->getId();

            if (array_key_exists($storeId, $labels)) {
                return $labels[$storeId];
            }
        }

        return false;
    }

    /**
     * Check cached validation result for specific address
     *
     * @param \Magento\Quote\Model\Quote\Address $address
     * @return bool
     */
    public function hasIsValidForAddress($address)
    {
        $addressId = $this->getAddressId($address);

        return isset($this->validatedAddresses[$addressId]) ? true : false;
    }

    /**
     * Set validation result for specific address to results cache
     *
     * @param \Magento\Quote\Model\Quote\Address $address
     * @param bool $validationResult
     * @return $this
     */
    public function setIsValidForAddress($address, $validationResult)
    {
        $addressId = $this->getAddressId($address);
        $this->validatedAddresses[$addressId] = $validationResult;

        return $this;
    }

    /**
     * Get cached validation result for specific address
     *
     * @param \Magento\Quote\Model\Quote\Address $address
     * @return bool
     */
    public function getIsValidForAddress($address)
    {
        $addressId = $this->getAddressId($address);

        return isset($this->validatedAddresses[$addressId]) ? $this->validatedAddresses[$addressId] : false;
    }

    /**
     * Return id for address
     *
     * @param \Magento\Quote\Model\Quote\Address $address
     * @return string
     */
    private function getAddressId($address)
    {
        if ($address instanceof Address) {
            return $address->getId();
        }

        return $address;
    }

    /**
     * @return bool
     */
    public function isForWholeCart()
    {
        return in_array($this->getSimpleAction(), $this->wholeCartActions);
    }

    /**
     * @param string $formName
     * @return string
     * @since 100.1.0
     */
    public function getConditionsFieldSetId($formName = '')
    {
        return $formName . '_rule_conditions_fieldset';
    }

    /**
     * @param string $formName
     * @return string
     * @since 100.1.0
     */
    public function getActionsFieldSetId($formName = '')
    {
        return $formName . '_rule_actions_fieldset';
    }
}
