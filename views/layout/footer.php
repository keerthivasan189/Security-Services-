</div><!-- end main-content -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function(){
  // Auto-wrap tables for responsiveness
  $('table.table').each(function(){
    if(!$(this).parent().hasClass('table-responsive')){
      $(this).wrap('<div class="table-responsive"></div>');
    }
  });

  // Only init tables that explicitly have the 'datatable' class
  // Skip tables with dynamic columns (attendance grid, payslip, invoice items)
  $('.datatable').each(function(){
    var $tbl = $(this);
    var thCount  = $tbl.find('thead tr:first th').length;
    var tdCount  = $tbl.find('tbody tr:first td').length;
    // Only init if column counts match (or tbody is empty)
    if(tdCount === 0 || thCount === tdCount){
      $tbl.DataTable({pageLength:25, order:[], retrieve:true});
    } else {
      console.warn('DataTables skipped on table (th=' + thCount + ' td=' + tdCount + '):', this.id || this.className);
    }
  });
});
</script>
<?php if (isset($extraJs)) echo $extraJs; ?>
</body>
</html>
