<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="mb-0"><i class="bi bi-box-seam me-2"></i>Inventory Report</h5>
  <div class="d-flex gap-2 no-print">
    <?php $exUrl = BASE_URL.'/index.php?url=reports/inventory&export=excel&vendor_id='.urlencode($vendorId??''); ?>
    <a href="<?= $exUrl ?>" class="btn btn-success btn-sm"><i class="bi bi-file-earmark-excel me-1"></i>Excel</a>
    <button onclick="window.print()" class="btn btn-outline-secondary btn-sm"><i class="bi bi-printer me-1"></i>Print</button>
  </div>
</div>

<div class="card mb-3 no-print">
  <div class="card-body py-2">
    <form class="row g-2 align-items-end" method="GET" action="<?= BASE_URL ?>/index.php">
      <input type="hidden" name="url" value="reports/inventory">
      <div class="col-md-3"><label class="form-label small">Vendor</label>
        <select name="vendor_id" class="form-select form-select-sm">
          <option value="">All Vendors</option>
          <?php foreach ($vendors as $v): ?><option value="<?=$v['id']?>" <?=$vendorId==$v['id']?'selected':''?>><?=htmlspecialchars($v['vendor_name'])?></option><?php endforeach; ?>
        </select>
      </div>
      <div class="col-auto"><button class="btn btn-primary btn-sm"><i class="bi bi-search me-1"></i>Filter</button>
        <a href="<?= u('reports/inventory') ?>" class="btn btn-outline-secondary btn-sm">Clear</a></div>
    </form>
  </div>
</div>

<div class="row g-3 mb-3">
  <div class="col-md-4"><div class="stat-card"><div class="label">Total Items</div><div class="value text-primary"><?= count($items) ?></div></div></div>
  <div class="col-md-4"><div class="stat-card"><div class="label">Total Qty Issued</div><div class="value text-success"><?= array_sum(array_column($items,'total_qty_issued')) ?></div></div></div>
  <div class="col-md-4"><div class="stat-card"><div class="label">Total Value</div><div class="value text-warning"><?= Helper::money(array_sum(array_map(fn($i)=>$i['unit_price']*$i['total_qty_issued'],$items))) ?></div></div></div>
</div>

<div class="card">
  <div class="card-body p-0"><table class="table table-hover datatable mb-0">
    <thead class="table-light"><tr><th>#</th><th>Item Name</th><th>Vendor</th><th>Unit Price</th><th>Times Issued</th><th>Total Qty</th><th>Total Value</th></tr></thead>
    <tbody>
    <?php foreach ($items as $i => $it): ?>
    <tr>
      <td><?=$i+1?></td>
      <td class="fw-semibold"><?=htmlspecialchars($it['item_name'])?></td>
      <td><?=htmlspecialchars($it['vendor_name']??'—')?></td>
      <td><?=Helper::money($it['unit_price'])?></td>
      <td><?=$it['times_issued']?></td>
      <td class="fw-bold"><?=$it['total_qty_issued']?></td>
      <td class="text-success"><?=Helper::money($it['unit_price']*$it['total_qty_issued'])?></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table></div>
</div>
