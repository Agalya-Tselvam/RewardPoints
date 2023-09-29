<?php

namespace Riverstone\RewardPoints\Observer;

use Exception;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Quote\Model\Quote;
use Psr\Log\LoggerInterface;
use Riverstone\RewardPoints\Model\ResourceModel\RewardPoints\CollectionFactory;

class OrderPlaceAfterSavePoints implements ObserverInterface
{
    protected $logger;
    protected $collectionFactory;
    protected $json;
    protected $jsonSerializer;

    public function __construct(LoggerInterface $logger, CollectionFactory $collectionFactory, Json $jsonSerializer,
                                \Magento\Framework\Serialize\Serializer\Json $json
    )
    {
        $this->collectionFactory = $collectionFactory;
        $this->jsonSerializer = $jsonSerializer;
        $this->json = $json;
        $this->logger = $logger;
    }

    public function execute(Observer $observer)
    {
        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/test.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);
        $logger->info("---my custome message-----");
        try {
            $event = $observer->getEvent();
            $order = $event->getOrder();
            $ruleCollection = $this->collectionFactory->create();
            foreach ($ruleCollection as $rule) {
                $conditionsSerialized = $rule->getConditionsSerialized();
                $conditions =$this->json->unserialize($conditionsSerialized);
                if (isset($conditions['conditions']) && is_array($conditions['conditions'])) {
                    $conditionArray = $conditions['conditions'];
                    foreach ($conditionArray as $condition) {
                        $type = $condition['type'];
                        $attribute = $condition['attribute'];
                        $operator = $condition['operator'];
                        $value = $condition['value'];
                        $isValueProcessed = $condition['is_value_processed'];
                        if ($type === 'Magento\\SalesRule\\Model\\Rule\\Condition\\Address' && $attribute === 'base_subtotal' && $operator === '==' && $value === '100' && !$isValueProcessed) {

                        }
                    }
                }
                return '';
            }

        } catch (Exception $e) {
            $this->logger->info($e->getMessage());
        }
    }

}
