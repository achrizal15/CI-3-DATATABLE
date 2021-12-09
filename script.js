let dataTableHandler = function () {
   let table = $(".datatable-ajax")
   let thead = [];
   let tbody = [];

   if (table.length > 0) {
       table.find("thead tr th").each(function (e) {
           let text = $(this).text()
           thead.push({ "data": text })
           tbody.push({
               "width": $(this).data("tdwidth") ? $(this).data("tdwidth") : "10px",
               "targets": e, // your case first column
               "className": $(this).data("tdclass") ? $(this).data("tdclass") : null,
           })
       })
       $(".ig-loader").removeClass('hidden');
       table.DataTable({
           "ordering": false,
           "autoWidth":false,
           "lengthChange": false,
           "searching": false,
           "processing": true,
           "serverSide": true,
           "serverMethod": "post",
           "language": {
               "processing": '',
               "emptyTable": "NO DATA AVAILABLE IN TABLE",
               "info": 'TOTAL : <span style="color:#d94527; font-weight:bold;"> _TOTAL_ </span> I<span class="text-lowercase">tem(s)</span>',
               "paginate": {
                   "previous": "Prev"
               }
           },
           "ajax": {
               "url": document.base_url + "das/" + table.data("tablename") + "/table",
               "data": filters()
           },
           "columns": thead,
           'columnDefs': tbody
       }).on('init', function (e, settings) {
           $(".ig-loader").addClass('hidden');
       }).on('preDraw', function () {
           $(".ig-loader").removeClass('hidden');
       })
           .on('draw.dt', function () {
               $(".ig-loader").addClass('hidden');
           });
   }


}