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
	      		<th scope="col">Dec.</th>
	      		<th scope="col">Jan.</th>
	      		<th scope="col">Feb.</th>
	      		<th scope="col">Mar.</th>
	      		<th scope="col">Apr.</th>
	      		<th scope="col">May.</th>
	      		<th scope="col">Jun.</th>
	      		<th scope="col">Jul.</th>
	      		<th scope="col">Aug.</th>
	      		<th scope="col">Sept.</th>
	      		<th scope="col">Oct.</th>
	      		<th scope="col">Nov.</th>
	      		<th scope="col">Vendor</th>
	    	</tr>
	  	</thead>
	</table>	
</div>
