<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="mb-0"><i class="bi bi-calendar-plus me-2"></i>Single Attendance Entry</h5>
</div>

<div class="card" style="max-width:750px">
  <div class="card-header">Mark attendance for one employee</div>
  <div class="card-body">
    <form method="POST" id="singleForm">
      <input type="hidden" name="save_single" value="1">
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label fw-semibold small">Company <span class="text-danger">*</span></label>
          <select name="client_id" id="clientSel" class="form-select" required onchange="loadEmployees(this.value)">
            <option value="">— Select Company —</option>
            <?php foreach ($clients as $c): ?>
            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['company_name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold small">Employee <span class="text-danger">*</span></label>
          <select name="employee_id" id="empSel" class="form-select" required onchange="checkExisting()">
            <option value="">— Select Employee —</option>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold small">Trade with Shift</label>
          <select name="trade_id" id="tradeSel" class="form-select">
            <option value="">— Auto from employee —</option>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label fw-semibold small">From Date <span class="text-danger">*</span></label>
          <input type="date" name="from_date" id="fromDate" class="form-control" value="<?= date('Y-m-d') ?>" required onchange="checkExisting()">
        </div>
        <div class="col-md-3">
          <label class="form-label fw-semibold small">To Date <span class="text-danger">*</span></label>
          <input type="date" name="to_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
        </div>
        <div class="col-md-5">
          <label class="form-label fw-semibold small">Attendance Status</label>
          <select name="status" id="statusSel" class="form-select">
            <option value="P">Present</option>
            <option value="HD">Half Day</option>
            <option value="A">Absent</option>
            <option value="OFF">Company Off</option>
            <option value="OT">Overtime</option>
          </select>
        </div>
        <div class="col-md-7">
          <label class="form-label fw-semibold small">Remarks</label>
          <input type="text" name="remarks" class="form-control" placeholder="Optional remarks">
        </div>

        <!-- Already-marked warning -->
        <div class="col-12" id="alreadyMarkedBox" style="display:none">
          <div class="alert alert-warning py-2 mb-0 d-flex align-items-center gap-2">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <span id="alreadyMarkedMsg"></span>
            <small class="text-muted ms-1">(Will be overwritten on save)</small>
          </div>
        </div>

        <div class="col-12">
          <button type="submit" class="btn btn-primary px-4">
            <i class="bi bi-save me-2"></i>Save Attendance
          </button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
const BASE_URL = '<?= BASE_URL ?>';

function loadEmployees(clientId){
  if(!clientId) return;
  fetch(BASE_URL + '/index.php?url=attendance/getEmployees&client_id=' + clientId)
    .then(r => r.json())
    .then(data => {
      const sel  = document.getElementById('empSel');
      const tSel = document.getElementById('tradeSel');
      sel.innerHTML  = '<option value="">— Select Employee —</option>';
      tSel.innerHTML = '<option value="">— Select Trade —</option>';
      data.forEach(e => {
        sel.innerHTML  += `<option value="${e.id}">${e.name}</option>`;
        tSel.innerHTML += `<option value="${e.trade_id}">${e.trade_label}</option>`;
      });
      document.getElementById('alreadyMarkedBox').style.display = 'none';
    });
}

function checkExisting(){
  const empId    = document.getElementById('empSel').value;
  const clientId = document.getElementById('clientSel').value;
  const date     = document.getElementById('fromDate').value;
  const box      = document.getElementById('alreadyMarkedBox');
  const msg      = document.getElementById('alreadyMarkedMsg');
  const statusSel = document.getElementById('statusSel');

  if(!empId || !clientId || !date){ box.style.display='none'; return; }

  fetch(`${BASE_URL}/index.php?url=attendance/checkAttendance&employee_id=${empId}&client_id=${clientId}&date=${date}`)
    .then(r => r.json())
    .then(data => {
      if(data && data.status){
        const labels = {P:'Present',A:'Absent',OFF:'Company Off',OT:'Overtime',HD:'Half Day'};
        msg.textContent = `Already marked as: ${labels[data.status] || data.status}${data.remarks ? ' — ' + data.remarks : ''}`;
        box.style.display = 'flex';
        statusSel.value = data.status; // pre-fill with existing
      } else {
        box.style.display = 'none';
      }
    });
}
</script>
