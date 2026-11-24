<?php

class ItemModel
{
    private $conn;

    // Nag-initialize sa database connection para magamit sa tanang query methods sa ItemModel
    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    // Nag-insert sa lista sa mga gi-post nga items ngadto sa tbl_item_details
    public function insertPostedItems(array $items, string $locatorItem, string $defaultRemarks, string $defaultAddedBy): array
    {
        // 10 columns + NOW() = 11th column (created_at)
        $post_stmt = $this->conn->prepare("
            INSERT INTO tbl_item_details 
            (item_locator, item_code, item_barcode, item_no, item_uom, item_desc, item_first_loc, item_sec_loc, item_stock, remarks, added_by, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");

        if (!$post_stmt) {
            return [
                'ok' => false,
                'inserted_count' => 0,
                'error' => 'Database prepare failed: ' . $this->conn->error
            ];
        }

        $inserted_count = 0;

        foreach ($items as $item) {
            $itemLocator = !empty($item['locatorItem']) ? $item['locatorItem'] : $locatorItem;
            $itemCode    = $item['itemCode'] ?? '';
            $barCode     = $item['barCode'] ?? '';
            $itemNo      = $item['itemNo'] ?? '';
            $itemUom     = $item['uom'] ?? '';
            $desc        = $item['desc'] ?? '';
            $firstLocInt = intval($item['firstLocChecked'] ?? 0);
            $secLocInt   = intval($item['secLocChecked'] ?? 0);
            $itemStock   = intval($item['itemStock'] ?? 0);
            $remarks     = isset($item['remarks']) && trim($item['remarks']) !== '' ? trim($item['remarks']) : $defaultRemarks;
            $addedBy     = isset($item['addedBy']) && trim($item['addedBy']) !== '' ? trim($item['addedBy']) : $defaultAddedBy;

            // 8 strings ("ssssssss") + 3 integers ("iii") = 11 parameters
            $post_stmt->bind_param(
                "ssssssiiiss",
                $itemLocator,
                $itemCode,
                $barCode,
                $itemNo,
                $itemUom,
                $desc,
                $firstLocInt,
                $secLocInt,
                $itemStock,
                $remarks,
                $addedBy
            );

            if ($post_stmt->execute()) {
                $inserted_count++;
            }
        }

        $post_stmt->close();

        return [
            'ok' => true,
            'inserted_count' => $inserted_count,
            'error' => null
        ];
    }

    // Nag-fetch sa mga na-post na nga items gikan sa tbl_item_details (default karon nga adlaw, o base sa dateRange)
    public function getPostedItems(string $dateRange = ''): array
    {
        // DEFAULT: Filter sa Current Day / Karon nga adlaw lang
        $where_clause = " WHERE DATE(created_at) = CURDATE()";

        // Kon may napili nga custom date/range sa filter input
        if (!empty($dateRange)) {
            $dates = explode(' to ', $dateRange);
            if (count($dates) == 2) {
                $start_date = mysqli_real_escape_string($this->conn, trim($dates[0]) . ' 00:00:00');
                $end_date   = mysqli_real_escape_string($this->conn, trim($dates[1]) . ' 23:59:59');
                $where_clause = " WHERE created_at BETWEEN '$start_date' AND '$end_date'";
            } else if (count($dates) == 1) {
                $single_date = mysqli_real_escape_string($this->conn, trim($dates[0]));
                $where_clause = " WHERE DATE(created_at) = '$single_date'";
            }
        }

        $query = "SELECT * FROM tbl_item_details $where_clause ORDER BY created_at DESC";
        $result = mysqli_query($this->conn, $query);

        if (!$result) {
            return [
                'ok' => false,
                'items' => [],
                'error' => mysqli_error($this->conn)
            ];
        }

        $items = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $items[] = [
                'item_locator'   => $row['item_locator'] ?? '',
                'item_code'      => $row['item_code'] ?? '',
                'item_barcode'   => $row['item_barcode'] ?? '',
                'item_no'        => $row['item_no'] ?? '',
                'item_uom'       => $row['item_uom'] ?? '',
                'item_desc'      => $row['item_desc'] ?? '',
                'item_first_loc' => $row['item_first_loc'] ?? '',
                'item_sec_loc'   => $row['item_sec_loc'] ?? '',
                'remarks'        => $row['remarks'] ?? '',
                'added_by'       => $row['added_by'] ?? '',
                'created_at'     => $row['created_at'] ?? ''
            ];
        }

        return [
            'ok' => true,
            'items' => $items,
            'error' => null
        ];
    }
}