$(document).ready(function() {

   

   $('#data-table-Messages').DataTable({
      paging: false,   
      searching: true,
      columnDefs: [
         { orderable: false, targets: 0 },
         { orderable: false, targets: 7 }
       ],
       order: [[1, 'asc']],
      "bLengthChange": true,
      "bInfo" : false,
      "scrollX": false,
    
   });

   $('#data-table-Messages_v2').DataTable({
      paging: false,   
      searching: true,
      columnDefs: [
         { orderable: false, targets: 0 },
         { orderable: false, targets: 1 },
         { orderable: false, targets: 8 }
       ],
       order: [[2, 'asc']],
      "bLengthChange": true,
      "bInfo" : false,
      
      "scrollX": false,
    
   });

   $('#wr-table-vPage').DataTable({
      paging: true,   
      searching: true,
      "bLengthChange": true,
      "bInfo" : false,
      "iDisplayLength": 25,
      "scrollX": false
    
   });

   $('#wr-tableaudit').DataTable({
      paging: false,   
      searching: true,
      "bLengthChange": true,
      "bInfo" : false,
      "scrollX": false
    
   });

   $('#wr-table-tstatus').DataTable({
      paging: true,   
      searching: true,
      "bLengthChange": true,
      "bInfo" : false,
      "scrollX": false
    
   });

   $('#wr-table-processlist').DataTable({
      paging: true,   
      searching: true,
      "bLengthChange": true,
      "bInfo" : false,
      "scrollX": false
    
   });

   $('#wr-table-variables').DataTable({
      paging: true,   
      searching: true,
      "bLengthChange": true,
      "bInfo" : false,
      "scrollX": false
    
   });

   $('#wr-table-enginelint-').DataTable({
      paging: true,   
      searching: true,
      "bLengthChange": true,
      "iDisplayLength": 25,
      "bInfo" : false,
      "scrollX": false
    
   });
  

   $('#table_whitelist').DataTable({
      paging: false,   
      searching: true,
      "bLengthChange": true,
      "bInfo" : false,
      "iDisplayLength": 10,
      "scrollX": false
   });

   $('#table_blacklist').DataTable({
      paging: false,   
      searching: true,
      "bLengthChange": true,
      "bInfo" : false,
      "iDisplayLength": 10,
      "scrollX": false
   });


   $('#table_active_filters').DataTable({
      paging: true,   
      searching: true,
      "bLengthChange": true,
      "bInfo" : false,
      "iDisplayLength": 10,
      "scrollX": false,
       
   });

   $('#rep_total_mail_by_date').DataTable({
      paging: true,   
      searching: true,
      "bLengthChange": true,
      "bInfo" : false,
      "iDisplayLength": 25,
      "scrollX": false,
       
   });

   $('#table_sa_lint').DataTable({
      paging: true,   
      searching: true,
      "bLengthChange": true,
      "bInfo" : false,
      "iDisplayLength": 25,
      "scrollX": false,
   });


 });

//  rep_total_mail_by_date