<?php

namespace Adyen\PrestaShop\model;

class AdyenNotification extends AbstractModel
{
    public const AUTHORISATION = 'AUTHORISATION';
    public const PENDING = 'PENDING';
    public const AUTHORISED = 'AUTHORISED';
    public const RECEIVED = 'RECEIVED';
    public const CANCELLED = 'CANCELLED';
    public const REFUSED = 'REFUSED';
    public const ERROR = 'ERROR';
    public const REFUND = 'REFUND';
    public const REFUND_FAILED = 'REFUND_FAILED';
    public const CANCEL_OR_REFUND = 'CANCEL_OR_REFUND';
    public const CAPTURE = 'CAPTURE';
    public const CAPTURE_FAILED = 'CAPTURE_FAILED';
    public const CANCELLATION = 'CANCELLATION';
    public const POSAPPROVED = 'POS_APPROVED';
    public const HANDLED_EXTERNALLY = 'HANDLED_EXTERNALLY';
    public const MANUAL_REVIEW_ACCEPT = 'MANUAL_REVIEW_ACCEPT';
    public const MANUAL_REVIEW_REJECT = 'MANUAL_REVIEW_REJECT ';
    public const RECURRING_CONTRACT = 'RECURRING_CONTRACT';
    public const REPORT_AVAILABLE = 'REPORT_AVAILABLE';
    public const ORDER_CLOSED = 'ORDER_CLOSED';
    public const OFFER_CLOSED = 'OFFER_CLOSED';

    private static $tableName = 'adyen_notification';

    /**
     * @return array|false|\mysqli_result|\PDOStatement|resource|null
     *
     * @throws \Exception
     */
    public function getUnprocessedNotifications()
    {
        $dateStart = new \DateTime();
        $dateStart->modify('-1 day');

        $dateEnd = new \DateTime();
        $dateEnd->modify('-1 minute');

        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . self::$tableName
            . ' WHERE `done` = 0'
            . ' AND `processing` = 0'
            . ' AND `created_at` > "' . $dateStart->format('Y-m-d H:i:s') . '"'
            . ' AND `created_at` < "' . $dateEnd->format('Y-m-d H:i:s') . '"'
            . ' LIMIT 100';

        return $this->dbInstance->executeS($sql);
    }

    public function getNumberOfUnprocessedNotifications()
    {
        $sql = 'SELECT COUNT(*) FROM ' . _DB_PREFIX_ . self::$tableName
            . ' WHERE `done` = "' . (int) 0 . '"'
            . ' AND `processing` = "' . (int) 0 . '"';

        return $this->dbInstance->getValue($sql);
    }

    /**
     * Update the unprocessed and not done notification to processing
     *
     * @param $id
     *
     * @return mixed
     */
    public function updateNotificationAsProcessing($id)
    {
        $data = [
            'processing' => 1,
        ];

        $where = '`done` = 0'
            . ' AND `processing` = 0'
            . ' AND `entity_id` = "' . (int) $id . '"';

        return $this->dbInstance->update(self::$tableName, $data, $where);
    }

    /**
     * Update the processed but not done notification to done
     *
     * @param $id
     *
     * @return mixed
     */
    public function updateNotificationAsDone($id)
    {
        $data = [
            'processing' => 0,
            'done' => 1,
        ];

        $where = '`done` = 0'
            . ' AND `processing` = 1'
            . ' AND `entity_id` = "' . (int) $id . '"';

        return $this->dbInstance->update(self::$tableName, $data, $where);
    }

    /**
     * Update the processed but not done notification to new
     *
     * @param $id
     *
     * @return mixed
     */
    public function updateNotificationAsNew($id)
    {
        $data = [
            'processing' => 0,
            'done' => 0,
        ];

        $where = '`done` = 0'
            . ' AND `processing` = 1'
            . ' AND `entity_id` = "' . (int) $id . '"';

        return $this->dbInstance->update(self::$tableName, $data, $where);
    }

    /**
     * @param $notification
     *
     * @throws \Exception
     */
    public function insertNotification($notification)
    {
        $data = [];
        if (isset($notification['pspReference'])) {
            $data['pspreference'] = pSQL($notification['pspReference']);
        }
        if (isset($notification['originalReference'])) {
            $data['original_reference'] = pSQL($notification['originalReference']);
        }
        if (isset($notification['merchantReference'])) {
            $data['merchant_reference'] = pSQL($notification['merchantReference']);
        }
        if (isset($notification['eventCode'])) {
            $data['event_code'] = pSQL($notification['eventCode']);
        }
        if (isset($notification['success'])) {
            $data['success'] = pSQL($notification['success']);
        }
        if (isset($notification['paymentMethod'])) {
            $data['payment_method'] = pSQL($notification['paymentMethod']);
        }
        if (isset($notification['amount'])) {
            $data['amount_value'] = pSQL($notification['amount']['value']);
            $data['amount_currency'] = pSQL($notification['amount']['currency']);
        }
        if (isset($notification['reason'])) {
            $data['reason'] = pSQL($notification['reason']);
        }

        if (isset($notification['additionalData'])) {
            $data['additional_data'] = pSQL(serialize($notification['additionalData']));
        }
        if (isset($notification['done'])) {
            $data['done'] = pSQL($notification['done']);
        }

        // do this to set both fields in the correct timezone
        $date = new \DateTime();

        $data['created_at'] = $this->getCreatedAtDate($data)->format('Y-m-d H:i:s');
        $data['updated_at'] = $date->format('Y-m-d H:i:s');

        $this->dbInstance->insert(self::$tableName, $data);
    }

    /**
     * If notification is already saved ignore it
     *
     * @param $notification
     *
     * @return mixed
     */
    public function isDuplicate($notification)
    {
        $pspReference = trim($notification['pspReference']);
        $eventCode = trim($notification['eventCode']);
        $success = trim($notification['success']);

        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . self::$tableName
            . ' WHERE `pspreference` = "' . pSQL($pspReference) . '"'
            . ' AND `event_code` = "' . pSQL($eventCode) . '"'
            . ' AND `success` = "' . pSQL($success) . '"';

        $originalReference = null;
        if (!empty($notification['originalReference'])) {
            $originalReference = trim($notification['originalReference']);
            $sql .= ' AND `original_reference` = "' . pSQL($originalReference) . '"';
        }

        return $this->dbInstance->getValue($sql);
    }

    /**
     * @param $merchantReference
     *
     * @return mixed
     */
    public function getProcessedNotificationsByMerchantReference($merchantReference)
    {
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . self::$tableName
            . ' WHERE `merchant_reference` = "' . pSQL($merchantReference) . '"'
            . ' AND `done` = 1';

        return $this->dbInstance->executeS($sql);
    }

    /**
     * @param $data
     *
     * @return \DateTime
     */
    private function getCreatedAtDate($data)
    {
        $date = new \DateTime();
        // If authorisation w/ false success OR offer closed, delay by an hour
        if (($data['event_code'] === self::AUTHORISATION && $data['success'] === 'false') ||
            $data['event_code'] === self::OFFER_CLOSED) {
            $date->add(new \DateInterval('PT1H'));
        }

        return $date;
    }
}
