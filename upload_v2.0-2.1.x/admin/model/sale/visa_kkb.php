<?php


class ModelSaleVisaKkb extends Model {

    public function getOrders($data = array()) {
        $sql = "SELECT o.order_id, CONCAT(o.firstname, ' ', o.lastname) AS customer,
		(SELECT os.name FROM " . DB_PREFIX . "order_status os WHERE os.order_status_id = o.order_status_id AND os.language_id = '" . (int) $this->config->get('config_language_id') . "') AS status,
		(SELECT vs.customer_reference FROM " . DB_PREFIX . "visa_kkb vs WHERE vs.visa_id = o.visa_kkb_id) AS customer_reference,
		(SELECT vs.transaction_status FROM " . DB_PREFIX . "visa_kkb vs WHERE vs.visa_id = o.visa_kkb_id) AS transaction_status,
		(SELECT vs.card_country FROM " . DB_PREFIX . "visa_kkb vs WHERE vs.visa_id = o.visa_kkb_id) AS card_country,
		(SELECT vs.card_number FROM " . DB_PREFIX . "visa_kkb vs WHERE vs.visa_id = o.visa_kkb_id) AS card_number,
		(SELECT vs.verified3d FROM " . DB_PREFIX . "visa_kkb vs WHERE vs.visa_id = o.visa_kkb_id) AS verified3d,
		(SELECT vs.ip_address FROM " . DB_PREFIX . "visa_kkb vs WHERE vs.visa_id = o.visa_kkb_id) AS ip_address,
                (SELECT vs.comment FROM " . DB_PREFIX . "visa_kkb vs WHERE vs.visa_id = o.visa_kkb_id) AS comment,
                (SELECT vs.approval_code FROM " . DB_PREFIX . "visa_kkb vs WHERE vs.visa_id = o.visa_kkb_id) AS approval_code,
		o.total, o.currency_code, o.currency_value, o.date_added, o.date_modified, o.visa_kkb_id FROM `" . DB_PREFIX . "order` o";

        if (isset($data['filter_order_status_id']) && !is_null($data['filter_order_status_id'])) {
            $sql .= " WHERE o.order_status_id = '" . (int) $data['filter_order_status_id'] . "'";
        } else {
            $sql .= " WHERE o.order_status_id > '0'";
        }

        if (isset($data['filter_visa_id']) && !is_null($data['filter_visa_id'])) {
            $sql .= " AND o.visa_kkb_id = '" . (int) $data['filter_visa_id'] . "'";
        } else {
            $sql .= " AND o.visa_kkb_id > '0'";
        }

        if (!empty($data['filter_order_id'])) {
            $sql .= " AND o.order_id = '" . (int) $data['filter_order_id'] . "'";
        }

        if (!empty($data['filter_customer'])) {
            $sql .= " AND CONCAT(o.firstname, ' ', o.lastname) LIKE '%" . $this->db->escape($data['filter_customer']) . "%'";
        }

        if (!empty($data['filter_date_added'])) {
            $sql .= " AND DATE(o.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
        }

        if (!empty($data['filter_date_modified'])) {
            $sql .= " AND DATE(o.date_modified) = DATE('" . $this->db->escape($data['filter_date_modified']) . "')";
        }

        if (!empty($data['filter_total'])) {
            $sql .= " AND o.total = '" . (float) $data['filter_total'] . "'";
        }

        $sort_data = array(
            'o.order_id',
            'customer',
            'status',
            'customer_reference',
            'transaction_status',
            'o.date_added',
            'o.date_modified',
            'o.total'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY o.order_id";
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int) $data['start'] . "," . (int) $data['limit'];
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function editVisa($data) {

        $this->db->query("UPDATE`" . DB_PREFIX . "visa_kkb` SET transaction_status = '" . $this->db->escape($data['transaction_status']) . "',
                comment = '" . $this->db->escape($data['comment']) . "',
		date_added = NOW() WHERE order_id = '" . (int) $data['order_id'] . "'");
    }

    public function addOrderHistory($order_id, $data) {
        $this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = '" . (int) $data['order_status_id'] . "', date_modified = NOW() WHERE order_id = '" . (int) $order_id . "'");

        $this->db->query("INSERT INTO " . DB_PREFIX . "order_history SET order_id = '" . (int) $order_id . "', order_status_id = '" . (int) $data['order_status_id'] . "',
			notify = '" . (isset($data['notify']) ? (int) $data['notify'] : 0) . "', date_added = NOW()");
    }

    public function createDatabaseTables() {
        $sql = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "visa_kkb` ( ";
        $sql .= "`visa_id` int(11) NOT NULL AUTO_INCREMENT, ";
        $sql .= "`order_id` int(11) NOT NULL, ";
        $sql .= "`customer_reference` varchar(20) NOT NULL DEFAULT '', ";
        $sql .= "`approval_code` varchar(20) NOT NULL DEFAULT '', ";
        $sql .= "`transaction_status` varchar(48) DEFAULT NULL, ";
        $sql .= "`date_added` datetime NOT NULL, ";
        $sql .= "`total` decimal(15,4) NOT NULL DEFAULT '0.0000', ";
        $sql .= "`card_country` varchar(255) NOT NULL, ";
        $sql .= "`card_number` varchar(20) NOT NULL, ";
        $sql .= "`verified3d` varchar(3) NOT NULL, ";
        $sql .= "`ip_address` varchar(15) NOT NULL, ";
        $sql .= "`comment` varchar(256) NOT NULL, ";
        $sql .= "PRIMARY KEY (`visa_id`) USING BTREE ";
        $sql .= ") ENGINE=MyISAM  DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=110 AUTO_INCREMENT=1 ;";
        $this->db->query($sql);

        $sql = "ALTER TABLE `" . DB_PREFIX . "order` ADD COLUMN `visa_kkb_id` INTEGER(11) NOT NULL;";
        $this->db->query($sql);
    }

    public function dropDatabaseTables() {
        $sql = "DROP TABLE IF EXISTS `" . DB_PREFIX . "visa_kkb`;";
        $this->db->query($sql);

        $sql = "ALTER TABLE `" . DB_PREFIX . "order` DROP COLUMN `visa_kkb_id`;";
        $this->db->query($sql);
    }

}

?>