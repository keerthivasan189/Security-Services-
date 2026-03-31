<h5 class="mb-3"><i class="bi bi-bar-chart me-2"></i>Report Hub</h5>
<p class="text-muted small mb-3">Generate advanced reports with filters, Excel export, and print functionality.</p>

<div class="row g-3">
  <?php
  $reports = [
      ['attendance','bi-calendar-check','#6c5ce7','Attendance Report','Client-wise attendance grid with daily status, present/absent/OT counts'],
      ['clients','bi-building','#00b894','Client Report','Client master list with deployed count, outstanding balance, branch filter'],
      ['employees','bi-people','#e17055','Employee Report','Employee list with designation, status, deployed client, search filter'],
      ['salary','bi-cash-stack','#0984e3','Salary Report','Full acquittance sheet with earnings, deductions, net salary breakdown'],
      ['invoices','bi-receipt','#fdcb6e','Invoice Report','Invoice list with month/client/status filters and totals summary'],
      ['payments','bi-credit-card','#d63031','Payments Report','Received payments with date range, client, and payment method filters'],
      ['outstanding','bi-exclamation-triangle','#636e72','Outstanding Report','Client-wise balance outstanding across all unpaid invoices'],
      ['expenses','bi-wallet2','#e84393','Expense Report','Combined fuel + misc expenses with type/date filters'],
      ['inventory','bi-box-seam','#00cec9','Inventory Report','Uniform items with vendor filter, issue count, and quantity tracking'],
      ['vendors','bi-truck','#74b9ff','Vendor Report','Vendor list with item count, total supplied value, search filter'],
      ['ledger','bi-journal-text','#a29bfe','Ledger Statement','Double-entry ledger with account/date filters and running balance'],
  ];
  foreach ($reports as $r): ?>
  <div class="col-md-4 col-sm-6">
    <a href="<?= u('reports/'.$r[0]) ?>" class="text-decoration-none">
      <div class="card h-100" style="border-left:4px solid <?= $r[2] ?>">
        <div class="card-body">
          <div class="d-flex align-items-center gap-2 mb-2">
            <i class="bi <?= $r[1] ?>" style="font-size:1.5rem;color:<?= $r[2] ?>"></i>
            <h6 class="mb-0"><?= $r[3] ?></h6>
          </div>
          <p class="small text-muted mb-2"><?= $r[4] ?></p>
          <div class="d-flex gap-1">
            <span class="badge bg-light text-dark border"><i class="bi bi-funnel me-1"></i>Filters</span>
            <span class="badge bg-light text-dark border"><i class="bi bi-file-earmark-excel me-1"></i>Excel</span>
            <span class="badge bg-light text-dark border"><i class="bi bi-printer me-1"></i>Print</span>
          </div>
        </div>
      </div>
    </a>
  </div>
  <?php endforeach; ?>
</div>
