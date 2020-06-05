<div class="modal" tabindex="-1" role="dialog" id="updateSuggestQtyModal">
  	<div class="modal-dialog" role="document">
    	<div class="modal-content">
  		<div class="modal-header">
        	<h5 class="modal-title">{{ config('app.name', 'Laravel') }}</h5>
    		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
          		<span aria-hidden="true">&times;</span>
        	</button>
      	</div>
      		<div class="modal-body">
    			<div class="form-group">
    				<label for="suggest">Suggest Quantity:</label>
    				<input type="number" class="form-control" id="suggest" name="suggest" focused>
    			</div>
      		</div>
      		<div class="modal-footer">
        		<button type="button" class="btn btn-primary" id="updateField">Save changes</button>
        		<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      		</div>
    	</div>
  	</div>
</div>

@php
	$m	= date("n");
	$months = ['Jan.', 'Feb.', 'Mar.', 'Apr.', 'May.', 'Jun.', 'Jul.', 'Aug.', 'Sept.', 'Oct.', 'Nov.', 'Dec.'];
	$show_extra_m = true;
@endphp

<div class="table-responsive">
	<table id="table_id" class="display table table-striped table-fixed" >
		<thead class="thead-dark">
	    	<tr>
	    		<th>&nbsp;&nbsp;&nbsp;&nbsp;</th>
	      		<th scope="col">Sku</th>
	      		<th scope="col">Name</th>
	      		<th data-class-name="priority" class="text-center" scope="col">Current <br>Inventory</th>
	      		<th class="text-center" scope="col">Unissued <br>PO</th>
	      		<th scope="col">On Order</th>
	      		<th scope="col">Suggest Qty.</th>
	      		<th scope="col">Case Lot.</th>
	      		<th class="text-center" scope="col">Est.<br> Monthly</th>
	      		<th scope="col">Max.</th>

	      		@for ($i = 0; $i < count($months); $i++)
	      			<th class = "{{ ($m > $i+1) ? 'text-success' : 'text-warning' }}" scope="col">{{ $months[$i] }} {{ ($m > $i+1) ? '20' : '19' }}</th>
	      			@if ($show_extra_m && ($i+1) == $m-1  )
	      				{{ $show_extra_m = false }}
	      				<th class = "text-warning" scope="col">{{ $months[$i] }}<br> 19</th>
	      			@endif
	      		@endfor

	      		<th scope="col">Vendor</th>
	      		<th scope="col">Category</th>
	    	</tr>
	  	</thead>
	</table>	
</div>
