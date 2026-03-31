<?php
/**
 * HRMS Quick Data Seeder — 100 records per section
 * Run: http://localhost/hrms/seed_data.php
 */
set_time_limit(300);
ini_set('memory_limit', '256M');
require_once __DIR__ . '/config/db.php';

echo "<pre>\n=== HRMS QUICK SEEDER (100 per section) ===\n\n";
$total = 0;

// ── HELPERS ──
$firstNames = ['RAJASEKARAN','MURUGAN','SELVAM','KUMAR','RAVI','PRAKASH','SURESH','RAMESH','GANESH','VIJAY',
    'KARTHIK','SENTHIL','MANIKANDAN','ARUN','BALA','DINESH','GOPAL','HARI','JAYAKUMAR','KANNAN',
    'LAKSHMI','MEENA','NIRMALA','PRIYA','REVATHI','SARANYA','THANGAM','UMA','VANITHA','YAMUNA',
    'ANBU','CHELLAMMAL','DEVI','ESWARI','FATIMA','GAYATHRI','HEMA','INDRA','JAYA','KAVITHA',
    'MANI','NAGESH','PALANI','RAJA','SATHYA','THIRU','VELU','ARJUN','BABU','CHANDRAN'];
$lastNames  = ['S','R','K','M','P','N','V','L','G','T'];
$designations = ['SECURITY GUARD','SECURITY OFFICER','AREA OFFICER','LADY SECURITY GUARD','DRIVER','OFFICE BOY','HOUSE KEEPING'];
$shifts    = ['DAY SHIFT','NIGHT SHIFT','GENERAL SHIFT'];
$banks     = ['STATE BANK OF INDIA','INDIAN BANK','CENTRAL BANK OF INDIA','BANK OF BARODA','CANARA BANK','HDFC BANK'];
$companies = ['E.I.D. PARRY INDIA LTD','TAMIL NADU MARITIME BOARD','EMPLOYEES PROVIDENT FUND ORGANISATION',
    'MAHATHMA GANDHI COLLEGE','BHUVANESWARI CIVIL ENGINEERS PVT LTD','VELL BISCUITS PVT LTD',
    'NEYVELI LIGNITE CORPORATION','NLC INDIA LIMITED','SICAL LOGISTICS LIMITED','PONNI SUGARS',
    'RAJSHREE SUGARS','CHETTINAD CEMENT','DALMIA BHARAT LTD','INDIA CEMENTS','RAMCO INDUSTRIES',
    'TVS MOTOR COMPANY','ASHOK LEYLAND','TITAN INDUSTRIES','LUCAS TVS','MURUGAPPA GROUP',
    'CRI PUMPS','ELGI EQUIPMENTS','PRICOL LIMITED','RANE GROUP','SONA KOYO',
    'WHEELS INDIA','BRAKES INDIA','SUNDARAM FASTENERS','SUPER SPINNING MILLS','LOYAL TEXTILE',
    'BANNARI AMMAN SUGARS','EID PARRY SUGAR','TANFAC INDUSTRIES','KAVERI SEEDS','MAHINDRA HOLIDAYS',
    'GRT HOTELS','HOTEL SANGAM','APPOLO HOSPITALS','GVN HOSPITAL','KUMARAN HOSPITAL',
    'ABIRAMI HOSPITAL','DISTRICT COURT','COLLECTOR OFFICE','MUNICIPAL CORPORATION','AAVIN DAIRY',
    'TANGEDCO','BSNL EXCHANGE','SRM UNIVERSITY','ANNAMALAI UNIVERSITY','BHARATHIDASAN UNIVERSITY',
    'NATIONAL INSURANCE','ORIENTAL INSURANCE','WAREHOUSING CORPORATION','FOOD CORPORATION','TOLL PLAZA NORTH',
    'TOLL PLAZA SOUTH','RAILWAY STATION','BUS STAND','FISHING HARBOUR','PORT TRUST',
    'THERMAL POWER STATION','SOLAR PARK','WATER TREATMENT PLANT','PUBLIC WORKS DEPT','HIGH COURT BENCH',
    'KAUVERY HOSPITAL','MEENAKSHI HOSPITAL','IT PARK PHASE 1','SIPCOT INDUSTRIAL ESTATE','ZOO PARK',
    'SPORTS AUTHORITY','STADIUM COMPLEX','CONVENTION CENTRE','SHOPPING MALL','JEWELLERY SHOWROOM',
    'BANK OF INDIA BRANCH','SBI BRANCH','ATM SECURITY NORTH','ATM SECURITY SOUTH','COLD STORAGE FACILITY',
    'PHARMA WAREHOUSE','VETERINARY HOSPITAL','DAIRY FARM','COCONUT BOARD','SPICE BOARD',
    'HANDLOOM SOCIETY','KHADI BOARD','GEMS AND JEWELLERY','CHAMBER OF COMMERCE','TRADE CENTRE',
    'SHRI HOSPITAL','SREE BALAJI HOSPITAL','ORDNANCE FACTORY','PAPER MILLS','LEATHER EXPORT ZONE',
    'TEMPLE ADMINISTRATION','CHURCH COUNCIL','GURUDWARA COMMITTEE','SWIMMING POOL COMPLEX','HYPERMARKET',
    'TEXTILE SHOWROOM','AUTOMOBILE SHOWROOM','PNB BRANCH','IOB BRANCH','ATM SECURITY EAST'];
$scheduleTypes = ['EVERY MONTH LAST DATE','EVERY MONTH 1ST DATE','EVERY MONTH 25TH DATE',
    'EVERY MONTH 26TH DATE','EVERY MONTH 21ST DATE','EXEMPTED','JAHWHAER','EVERY MONTH LAST DATE PART 2'];
$vehicles  = ['TN 31 AC 0123','TN 31 CA 0123','TN 31 BM 5549','TN 31 AK 0394','TN 31 BD 1471','TN 31 BD 7231'];

function rd($from, $to) { return date('Y-m-d', rand(strtotime($from), strtotime($to))); }
function rp() { return '9' . rand(3000000000, 9999999999); }

// ── CLEAR OLD DATA ──
echo "Clearing old data...\n";
$clearOrder = ['advance_dues','other_deductions','other_allowances','uniform_bill_items','uniform_bills',
    'inventory_stock','misc_expenses','fuel_expenses','transactions','invoice_payments','invoice_items',
    'invoices','invoice_sequence','salaries','attendance','positions','trades','employees','clients'];
foreach ($clearOrder as $t) {
    try { $pdo->exec("DELETE FROM `$t`"); $pdo->exec("ALTER TABLE `$t` AUTO_INCREMENT = 1"); } catch(Exception $e) {}
}
echo "  Done.\n";

// ── 1. CLIENTS (100) ──
echo "\n1. Clients (100)...\n";
$clientIds = [];
$st = $pdo->prepare("INSERT INTO clients (client_code,company_name,contact_person,mobile,email,gstin,address,branch,state,invoice_schedule,bill_type,status) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
for ($i = 1; $i <= 100; $i++) {
    $name = $companies[$i-1] ?? 'CLIENT '.$i;
    $contact = $firstNames[array_rand($firstNames)].' '.$lastNames[array_rand($lastNames)];
    $status = $i <= 93 ? 'active' : 'pre_client';
    $st->execute(['CLT-'.str_pad($i,4,'0',STR_PAD_LEFT), $name, $contact, rp(),
        strtolower(str_replace(' ','',$contact)).'@company.com',
        rand(10,35).'ABCDE'.rand(1000,9999).'Z'.rand(1,9),
        rand(1,200).', Main Road, Cuddalore', ['CUDDALORE(HO)','VILLUPURAM','PONDICHERRY'][rand(0,2)],
        'TAMIL NADU', $scheduleTypes[array_rand($scheduleTypes)],
        rand(0,3)===0?'RCM':'GST', $status]);
    $clientIds[] = $pdo->lastInsertId();
}
$total += 100;
echo "  ✓ 100 clients\n";

// ── 2. TRADES (200+) ──
echo "\n2. Trades...\n";
$tradeIds = []; $tc = 0;
$st = $pdo->prepare("INSERT INTO trades (client_id,designation,shift,billing_mode,rate,payable,no_of_positions) VALUES (?,?,?,?,?,?,?)");
foreach ($clientIds as $cid) {
    $n = rand(1,3); $used = [];
    for ($j = 0; $j < $n; $j++) {
        $d = $designations[array_rand($designations)];
        if (in_array($d,$used)) continue; $used[] = $d;
        $rate = rand(50,110);
        $st->execute([$cid,$d,$shifts[array_rand($shifts)],['PRO MONTH','PER DAY','PER HOUR'][rand(0,2)],$rate,round($rate*0.85,2),rand(1,15)]);
        $tradeIds[$cid][] = $pdo->lastInsertId(); $tc++;
    }
}
$total += $tc;
echo "  ✓ $tc trades\n";

// ── 3. EMPLOYEES (100) ──
echo "\n3. Employees (100)...\n";
$employeeIds = [];
$st = $pdo->prepare("INSERT INTO employees (emp_code,name,designation,doj,dob,gender,mobile,address,aadhaar,pan,uan_no,esi_no,bank_name,bank_account,bank_ifsc,basic_wage,epf_applicable,esi_applicable,insurance_amount,status) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
for ($i = 1; $i <= 100; $i++) {
    $fn = $firstNames[array_rand($firstNames)]; $ln = $lastNames[array_rand($lastNames)];
    $gender = in_array($fn,['LAKSHMI','MEENA','NIRMALA','PRIYA','REVATHI','SARANYA','THANGAM','UMA','VANITHA','YAMUNA','CHELLAMMAL','DEVI','ESWARI','FATIMA','GAYATHRI','HEMA','INDRA','JAYA','KAVITHA'])?'Female':'Male';
    $bank = $banks[array_rand($banks)];
    $st->execute([
        'SAI-'.str_pad($i,4,'0',STR_PAD_LEFT), "$fn $ln", $designations[array_rand($designations)],
        rd('2018-01-01','2026-02-28'), rd('1975-01-01','2002-12-31'), $gender, rp(),
        rand(1,200).', Gandhi Street, Cuddalore', str_pad(rand(100000000000,999999999999),12,'0',STR_PAD_LEFT),
        'ABCDE'.rand(1000,9999).'F', '10'.rand(1000000000,9999999999), '31'.rand(1000000000,9999999999),
        $bank, rand(10000000000,99999999999), substr($bank,0,4).'0'.rand(100000,999999),
        rand(10000,22000), 1, 1, rand(0,1)?rand(50,200):0, 'active']);
    $employeeIds[] = $pdo->lastInsertId();
}
$total += 100;
echo "  ✓ 100 employees\n";

// ── 4. POSITIONS (100) ──
echo "\n4. Positions (100)...\n";
$activeClients = array_slice($clientIds, 0, 93);
$st = $pdo->prepare("INSERT INTO positions (employee_id,trade_id,client_id,appointed_date,status,remarks) VALUES (?,?,?,?,?,?)");
foreach ($employeeIds as $eid) {
    $cid = $activeClients[array_rand($activeClients)];
    if (empty($tradeIds[$cid])) continue;
    $tid = $tradeIds[$cid][array_rand($tradeIds[$cid])];
    $st->execute([$eid,$tid,$cid,rd('2020-01-01','2026-02-01'),'active','Deployed']);
}
$total += 100;
echo "  ✓ 100 positions\n";

// ── 5. ATTENDANCE (100 employees × 31 days = ~3100) ──
echo "\n5. Attendance...\n";
$ac = 0;
$st = $pdo->prepare("INSERT IGNORE INTO attendance (employee_id,client_id,trade_id,att_date,status,shift) VALUES (?,?,?,?,?,?)");
$positions = $pdo->query("SELECT p.employee_id,p.client_id,p.trade_id,t.shift FROM positions p JOIN trades t ON t.id=p.trade_id WHERE p.status='active'")->fetchAll();
$statuses = ['P','P','P','P','P','P','A','OFF','P','P','P','P','OT','P','P','P','P','P','P','P'];

$pdo->beginTransaction();
foreach ($positions as $pos) {
    for ($d = 1; $d <= 28; $d++) {
        $dateStr = sprintf('2026-03-%02d', $d);
        if (strtotime($dateStr) > time()) continue;
        try { $st->execute([$pos['employee_id'],$pos['client_id'],$pos['trade_id'],$dateStr,$statuses[array_rand($statuses)],$pos['shift']]); $ac++; } catch(Exception $e) {}
    }
    // Also Feb
    for ($d = 1; $d <= 28; $d++) {
        $dateStr = sprintf('2026-02-%02d', $d);
        try { $st->execute([$pos['employee_id'],$pos['client_id'],$pos['trade_id'],$dateStr,$statuses[array_rand($statuses)],$pos['shift']]); $ac++; } catch(Exception $e) {}
    }
}
$pdo->commit();
$total += $ac;
echo "  ✓ $ac attendance records\n";

// ── 6. INVOICES (100) ──
echo "\n6. Invoices (100)...\n";
$invoiceIds = [];
$stSeq = $pdo->prepare("INSERT INTO invoice_sequence (dummy) VALUES (0)");
$st = $pdo->prepare("INSERT INTO invoices (invoice_no,client_id,bill_type,invoice_month,invoice_date,deployed_hours,subtotal,igst,sgst,cgst,grand_total,round_off,total_outstanding,payment_status,created_by) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
for ($i = 0; $i < 100; $i++) {
    $stSeq->execute(); $seq = $pdo->lastInsertId();
    $invNo = 'SAI-'.str_pad($seq,3,'0',STR_PAD_LEFT);
    $cid = $activeClients[array_rand($activeClients)];
    $mOff = rand(0,2); $invMonth = date('Y-m',strtotime("-$mOff months"));
    $sub = rand(30000,400000);
    $bt = rand(0,3)===0?'RCM':'GST';
    $sgst = $bt==='GST'?round($sub*0.09,2):0; $cgst = $sgst;
    $grand = round($sub+$sgst+$cgst);
    $paidPct = [0,0.5,1][rand(0,2)]; $outstanding = round($grand*(1-$paidPct),2);
    $status = $paidPct>=1?'paid':($paidPct>0?'partial':'unpaid');
    $st->execute([$invNo,$cid,$bt,$invMonth,date('Y-m-d',strtotime("-$mOff months")),12,$sub,0,$sgst,$cgst,$grand,0,$outstanding,$status,1]);
    $invoiceIds[] = $pdo->lastInsertId();
}
$total += 100;
echo "  ✓ 100 invoices\n";

// ── 7. INVOICE ITEMS (200+) ──
echo "\n7. Invoice items...\n";
$iic = 0;
$st = $pdo->prepare("INSERT INTO invoice_items (invoice_id,sl_no,code,sac,designation,nos,duties,ot,off_days,total_hours,rate_per_hour,amount) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
foreach ($invoiceIds as $iid) {
    $n = rand(1,3);
    for ($j = 1; $j <= $n; $j++) {
        $nos = rand(2,14); $duties = rand(20,30)*$nos; $ot = rand(0,3); $off = rand(0,4);
        $hours = ($duties+$ot)*12; $rate = rand(50,110); $amt = round($hours*$rate,2);
        $st->execute([$iid,$j,'106','998525',$designations[array_rand($designations)],$nos,$duties,$ot,$off,$hours,$rate,$amt]);
        $iic++;
    }
}
$total += $iic;
echo "  ✓ $iic invoice items\n";

// ── 8. INVOICE PAYMENTS (100) ──
echo "\n8. Invoice payments (100)...\n";
$ledgerIds = array_column($pdo->query("SELECT id FROM ledger_accounts")->fetchAll(),'id');
$st = $pdo->prepare("INSERT INTO invoice_payments (invoice_id,payment_date,amount,payment_type,payment_method,ref_no,credit_ledger_id,remarks) VALUES (?,?,?,?,?,?,?,?)");
for ($i = 0; $i < 100; $i++) {
    $iid = $invoiceIds[array_rand($invoiceIds)];
    $st->execute([$iid,rd('2025-11-01','2026-03-20'),rand(5000,80000),
        ['received','received','received','tds_deduct'][rand(0,3)],
        ['NEFT','Cheque','Cash','UPI'][rand(0,3)],
        'REF-'.rand(100000,999999),$ledgerIds[array_rand($ledgerIds)],'Payment received']);
}
$total += 100;
echo "  ✓ 100 invoice payments\n";

// ── 9. SALARIES (100) ──
echo "\n9. Salaries (100)...\n";
$sc = 0;
$st = $pdo->prepare("INSERT IGNORE INTO salaries (employee_id,salary_month,days_present,days_ot,days_off,days_absent,total_days,basic_wage,da,hra,attendance_incentive,total_earnings,salary_advance_ded,uniform_due_ded,insurance_premium,epf,esi,total_deductions,net_salary,payment_mode,payment_status,psl_no) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
foreach ($employeeIds as $eid) {
    $p = rand(22,30); $ot = rand(0,2); $off = rand(0,3); $ab = max(0,30-$p-$off);
    $bw = rand(10000,20000); $da = round($bw*0.05,2); $hra = round($bw*0.08,2);
    $attInc = $p>=28?1000:0; $te = $bw+$da+$hra+$attInc;
    $epf = round($bw*0.12,2); $esi = $te<=21000?round($te*0.0075,2):0;
    $td = $epf+$esi; $net = round($te-$td,2);
    try {
        $st->execute([$eid,'2026-03',$p,$ot,$off,$ab,$p+$ot+$off+$ab,$bw,$da,$hra,$attInc,$te,0,0,0,$epf,$esi,$td,$net,
            ['NEFT','Cash'][rand(0,1)],'paid','PSL-'.str_pad($eid,4,'0',STR_PAD_LEFT).'-202603']);
        $sc++;
    } catch(Exception $e) {}
}
$total += $sc;
echo "  ✓ $sc salaries\n";

// ── 10. TRANSACTIONS (100) — NO DUPLICATE ENTRIES (debit ≠ credit) ──
echo "\n10. Transactions (100) — unique debit/credit ledgers per entry...\n";
$st = $pdo->prepare("INSERT INTO transactions (txn_date,description,debit_ledger_id,credit_ledger_id,amount,remarks,created_by) VALUES (?,?,?,?,?,?,?)");
$txnDescs = ['Salary payment','Office rent','Electricity bill','Phone bill','Fuel reimbursement',
    'Travel expense','Vehicle maintenance','Uniform purchase','Insurance premium','EPF deposit',
    'ESI deposit','GST payment','Client payment received','Petty cash replenish','Bank transfer',
    'Cash withdrawal','Advance given','Festival bonus','Incentive payment','Penalty collected'];

for ($i = 0; $i < 100; $i++) {
    // ENSURE debit ledger ≠ credit ledger (NO double entries!)
    $debitIdx  = array_rand($ledgerIds);
    do { $creditIdx = array_rand($ledgerIds); } while ($creditIdx === $debitIdx);

    $st->execute([
        rd('2025-06-01','2026-03-20'),
        $txnDescs[array_rand($txnDescs)],
        $ledgerIds[$debitIdx],
        $ledgerIds[$creditIdx],
        rand(500,200000),
        'Verified entry',
        1
    ]);
}
$total += 100;
echo "  ✓ 100 transactions (debit ≠ credit guaranteed)\n";

// ── 11. ADVANCES (100) ──
echo "\n11. Advances (100)...\n";
$stAdv = $pdo->prepare("INSERT INTO advances (employee_id,advance_type,advance_date,amount,no_of_dues,due_first_month,due_last_month,due_amount,remarks) VALUES (?,?,?,?,?,?,?,?,?)");
$stDue = $pdo->prepare("INSERT INTO advance_dues (advance_id,employee_id,due_month,due_amount) VALUES (?,?,?,?)");
$adc = 0;
for ($i = 0; $i < 100; $i++) {
    $eid = $employeeIds[array_rand($employeeIds)];
    $amt = rand(1000,10000); $dues = rand(1,4); $dueAmt = round($amt/$dues,2);
    $fm = date('Y-m',strtotime('-'.rand(0,3).' months'));
    $ld = new DateTime($fm.'-01'); $ld->modify('+'.($dues-1).' months'); $lm = $ld->format('Y-m');
    $stAdv->execute([$eid,rand(0,1)?'Salary Advance':'Emergency advance',rd('2025-10-01','2026-03-15'),$amt,$dues,$fm,$lm,$dueAmt,'Advance']);
    $aid = $pdo->lastInsertId();
    $dd = new DateTime($fm.'-01');
    for ($j = 0; $j < $dues; $j++) {
        $stDue->execute([$aid,$eid,$dd->format('Y-m'),$dueAmt]); $dd->modify('+1 month'); $adc++;
    }
}
$total += 100 + $adc;
echo "  ✓ 100 advances + $adc dues\n";

// ── 12. FUEL EXPENSES (100) ──
echo "\n12. Fuel expenses (100)...\n";
$st = $pdo->prepare("INSERT INTO fuel_expenses (ref_no,vehicle_no,current_km,biller,bill_date,bill_amount,igst,sgst,cgst,paid_status,paid_from_ledger,transaction_no,remarks) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)");
for ($i = 0; $i < 100; $i++) {
    $amt = rand(500,4000); $sgst = round($amt*0.09,2);
    $st->execute(['FUEL-'.str_pad($i+1,4,'0',STR_PAD_LEFT),$vehicles[array_rand($vehicles)],rand(10000,180000),
        ['HP PETROL BUNK','INDIAN OIL','BHARAT PETROLEUM','SHELL'][rand(0,3)],
        rd('2025-08-01','2026-03-20'),$amt,0,$sgst,$sgst,
        rand(0,1)?'Paid':'Not Paid',$ledgerIds[array_rand($ledgerIds)],'TXN'.rand(100000,999999),'Fuel']);
}
$total += 100;
echo "  ✓ 100 fuel expenses\n";

// ── 13. MISC EXPENSES (100) ──
echo "\n13. Misc expenses (100)...\n";
$st = $pdo->prepare("INSERT INTO misc_expenses (bill_no,ref_no,expense_desc,expense_type,gross_amount,biller,bill_date,bill_amount,igst,sgst,cgst,discount,paid_status,paid_from_ledger,transaction_no,remarks) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
$expItems = ['Office chair','Printer cartridge','Cleaning supplies','First aid kit','Torch','Rain coat','Log book','Pen set','Stationery','Uniform'];
for ($i = 0; $i < 100; $i++) {
    $amt = rand(200,15000); $sgst = round($amt*0.09,2);
    $st->execute(['MISC-'.str_pad($i+1,5,'0',STR_PAD_LEFT),'REF-'.rand(10000,99999),
        $expItems[array_rand($expItems)],'OTHER EXPENSES',$amt+$sgst*2,
        ['Local Vendor','Amazon','Office Mart','Stationery House'][rand(0,3)],
        rd('2025-08-01','2026-03-20'),$amt,0,$sgst,$sgst,0,
        rand(0,1)?'Paid':'Not Paid',$ledgerIds[array_rand($ledgerIds)],'TXN'.rand(100000,999999),'Misc']);
}
$total += 100;
echo "  ✓ 100 misc expenses\n";

// ── 14. OTHER ALLOWANCES (100) ──
echo "\n14. Other allowances (100)...\n";
$st = $pdo->prepare("INSERT INTO other_allowances (employee_id,client_id,allowance_date,amount,reason,remarks) VALUES (?,?,?,?,?,?)");
$reasons = ['Festival bonus','Diwali gift','Pongal bonus','Birthday bonus','Excellence award','Perfect attendance','Night shift allowance','Overtime incentive','Referral bonus','Emergency duty'];
for ($i = 0; $i < 100; $i++) {
    $st->execute([$employeeIds[array_rand($employeeIds)],$activeClients[array_rand($activeClients)],
        rd('2025-08-01','2026-03-20'),rand(200,5000),$reasons[array_rand($reasons)],'Allowance']);
}
$total += 100;
echo "  ✓ 100 allowances\n";

// ── 15. OTHER DEDUCTIONS (100) ──
echo "\n15. Other deductions (100)...\n";
$st = $pdo->prepare("INSERT INTO other_deductions (employee_id,client_id,deduction_date,amount,reason,remarks) VALUES (?,?,?,?,?,?)");
$dedReasons = ['Late arrival','Absent without notice','Damaged equipment','Lost ID card','Misconduct','Dress code violation','Phone usage','Sleeping on duty','Client complaint','Unauthorized leave'];
for ($i = 0; $i < 100; $i++) {
    $st->execute([$employeeIds[array_rand($employeeIds)],$activeClients[array_rand($activeClients)],
        rd('2025-08-01','2026-03-20'),rand(100,2000),$dedReasons[array_rand($dedReasons)],'Deduction']);
}
$total += 100;
echo "  ✓ 100 deductions\n";

// ── 16. UNIFORM BILLS (100) ──
echo "\n16. Uniform bills (100)...\n";
$itemIds = array_column($pdo->query("SELECT id FROM uniform_items")->fetchAll(),'id');
$stUB = $pdo->prepare("INSERT INTO uniform_bills (bill_no,employee_id,client_id,bill_date,subtotal,discount,total_amount,paid_amount,balance_amount,no_of_dues,due_first_month,due_last_month,due_amount,remarks) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
$stUBI = $pdo->prepare("INSERT INTO uniform_bill_items (bill_id,item_id,qty,unit_price,total) VALUES (?,?,?,?,?)");
$ubic = 0;
for ($i = 0; $i < 100; $i++) {
    $fm = date('Y-m',strtotime('-'.rand(0,3).' months'));
    $dues = rand(1,3); $ld = new DateTime($fm.'-01'); $ld->modify('+'.($dues-1).' months'); $lm = $ld->format('Y-m');
    $stUB->execute(['UB-'.str_pad($i+1,4,'0',STR_PAD_LEFT),$employeeIds[array_rand($employeeIds)],
        $activeClients[array_rand($activeClients)],rd('2025-08-01','2026-03-15'),0,0,0,0,0,$dues,$fm,$lm,0,'Issued']);
    $bid = $pdo->lastInsertId(); $sub = 0;
    $n = rand(1,4);
    for ($j = 0; $j < $n; $j++) {
        if (empty($itemIds)) break;
        $qty = rand(1,2); $price = rand(80,700); $t = $qty*$price; $sub += $t;
        $stUBI->execute([$bid,$itemIds[array_rand($itemIds)],$qty,$price,$t]); $ubic++;
    }
    $paid = rand(0,1)?$sub:rand(0,(int)$sub); $bal = $sub-$paid; $da = $dues>0?round($bal/$dues,2):0;
    $pdo->prepare("UPDATE uniform_bills SET subtotal=?,total_amount=?,paid_amount=?,balance_amount=?,due_amount=? WHERE id=?")->execute([$sub,$sub,$paid,$bal,$da,$bid]);
}
$total += 100 + $ubic;
echo "  ✓ 100 uniform bills + $ubic items\n";

echo "\n============================\n";
echo "✅ DONE! $total total records inserted.\n";
echo "============================\n</pre>\n";
