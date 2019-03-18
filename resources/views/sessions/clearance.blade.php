<form method="get" action="{{ route($action) }}">
	@csrf
	<div class="input-group mb-3">
	  	<input type="text" class="form-control" name="search" required focused="true">
	  	<div class="input-group-append">
	    	<button type="submit" class="btn btn-success">Search</button>
	    </div>
	</div>
	<div class="table-responsive">
		<table class=" table table-striped  table-fixed">
		  	<thead class="thead-dark">
		    	<tr>
		      		<th scope="col">
		      			<label class="mb-0" for="filter_sku">Sku</label>
		      			<input type="checkbox" name="filter_sku" id="filter_sku" checked>
		      		</th>
		      		<th scope="col">
		      			<label class="mb-0" for="filter_name">Name</label>
		      			<input type="checkbox" name="filter_name" id="filter_name" checked >
		      		</th>
		      		<th scope="col">Current Inventory</th>
		      		<th scope="col">Unissued PO</th>
		      		<th scope="col">On Order</th>
		      		<th scope="col">Ave.</th>
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
		    	</tr>
		  	</thead>
		  	<tbody>
		  		@foreach ($parts as $element)	
		  			@include('sessions.fields', [])
		  		@endforeach
		  	</tbody>
		</table>	
	</div>
</form>
<div class="table-responsive">
	{{ $parts->appends(request()->query())->links() }}	
</div>



