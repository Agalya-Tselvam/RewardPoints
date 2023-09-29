<?php
namespace Riverstone\RewardPoints\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Riverstone\RewardPoints\Model\ResourceModel\RewardPoints\CollectionFactory;

class RuleDisplayForProducts implements ObserverInterface
{
    protected $collectionFactory;
    protected $json;

    public function __construct(CollectionFactory $collectionFactory, Json $json)
    {
        $this->collectionFactory = $collectionFactory;
        $this->json = $json;
    }

    /**
     * Below is the method that will fire whenever the event runs!
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $product = $observer->getProduct();
        $productData = $product->getData();
        $categoryIds = $product->getCategoryIds();
        $result = $this->conditionsMatchProduct($productData, $categoryIds);

        if ($result) {
            $originalName = $product->getName();
            $modifiedName = $originalName . ' - Content added by Magenest ';
            $product->setName($modifiedName);
        }
    }

    public function conditionsMatchProduct($productData, $categoryIds)
    {
        $ruleCollection = $this->collectionFactory->create();
        foreach ($ruleCollection as $rule) {
            $actionsSerialized = $rule->getActionsSerialized();

            // Check if the actions serialized data is not empty
            if (!empty($actionsSerialized)) {
                $actions = json_decode($actionsSerialized, true);
                if (isset($actions['type']) && $actions['type'] === 'Magento\\SalesRule\\Model\\Rule\\Condition\\Product'
                    && isset($actions['attribute']) && $actions['attribute'] === 'category_ids'
                    && isset($actions['operator']) && $actions['operator'] === '==' && isset($actions['value'])) {

                    $conditionValue = explode(',', $actions['value']);
                    $isInCategories = count(array_intersect($conditionValue, $categoryIds)) > 0;

                    if ($isInCategories) {
                        return true;
                    }
                }
            }
        }

        return false;
    }
}
