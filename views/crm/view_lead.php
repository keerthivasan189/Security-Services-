<?php
$statusConfig = [
    'new'           => ['label'=>'New',           'color'=>'#6c5ce7'],
    'contacted'     => ['label'=>'Contacted',      'color'=>'#0984e3'],
    'qualified'     => ['label'=>'Qualified',      'color'=>'#00b894'],
    'proposal_sent' => ['label'=>'Proposal Sent',  'color'=>'#f9a825'],
    'negotiation'   => ['label'=>'Negotiation',    'color'=>'#e17055'],
    'won'           => ['label'=>'Won',            'color'=>'#2e7d32'],
    'lost'          => ['label'=>'Lost',           'color'=>'#d63031'],
    'on_hold'       => ['label'=>'On Hold',        'color'=>'#636e72'],
];
$actIcons = [
    'call'=>'bi-telephone','meeting'=>'bi-people','email'=>'bi-envelope',
    'whatsapp'=>'bi-whatsapp text-success','site_visit'=>'bi-geo-alt',
    'demo'=>'bi-display','proposal'=>'bi-file-earmark-text',
    'follow_up'=>'bi-arrow-repeat','note'=>'bi-sticky'
];
$sc = $statusConfig[$lead['status']] ?? ['label'=>$lead['status'],'color'=>'#636e72'];
$isOpen = !in_array($lead['status'], ['won','lost']);
?>

<!-- Top Bar -->
<div class="d-flex flex-wrap gap-2 align-items-start justify-content-between mb-3">
  <div class="flex-grow-1" style="min-width:0">
    <h5 class="mb-1 fw-bold" style="font-size:clamp(13px,3vw,17px)">
      <?= htmlspecialchars($lead['company_name']) ?>
      <span class="badge ms-1 rounded-pill" style="background:<?= $sc['color'] ?>;font-size:11px"><?= $sc['label'] ?></span>
      <?php if ($lead['priority']==='high'): ?><span class="badge bg-danger ms-1 rounded-pill" style="font-size:10px">🔥 High</span><?php endif; ?>
    </h5>
    <small class="text-muted"><?= $lead['lead_code'] ?> · <?= date('d M Y', strtotime($lead['created_at'])) ?></small>
  </div>
  <div class="d-flex gap-1 flex-wrap flex-shrink-0">
    <a href="<?= u('crm/leads') ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i><span class="d-none d-sm-inline ms-1">Back</span></a>
    <a href="<?= u("crm/editLead/{$lead['id']}") ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i><span class="d-none d-sm-inline ms-1">Edit</span></a>
    <?php if ($isOpen): ?>
    <a href="<?= u("crm/convertToClient/{$lead['id']}") ?>" class="btn btn-sm btn-success"><i class="bi bi-arrow-right-circle"></i><span class="d-none d-sm-inline ms-1">Convert</span></a>
    <?php elseif ($lead['status']==='won' && $lead['converted_client_id']): ?>
    <a href="<?= u("clients/profile/{$lead['converted_client_id']}") ?>" class="btn btn-sm btn-success"><i class="bi bi-building"></i><span class="d-none d-sm-inline ms-1">Client</span></a>
    <?php endif; ?>
    <a href="<?= u("crm/deleteLead/{$lead['id']}") ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this lead?')"><i class="bi bi-trash"></i></a>
  </div>
</div>

<div class="row g-3">

  <!-- Left: Lead Info -->
  <div class="col-lg-4">
    <!-- Contact Info -->
    <div class="card mb-3">
      <div class="card-header"><i class="bi bi-person-lines-fill me-1"></i> Contact Info</div>
      <div class="card-body p-3" style="font-size:13px">
        <table class="table table-borderless mb-0" style="font-size:13px">
          <tr><td class="text-muted pe-2 py-1">Contact</td><td class="fw-semibold"><?= htmlspecialchars($lead['contact_person'] ?: '—') ?></td></tr>
          <tr><td class="text-muted pe-2 py-1">Mobile</td><td><a href="tel:<?= $lead['mobile'] ?>"><?= $lead['mobile'] ?: '—' ?></a></td></tr>
          <tr><td class="text-muted pe-2 py-1">Phone</td><td><?= $lead['phone'] ?: '—' ?></td></tr>
          <tr><td class="text-muted pe-2 py-1">Email</td><td><?= $lead['email'] ? '<a href="mailto:'.$lead['email'].'">'.$lead['email'].'</a>' : '—' ?></td></tr>
          <tr><td class="text-muted pe-2 py-1">Industry</td><td><?= htmlspecialchars($lead['industry'] ?: '—') ?></td></tr>
          <tr><td class="text-muted pe-2 py-1">District</td><td><?= $lead['district'] ?>, <?= $lead['state'] ?></td></tr>
          <?php if ($lead['address']): ?>
          <tr><td class="text-muted pe-2 py-1">Address</td><td><?= nl2br(htmlspecialchars($lead['address'])) ?></td></tr>
          <?php endif; ?>
        </table>
      </div>
    </div>

    <!-- Lead Details -->
    <div class="card mb-3">
      <div class="card-header"><i class="bi bi-graph-up me-1"></i> Lead Details</div>
      <div class="card-body p-3">
        <table class="table table-borderless mb-0" style="font-size:13px">
          <tr><td class="text-muted pe-2 py-1">Source</td><td><span class="badge" style="background:#f0eeff;color:#6c5ce7"><?= $lead['source'] ?></span></td></tr>
          <?php if ($lead['reference_name']): ?>
          <tr><td class="text-muted pe-2 py-1">Reference</td><td><?= htmlspecialchars($lead['reference_name']) ?></td></tr>
          <?php endif; ?>
          <tr><td class="text-muted pe-2 py-1">Assigned To</td><td><?= htmlspecialchars($lead['assigned_to'] ?: '—') ?></td></tr>
          <tr><td class="text-muted pe-2 py-1">Strength</td><td><?= $lead['expected_strength'] ?> pax</td></tr>
          <tr><td class="text-muted pe-2 py-1">Monthly Value</td><td class="fw-bold" style="color:#6c5ce7">₹<?= number_format($lead['expected_value']) ?></td></tr>
          <tr><td class="text-muted pe-2 py-1">Annual Value</td><td>₹<?= number_format($lead['expected_value']*12) ?></td></tr>
          <tr><td class="text-muted pe-2 py-1">Follow-up</td>
            <td class="<?= ($lead['follow_up_date'] && $lead['follow_up_date'] < date('Y-m-d') && $isOpen) ? 'text-danger fw-bold' : '' ?>">
              <?= $lead['follow_up_date'] ?: '—' ?>
            </td>
          </tr>
          <?php if ($lead['service_needed']): ?>
          <tr><td class="text-muted pe-2 py-1 align-top">Service</td><td><?= nl2br(htmlspecialchars($lead['service_needed'])) ?></td></tr>
          <?php endif; ?>
          <?php if ($lead['remarks']): ?>
          <tr><td class="text-muted pe-2 py-1 align-top">Remarks</td><td><?= nl2br(htmlspecialchars($lead['remarks'])) ?></td></tr>
          <?php endif; ?>
          <?php if ($lead['lost_reason']): ?>
          <tr><td class="text-muted pe-2 py-1 align-top">Lost Reason</td><td class="text-danger"><?= nl2br(htmlspecialchars($lead['lost_reason'])) ?></td></tr>
          <?php endif; ?>
        </table>
      </div>
    </div>

    <!-- Quick Status Update -->
    <?php if ($isOpen): ?>
    <div class="card mb-3">
      <div class="card-header"><i class="bi bi-arrow-repeat me-1"></i> Quick Update Status</div>
      <div class="card-body p-3">
        <div class="d-flex flex-wrap gap-1">
          <?php $stages=['new','contacted','qualified','proposal_sent','negotiation','won','lost','on_hold'];
          $labels=['new'=>'New','contacted'=>'Contacted','qualified'=>'Qualified','proposal_sent'=>'Proposal','negotiation'=>'Negotiation','won'=>'Won','lost'=>'Lost','on_hold'=>'On Hold'];
          foreach ($stages as $st): ?>
          <button onclick="updateStatus(<?= $lead['id'] ?>,'<?= $st ?>')"
            class="btn btn-sm <?= $lead['status']===$st ? 'btn-primary' : 'btn-outline-secondary' ?>"
            style="font-size:11px">
            <?= $labels[$st] ?>
          </button>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
    <?php endif; ?>
  </div>

  <!-- Right: Activities + Proposals -->
  <div class="col-lg-8">

    <!-- Log Activity -->
    <?php if ($isOpen): ?>
    <div class="card mb-3">
      <div class="card-header" style="cursor:pointer" data-bs-toggle="collapse" data-bs-target="#activityForm">
        <i class="bi bi-plus-circle me-1" style="color:#6c5ce7"></i> Log Activity / Note
        <i class="bi bi-chevron-down ms-auto float-end" style="font-size:10px;margin-top:3px"></i>
      </div>
      <div class="collapse show" id="activityForm">
        <div class="card-body">
          <form method="POST" action="<?= u("crm/addActivity/{$lead['id']}") ?>">
            <div class="row g-2">
              <div class="col-md-3">
                <label class="form-label small fw-semibold">Type</label>
                <select name="activity_type" class="form-select form-select-sm">
                  <option value="call">📞 Call</option>
                  <option value="meeting">👥 Meeting</option>
                  <option value="email">✉️ Email</option>
                  <option value="whatsapp">💬 WhatsApp</option>
                  <option value="site_visit">📍 Site Visit</option>
                  <option value="demo">🖥️ Demo</option>
                  <option value="proposal">📄 Proposal</option>
                  <option value="follow_up">🔁 Follow-up</option>
                  <option value="note" selected>📝 Note</option>
                </select>
              </div>
              <div class="col-md-3">
                <label class="form-label small fw-semibold">Date</label>
                <input type="date" name="activity_date" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>" required>
              </div>
              <div class="col-md-2">
                <label class="form-label small fw-semibold">Time</label>
                <input type="time" name="activity_time" class="form-control form-control-sm">
              </div>
              <div class="col-md-2">
                <label class="form-label small fw-semibold">Duration (min)</label>
                <input type="number" name="duration_min" class="form-control form-control-sm" min="0" value="0">
              </div>
              <div class="col-md-2">
                <label class="form-label small fw-semibold">Done By</label>
                <input type="text" name="done_by" class="form-control form-control-sm" placeholder="Your name">
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-semibold">Subject</label>
                <input type="text" name="subject" class="form-control form-control-sm" placeholder="Brief subject">
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-semibold">Outcome</label>
                <input type="text" name="outcome" class="form-control form-control-sm" placeholder="Result / outcome">
              </div>
              <div class="col-12">
                <label class="form-label small fw-semibold">Description</label>
                <textarea name="description" class="form-control form-control-sm" rows="2" placeholder="Detailed notes..."></textarea>
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-semibold">Next Action</label>
                <input type="text" name="next_action" class="form-control form-control-sm" placeholder="What to do next">
              </div>
              <div class="col-md-3">
                <label class="form-label small fw-semibold">Next Action Date</label>
                <input type="date" name="next_action_date" class="form-control form-control-sm">
              </div>
              <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary btn-sm w-100"><i class="bi bi-check-lg me-1"></i>Save Activity</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <!-- Proposals -->
    <div class="card mb-3">
      <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-file-earmark-text me-1"></i> Proposals (<?= count($proposals) ?>)</span>
        <?php if ($isOpen): ?>
        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#proposalForm">
          <i class="bi bi-plus-lg me-1"></i>Add Proposal
        </button>
        <?php endif; ?>
      </div>
      <?php if ($isOpen): ?>
      <div class="collapse" id="proposalForm">
        <div class="card-body border-bottom" style="background:#f8f9fa">
          <form method="POST" action="<?= u("crm/addProposal/{$lead['id']}") ?>">
            <div class="row g-2">
              <div class="col-md-6">
                <label class="form-label small fw-semibold">Title</label>
                <input type="text" name="title" class="form-control form-control-sm" placeholder="Proposal title" required>
              </div>
              <div class="col-md-3">
                <label class="form-label small fw-semibold">Manpower</label>
                <input type="number" name="manpower" class="form-control form-control-sm" min="0" placeholder="No. of staff">
              </div>
              <div class="col-md-3">
                <label class="form-label small fw-semibold">Monthly Value (₹)</label>
                <input type="number" name="monthly_value" class="form-control form-control-sm" step="0.01" min="0" required>
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-semibold">Service Details</label>
                <textarea name="service_details" class="form-control form-control-sm" rows="2"></textarea>
              </div>
              <div class="col-md-2">
                <label class="form-label small fw-semibold">Validity (days)</label>
                <input type="number" name="validity_days" class="form-control form-control-sm" value="30">
              </div>
              <div class="col-md-2">
                <label class="form-label small fw-semibold">Sent Date</label>
                <input type="date" name="sent_date" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>">
              </div>
              <div class="col-md-2">
                <label class="form-label small fw-semibold">Status</label>
                <select name="status" class="form-select form-select-sm">
                  <option value="draft">Draft</option>
                  <option value="sent" selected>Sent</option>
                  <option value="accepted">Accepted</option>
                  <option value="rejected">Rejected</option>
                  <option value="revised">Revised</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-semibold">Remarks</label>
                <input type="text" name="remarks" class="form-control form-control-sm">
              </div>
              <div class="col-md-6 d-flex align-items-end">
                <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-check-lg me-1"></i>Save Proposal</button>
              </div>
            </div>
          </form>
        </div>
      </div>
      <?php endif; ?>
      <div class="card-body p-0">
        <?php if (empty($proposals)): ?>
          <div class="text-center text-muted py-3" style="font-size:13px">No proposals yet</div>
        <?php else: foreach ($proposals as $p): ?>
        <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
          <div>
            <div class="fw-semibold" style="font-size:13px"><?= htmlspecialchars($p['title']) ?> <small class="text-muted">(<?= $p['proposal_no'] ?>)</small></div>
            <div style="font-size:11px;color:#888">Manpower: <?= $p['manpower'] ?> · Monthly: ₹<?= number_format($p['monthly_value']) ?> · Annual: ₹<?= number_format($p['annual_value']) ?></div>
            <?php if ($p['sent_date']): ?><div style="font-size:11px;color:#888">Sent: <?= $p['sent_date'] ?> · Valid <?= $p['validity_days'] ?> days</div><?php endif; ?>
          </div>
          <?php
          $ps=['draft'=>['Draft','secondary'],'sent'=>['Sent','primary'],'accepted'=>['Accepted','success'],'rejected'=>['Rejected','danger'],'revised'=>['Revised','warning']];
          $psi = $ps[$p['status']] ?? [$p['status'],'secondary'];
          ?>
          <span class="badge bg-<?= $psi[1] ?>"><?= $psi[0] ?></span>
        </div>
        <?php endforeach; endif; ?>
      </div>
    </div>

    <!-- Attachments -->
    <div class="card mb-3">
      <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-paperclip me-1"></i> Attachments (<?= count($attachments) ?>)</span>
        <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#attachForm">
          <i class="bi bi-upload me-1"></i>Upload
        </button>
      </div>
      <div class="collapse" id="attachForm">
        <div class="card-body border-bottom" style="background:#f8f9fa">
          <form method="POST" action="<?= u("crm/uploadAttachment/{$lead['id']}") ?>" enctype="multipart/form-data">
            <div class="row g-2 align-items-end">
              <div class="col-12 col-sm-8">
                <label class="form-label small fw-semibold">Choose File <span class="text-muted">(PDF, Word, Excel, Image — max 10 MB)</span></label>
                <input type="file" name="attachment" class="form-control form-control-sm"
                       accept=".jpg,.jpeg,.png,.gif,.pdf,.webp,.doc,.docx,.xls,.xlsx,.txt,.csv" required>
              </div>
              <div class="col-12 col-sm-4">
                <button type="submit" class="btn btn-primary btn-sm w-100"><i class="bi bi-cloud-upload me-1"></i>Upload</button>
              </div>
            </div>
          </form>
        </div>
      </div>
      <div class="card-body p-0">
        <?php if (empty($attachments)): ?>
          <div class="text-center text-muted py-3" style="font-size:13px">No attachments yet</div>
        <?php else:
          $iconMap = [
            'pdf'  => ['bi-file-earmark-pdf text-danger',   'PDF'],
            'doc'  => ['bi-file-earmark-word text-primary',  'DOC'],
            'docx' => ['bi-file-earmark-word text-primary',  'DOCX'],
            'xls'  => ['bi-file-earmark-excel text-success', 'XLS'],
            'xlsx' => ['bi-file-earmark-excel text-success', 'XLSX'],
            'txt'  => ['bi-file-earmark-text text-secondary','TXT'],
            'csv'  => ['bi-file-earmark-spreadsheet text-success','CSV'],
            'jpg'  => ['bi-file-earmark-image text-warning', 'JPG'],
            'jpeg' => ['bi-file-earmark-image text-warning', 'JPEG'],
            'png'  => ['bi-file-earmark-image text-info',   'PNG'],
            'gif'  => ['bi-file-earmark-image text-info',   'GIF'],
            'webp' => ['bi-file-earmark-image text-info',   'WEBP'],
          ];
          foreach ($attachments as $att):
            $ico = $iconMap[$att['file_type']] ?? ['bi-file-earmark text-secondary', strtoupper($att['file_type'])];
            $sz  = $att['file_size'] < 1048576
                   ? round($att['file_size']/1024, 1).' KB'
                   : round($att['file_size']/1048576, 1).' MB';
        ?>
        <div class="d-flex align-items-center gap-3 p-3 border-bottom">
          <i class="bi <?= $ico[0] ?>" style="font-size:24px;flex-shrink:0"></i>
          <div class="flex-grow-1" style="min-width:0">
            <div class="fw-semibold text-truncate" style="font-size:13px" title="<?= htmlspecialchars($att['original_name']) ?>">
              <?= htmlspecialchars($att['original_name']) ?>
            </div>
            <small class="text-muted"><?= $ico[1] ?> · <?= $sz ?> · <?= date('d M Y, H:i', strtotime($att['uploaded_at'])) ?></small>
          </div>
          <div class="d-flex gap-1 flex-shrink-0">
            <a href="<?= u("crm/serveAttachment/{$att['id']}") ?>" target="_blank"
               class="btn btn-sm btn-outline-primary" title="View / Download" style="padding:4px 8px">
              <i class="bi bi-eye"></i>
            </a>
            <a href="<?= u("crm/deleteAttachment/{$att['id']}") ?>"
               class="btn btn-sm btn-outline-danger" title="Delete"
               onclick="return confirm('Delete this attachment?')" style="padding:4px 8px">
              <i class="bi bi-trash"></i>
            </a>
          </div>
        </div>
        <?php endforeach; endif; ?>
      </div>
    </div>

    <!-- Activity Timeline -->
    <div class="card">
      <div class="card-header"><i class="bi bi-clock-history me-1"></i> Activity Timeline (<?= count($activities) ?>)</div>
      <div class="card-body p-0">
        <?php if (empty($activities)): ?>
          <div class="text-center text-muted py-4" style="font-size:13px">No activities logged yet</div>
        <?php else: ?>
        <div style="max-height:500px;overflow-y:auto">
          <?php foreach ($activities as $a): ?>
          <div class="d-flex gap-3 p-3 border-bottom">
            <div class="flex-shrink-0">
              <span class="rounded-circle d-flex align-items-center justify-content-center" style="width:36px;height:36px;background:#f0eeff;color:#6c5ce7">
                <i class="bi <?= $actIcons[$a['activity_type']] ?? 'bi-dot' ?>"></i>
              </span>
            </div>
            <div class="flex-grow-1">
              <div class="d-flex justify-content-between">
                <strong style="font-size:13px"><?= ucfirst(str_replace('_',' ',$a['activity_type'])) ?></strong>
                <small class="text-muted"><?= $a['activity_date'] ?><?= $a['activity_time'] ? ' '.substr($a['activity_time'],0,5) : '' ?><?= $a['duration_min'] ? ' ('.$a['duration_min'].' min)' : '' ?></small>
              </div>
              <?php if ($a['subject']): ?><div style="font-size:13px;font-weight:500"><?= htmlspecialchars($a['subject']) ?></div><?php endif; ?>
              <?php if ($a['description']): ?><div style="font-size:12px;color:#555"><?= nl2br(htmlspecialchars($a['description'])) ?></div><?php endif; ?>
              <?php if ($a['outcome']): ?><div style="font-size:12px" class="text-success">✔ <?= htmlspecialchars($a['outcome']) ?></div><?php endif; ?>
              <?php if ($a['next_action']): ?><div style="font-size:12px;color:#0984e3">→ <?= htmlspecialchars($a['next_action']) ?><?= $a['next_action_date'] ? ' by '.$a['next_action_date'] : '' ?></div><?php endif; ?>
              <?php if ($a['done_by']): ?><small class="text-muted">by <?= htmlspecialchars($a['done_by']) ?></small><?php endif; ?>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Attachments -->
    <div class="card mt-3">
      <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-paperclip me-1"></i> Attachments (<?= count($attachments ?? []) ?>)</span>
        <?php if ($isOpen): ?>
        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#attachmentForm">
          <i class="bi bi-upload me-1"></i>Upload File
        </button>
        <?php endif; ?>
      </div>
      <?php if ($isOpen): ?>
      <div class="collapse" id="attachmentForm">
        <div class="card-body border-bottom" style="background:#f8f9fa">
          <form id="attachmentUploadForm" method="POST" action="<?= u("crm/uploadAttachment/{$lead['id']}") ?>" enctype="multipart/form-data">
            <div class="row g-2">
              <div class="col-md-8">
                <label class="form-label small fw-semibold">Select File (Max 5MB)</label>
                <input type="file" id="attachmentFile" name="file" class="form-control form-control-sm" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.gif,.txt,.csv" required>
                <small class="text-muted">Allowed: PDF, Word, Excel, Images, CSV, Text</small>
              </div>
              <div class="col-md-2">
                <label class="form-label small fw-semibold">Uploaded By</label>
                <input type="text" name="uploaded_by" class="form-control form-control-sm" placeholder="Your name">
              </div>
              <div class="col-md-2 d-flex align-items-end">
                <button type="button" id="attachmentUploadBtn" class="btn btn-primary btn-sm w-100">
                  <i class="bi bi-upload me-1"></i>Upload
                </button>
              </div>
            </div>
            <div id="uploadProgress" class="mt-2" style="display:none">
              <div class="progress" style="height:20px">
                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
              </div>
            </div>
          </form>
        </div>
      </div>
      <?php endif; ?>
      <div class="card-body p-0">
        <?php if (empty($attachments)): ?>
          <div class="text-center text-muted py-3" style="font-size:13px">No attachments yet</div>
        <?php else: foreach ($attachments as $att): ?>
        <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
          <div>
            <div class="fw-semibold" style="font-size:13px">
              <i class="bi bi-file me-1"></i><?= htmlspecialchars($att['file_name']) ?>
              <small class="text-muted">(<?= number_format($att['file_size']/1024, 1) ?>KB)</small>
            </div>
            <small class="text-muted">Uploaded <?= date('d M Y H:i', strtotime($att['uploaded_at'])) ?> by <?= htmlspecialchars($att['uploaded_by']) ?></small>
          </div>
          <div class="d-flex gap-1">
            <a href="<?= u("crm/downloadAttachment/{$att['id']}") ?>" class="btn btn-sm btn-outline-primary" title="Download">
              <i class="bi bi-download"></i>
            </a>
            <?php if ($isOpen): ?>
            <button onclick="deleteAttachment(<?= $att['id'] ?>)" class="btn btn-sm btn-outline-danger" title="Delete">
              <i class="bi bi-trash"></i>
            </button>
            <?php endif; ?>
          </div>
        </div>
        <?php endforeach; endif; ?>
      </div>
    </div>

  </div>
</div>

<script>
function updateStatus(id, status) {
    if (!confirm('Update status to "' + status + '"?')) return;
    fetch('<?= BASE_URL ?>/index.php?url=crm/updateStatus/' + id, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'status=' + encodeURIComponent(status)
    }).then(r => r.json()).then(d => {
        if (d.ok) location.reload();
        else alert('Failed to update status');
    });
}

function deleteAttachment(attId) {
    if (!confirm('Delete this attachment?')) return;
    fetch('<?= BASE_URL ?>/index.php?url=crm/deleteAttachment/' + attId, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
    }).then(r => r.json()).then(d => {
        if (d.ok) location.reload();
        else alert('Failed to delete attachment');
    });
}

document.getElementById('attachmentUploadBtn')?.addEventListener('click', function(e) {
    e.preventDefault();
    const form = document.getElementById('attachmentUploadForm');
    const fileInput = document.getElementById('attachmentFile');
    const progressDiv = document.getElementById('uploadProgress');
    const progressBar = progressDiv.querySelector('.progress-bar');

    if (!fileInput.files.length) {
        alert('Please select a file');
        return;
    }

    const formData = new FormData(form);
    progressDiv.style.display = 'block';
    progressBar.style.width = '0%';

    const xhr = new XMLHttpRequest();
    xhr.upload.addEventListener('progress', function(e) {
        if (e.lengthComputable) {
            const percent = (e.loaded / e.total) * 100;
            progressBar.style.width = percent + '%';
        }
    });

    xhr.addEventListener('load', function() {
        try {
            const response = JSON.parse(xhr.responseText);
            if (response.ok) {
                alert('File uploaded successfully');
                location.reload();
            } else {
                alert('Upload failed: ' + response.msg);
            }
        } catch (e) {
            alert('Upload failed. Please try again.');
        }
        progressDiv.style.display = 'none';
    });

    xhr.addEventListener('error', function() {
        alert('Upload failed. Please check your connection.');
        progressDiv.style.display = 'none';
    });

    xhr.open('POST', form.action);
    xhr.send(formData);
});
</script>
