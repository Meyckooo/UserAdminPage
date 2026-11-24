<?php

require_once __DIR__ . '/../entity/ItemModel.php';
require_once __DIR__ . '/../check_access.php';

class ItemController
{
    private ItemModel $itemModel;
    private int $locatorModuleId;

    // Nag-instantiate sa ItemModel ug nag-set sa module ID para sa permission checks
    public function __construct($conn, int $locatorModuleId = 16)
    {
        $this->itemModel = new ItemModel($conn);
        $this->locatorModuleId = $locatorModuleId;
    }

    // Nag-validate sa session ug oPost permission, nag-parse sa payload, ug nag-post sa items via ItemModel
    public function postItems(array $postData, array $sessionData): void
    {
        header('Content-Type: application/json');

        if (!isset($sessionData['oUserid'])) {
            echo json_encode(['status' => 'error', 'message' => 'Session expired. Please log in again.']);
            exit();
        }

        if (!hasPermission($this->locatorModuleId, 'oPost')) {
            echo json_encode([
                'status' => 'error',
                'code'   => 'NO_PERMISSION',
                'message' => "You don't have permission to post items in this module."
            ]);
            exit();
        }

        $items_json = $postData['items'] ?? '[]';
        $items = json_decode($items_json, true);

        $locator_details_json = $postData['locator_details'] ?? '{}';
        $locator_details = json_decode($locator_details_json, true);

        $locatorItem    = trim($postData['locator_item'] ?? ($locator_details['locator_code'] ?? ''));
        $defaultRemarks = trim($locator_details['remarks'] ?? '');
        $defaultAddedBy = trim($locator_details['added_by'] ?? ($sessionData['name'] ?? $sessionData['username'] ?? ''));

        if (empty($items) || !is_array($items)) {
            echo json_encode(['status' => 'error', 'message' => 'There is no item(s) to post!']);
            exit();
        }

        $result = $this->itemModel->insertPostedItems($items, $locatorItem, $defaultRemarks, $defaultAddedBy);

        if (!$result['ok']) {
            echo json_encode(['status' => 'error', 'message' => $result['error']]);
            exit();
        }

        $inserted_count = $result['inserted_count'];

        if ($inserted_count > 0) {
            $byText = $defaultAddedBy !== '' ? " by {$defaultAddedBy}" : "";
            echo json_encode([
                'status' => 'success',
                'message' => "$inserted_count item(s) successfully posted{$byText}!"
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to post items.']);
        }
        exit();
    }

    // Nag-fetch sa mga na-post na nga items ug nagbalik og JSON response para sa frontend table/modal
    public function getPostedItems(array $postData): void
    {
        error_reporting(E_ALL);
        ini_set('display_errors', 0); // I-set sa 0 aron dili mo-print og HTML errors

        header('Content-Type: application/json');

        $response = [
            'status' => 'error',
            'items' => [],
            'message' => ''
        ];

        $dateRange = !empty($postData['date_range']) ? $postData['date_range'] : '';
        $result = $this->itemModel->getPostedItems($dateRange);

        if ($result['ok']) {
            $response['status'] = 'success';
            $response['items']  = $result['items'];
        } else {
            $response['message'] = $result['error'];
        }

        echo json_encode($response);
        exit();
    }
}