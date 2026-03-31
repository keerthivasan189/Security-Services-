<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="mb-0 text-uppercase fw-semibold" style="color:#666">CLIENT CALL LOGS</h5>
</div>

<div class="card border-0 shadow-sm" style="border-radius:10px">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0 datatable" style="font-size:13px">
        <thead class="table-light text-muted">
          <tr>
            <th class="ps-3 border-0 fw-semibold">Date</th>
            <th class="border-0 fw-semibold">Client Company</th>
            <th class="border-0 fw-semibold">Contact Person</th>
            <th class="border-0 fw-semibold">Phone</th>
            <th class="border-0 fw-semibold">Type</th>
            <th class="border-0 fw-semibold">Subject / Notes</th>
            <th class="pe-3 border-0 fw-semibold">Follow Up</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($logs as $log): ?>
        <tr>
          <td class="ps-3 fw-semibold text-dark"><?= date('d-M-Y', strtotime($log['call_date'])) ?></td>
          <td class="fw-bold text-primary"><a href="<?= u('clients/profile/'.$log['client_id']) ?>" class="text-decoration-none"><?= htmlspecialchars($log['company_name']) ?></a></td>
          <td class="fw-semibold text-dark"><?= htmlspecialchars($log['contact_person'] ?? '') ?></td>
          <td class="text-muted"><?= htmlspecialchars($log['phone'] ?? '') ?></td>
          <td><span class="badge <?= $log['call_type']==='incoming'?'bg-success':'bg-info text-dark' ?> text-uppercase"><?= $log['call_type'] ?></span></td>
          <td class="text-muted small">
            <strong><?= htmlspecialchars($log['subject'] ?? '') ?></strong><br>
            <?= nl2br(htmlspecialchars($log['notes'] ?? '')) ?>
          </td>
          <td class="pe-3 fw-semibold text-danger">
            <?= $log['follow_up_date'] ? date('d-M-Y', strtotime($log['follow_up_date'])) : '—' ?>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if(empty($logs)): ?>
          <tr><td colspan="7" class="text-center text-muted py-4">No call logs found.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
