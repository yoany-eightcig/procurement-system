@include('sessions.table_header', [])

@section('scripts')
<script type="text/javascript">
	var oldExportAction = function (self, e, dt, button, config) {
	    if (button[0].className.indexOf('buttons-excel') >= 0) {
	        if ($.fn.dataTable.ext.buttons.excelHtml5.available(dt, config)) {
	            $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt, button, config);
	        }
	        else {
	            $.fn.dataTable.ext.buttons.excelFlash.action.call(self, e, dt, button, config);
	        }
	    } else if (button[0].className.indexOf('buttons-print') >= 0) {
	        $.fn.dataTable.ext.buttons.print.action(e, dt, button, config);
	    }
	};

	var newExportAction = function (e, dt, button, config) {
	    var self = this;
	    var oldStart = dt.settings()[0]._iDisplayStart;

	    dt.one('preXhr', function (e, s, data) {
	        // Just this once, load all data from the server...
	        data.start = 0;
	        data.length = 2147483647;

	        dt.one('preDraw', function (e, settings) {
	            // Call the original action function 
	            oldExportAction(self, e, dt, button, config);

	            dt.one('preXhr', function (e, s, data) {
	                // DataTables thinks the first item displayed is index 0, but we're not drawing that.
	                // Set the property to what it was before exporting.
	                settings._iDisplayStart = oldStart;
	                data.start = oldStart;
	            });

	            // Reload the grid with the original page. Otherwise, API functions like table.cell(this) don't work properly.
	            setTimeout(dt.ajax.reload, 0);

	            // Prevent rendering of the full data to the DOM
	            return false;
	        });
	    });

	    // Requery the server with the new one-time export settings
	    dt.ajax.reload();
	};


	$(document).ready( function () {
		$.noConflict();
		$.fn.dataTable.ext.errMode = 'none';
		let sku = '';

		var table = $('#table_id').DataTable( {
		    processing: true,
		    serverSide: true,
		    fixedHeader: true,
		    select: {
		        style:    'os',
		        selector: 'td:first-child'
		    },
		    pageLength: 100,
		    search: {
		    	regex: true,
		    },
		    lengthMenu: [[25, 50, 100, -1], [25, 50, 100, "All"]],
		    ajax: {
		        "url": "postDatatable/0",
		        "type": "POST",
		        "headers": {
		        	'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		    	},
		    },
		    createdRow: function( row, data, dataIndex ) {
		    	$(row).addClass( parseInt(data[8]) >= parseInt(data[3])  ? 'table-danger' : "" ); 
		    },
		    order: [[ 1, 'asc' ]],
		    columnDefs: [
		    	{
	                orderable: false,
	                className: 'select-checkbox',
	                targets:   0
		        },		    
		    	{
		      		"targets": [8,9],
		      		"createdCell": function (td, cellData, rowData, row, col) {
	          			$(td).addClass("border border-dark");
	          			$(td).css("background", "#c6e0f5");
		      		}
		      	},
  		    	{
  		      		"targets": [10,11,12,13,14,15,16,17,18,19,20,21,22],
  		      		"createdCell": function (td, cellData, rowData, row, col) {
  	          			$(td).addClass("border border-dark");
  	          			$(td).css("background", "#c7eed8");
  		      		}
  		      	},

	      	],
		    dom: '<"justify-content-between row flex-row-reverse"Blf>rtip',
		    buttons: [
		        { 
		        	extend: 'excel',
		        	text: '<i class="fas fa-file-excel pr-2"></i>Export Page',
		        	className: 'btn-info',
		        	exportOptions: {
		        		// columns: [1, 2, 5, 6, 19],
		        		modifier: {
		        			order : 'current',
		        			page : 'all',
		        			search : 'applied'
		        		}
		        	},
		        },
		        { 
		        	extend: 'excel',
		        	text: '<i class="fas fa-file-excel pr-2"></i>Export All',
		        	className: 'btn-success ml-2',
		        	exportOptions: {
		        		// columns: [1, 2, 5, 6, 19],
		        		modifier: {
		        			order : 'current',
		        			page : 'all',
		        			search : 'applied'
		        		}
		        	},
		        	action: newExportAction,
		        },
		    ],
		} );

		function closeAndRefresh()
		{
			table.ajax.reload();
			table.draw();
			$('#updateSuggestQtyModal').modal('hide');
		}

		$('#table_id').on('dblclick', 'td', function (e){
			e.preventDefault();  
			var column_num = parseInt( $(this).index() ) + 1;

			if (column_num == 7) {
				var data = table.row($(this).closest('tr')).data();
				sku = data[1];
				$('#updateSuggestQtyModal').modal('show');
				$("#suggest").val(data[6]);
				$("#suggest").focus();
				console.log(sku);
			}
		});

		$("#updateField").on("click", function(e) {
			$.get({
			    url: "/updatefield",
			   	data: {
			   		sku: sku,
			   		value: $("#suggest").val(),
			   	},
			}).done(function (result) {
				closeAndRefresh();
			}).fail(function (xhr, result, status) {
			    alert("An error occurred when trying to update the field.");
			    closeAndRefresh();
			});

		});
	});	
</script>
@endsection


