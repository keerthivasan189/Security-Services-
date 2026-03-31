<?php
require_once BASE_PATH . '/controllers/BaseController.php';

class InventoryController extends BaseController {

    public function index(?string $param = null): void {
        if (Helper::isPost() && Helper::post('action') === 'add') {
            $this->execute(
                "INSERT INTO vendors (vendor_name, contact_name, mobile, address) VALUES (?,?,?,?)",
                [Helper::post('vendor_name'), Helper::post('contact_name'), Helper::post('mobile'), Helper::post('address')]
            );
            Session::flash('success', 'Vendor added successfully.');
            $this->redirect('inventory/index');
        }

        if (Helper::isPost() && Helper::post('action') === 'delete') {
            $this->execute("DELETE FROM vendors WHERE id=?", [Helper::post('vendor_id')]);
            Session::flash('success', 'Vendor deleted.');
            $this->redirect('inventory/index');
        }

        $vendors = $this->fetchAll("SELECT * FROM vendors ORDER BY vendor_name");
        $this->view('inventory/index', compact('vendors')
            + ['pageTitle' => 'Vendor Master', 'active' => 'inventory']);
    }

    public function editVendor(?string $id = null): void {
        $vendor = $this->fetchOne("SELECT * FROM vendors WHERE id=?", [$id]);
        if (!$vendor) { Session::flash('error','Vendor not found.'); $this->redirect('inventory/index'); }

        if (Helper::isPost()) {
            $this->execute(
                "UPDATE vendors SET vendor_name=?, contact_name=?, mobile=?, address=? WHERE id=?",
                [Helper::post('vendor_name'), Helper::post('contact_name'), Helper::post('mobile'), Helper::post('address'), $id]
            );
            Session::flash('success', 'Vendor updated.');
            $this->redirect('inventory/index');
        }
        $this->view('inventory/editvendor', compact('vendor')
            + ['pageTitle' => 'Edit Vendor', 'active' => 'inventory']);
    }

    public function items(?string $param = null): void {
        $vendors = $this->fetchAll("SELECT id, vendor_name FROM vendors ORDER BY vendor_name");

        if (Helper::isPost() && Helper::post('action') === 'add') {
            $this->execute(
                "INSERT INTO uniform_items (item_name, vendor_id, unit_price) VALUES (?,?,?)",
                [Helper::post('item_name'), Helper::post('vendor_id') ?: null, (float)(Helper::post('unit_price') ?: 0)]
            );
            Session::flash('success', 'Item added.');
            $this->redirect('inventory/items');
        }

        if (Helper::isPost() && Helper::post('action') === 'delete') {
            $this->execute("DELETE FROM uniform_items WHERE id=?", [Helper::post('item_id')]);
            Session::flash('success', 'Item deleted.');
            $this->redirect('inventory/items');
        }

        $items = $this->fetchAll("
            SELECT ui.*, v.vendor_name
            FROM uniform_items ui
            LEFT JOIN vendors v ON v.id = ui.vendor_id
            ORDER BY ui.item_name
        ");
        $this->view('inventory/items', compact('items','vendors')
            + ['pageTitle' => 'Uniform Item Catalog', 'active' => 'inventory']);
    }
}
