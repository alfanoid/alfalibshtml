function alfaDatatableSortNumber(a, b) {
  return a - b;
}
              
function alfaDatatable_Display_Data() {

  myTable = new DataTable('#alfaDatatableDataTableID',{
  
    data: alfaDatatableData,
  
    // Display Columns
    columns: alfaDatatableColumns,
  
    order: [[ 0, 'desc']],
  //  order: [[ 1, 'desc'],[2, 'asc']],
  //  pageLength: 25,
    autoWidth: false,
  //  fixedColumns: true,
    paging: false,
    scrollCollapse: true,
  //    scrollX: true,
  //    scrollY: 300,
  //    fixedHeader: true,
  
    layout: {
      topStart: [
        'search',
        'pageLength',
        'buttons',
        'info',
        'paging',
      ],
      topEnd: null,
      bottomStart: null,
      bottomEnd: null,
    },
  
    columnDefs: [{
      targets: '_all', // All columns
      orderable: false, // Turns off Heading onclick Sort 

      // Hi-light a cell
      createdCell: function(td, cellData, rowData, row, col) {
        // This function is executed per column, per row.
        
        ColumnName =  Object.keys(rowData)[col]; // Get column name.

        if (ColumnName == 'VALID_TRANSACTIONS_COUNT' && rowData[ColumnName] > 0) {
          $(td).css('background-color', 'lightgreen');
        };
        if (ColumnName == 'INVALID_TRANSACTIONS_COUNT' && rowData[ColumnName] > 0) {
          $(td).css('background-color', 'pink');
        };
      },

    }],
  
  //  buttons: [
  //        'copy', 'excel', 'pdf'
  //  ],
  
    buttons: [
      {
        text: 'Reset/Reload',
        action: function (e, dt, node, config) {
  //        $('#IDDataTable').DataTable().columns().search('').draw();
          location.reload();
        }
      }
    ],
  

    // Highlight a row
    rowCallback: function(row, data, index) {
//      if (data.VALID_TRANSACTIONS_COUNT > 0) {
//        $(row).css('background-color', 'khaki');
//        $(td).css('color', 'khaki');
//      } else if (data.INVALID_TRANSACTIONS_COUNT > 0) {
//        $(row).css('background-color', 'pink');
//      }
    },
  
    initComplete: function () {
      
      $("#alfaDatatable").hide();

      this.api().columns().every(function (index) {
  
        var column = this;

        ColumnName = this.header().textContent;

//        console.log(`ColumnName: ${ColumnName}`);

        // Build dropdown filter
        if ( alfaDatatableColumnsFilter[ColumnName] ) {
          var select = $('<br><select><option value=".*">All</option></select>')
            .appendTo($(column.header()))
            .on('change', function () {
              //var val = $.fn.dataTable.util.escapeRegex( $(this).val() );
              var val = $(this).val();
  
              if ( val ) {
              column
                .search(val ? '^' + val + '$' : '', true, false)
                .draw();
              } else {
              column
                .search("![^]", true, true)
                .draw();
              }
            });
  
          DCF = alfaDatatableColumnsFilter[ColumnName];

          if ( DCF.sort ) { // Custom Sort

            Sort = DCF.sort.split(',');

            switch( Sort[0] ) {
              case "number":
                SortCols = column.render('display').unique().sort(
                  function (a, b) {
                    if ( Sort[1] == "desc" ) {
                      return alfaDatatableSortNumber(b, a)
                    } else {
                      return alfaDatatableSortNumber(a, b)
                    }
                  }
                );
                break;

              case "string":
                if ( Sort[1] == "desc" ) {
                  SortCols = column.render('display').unique().sort().reverse();
                } else {
                  SortCols = column.render('display').unique().sort();
                }
                break;

            } // End switch

          } else {
            SortCols = column.render('display').unique().sort();
          }

          SortCols.each(function (d, j) {
            select.append(`<option value="${d}">${d}</option>
            `);
          }) ;

        } // End Build dropdown filter
      }); // End every Column
    } // initComplete end
  
  });
}
