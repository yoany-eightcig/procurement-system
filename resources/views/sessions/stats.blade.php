<form method="get" action="{{ route('search') }}">
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
		      		<th scope="col">Dec.</th>
		    	</tr>
		  	</thead>
		  	<tbody>
		  		@foreach ($parts as $element)	
		  			@if ($element->quantity != 0 && $element->ave != 0 || $element->ave != null)
		  				<tr {{ $element->ave >= $element->quantity  ? 'class=table-danger' : "" }}> 
		  			  		<td class="text-center" scope="col">{{ $element->sku }}</td>
		  			  		<td class="text-center" scope="col">{{ $element->name }}</td>
		  			  		<td class="text-center" scope="col">{{ $element->quantity }}</td>
		  			  		<td class="text-center" scope="col">{{ $element->unissued }}</td>
		  			  		<td class="text-center" scope="col">{{ $element->on_order }}</td>
		  			  		<td class="text-center table-primary" scope="col">{{$element->ave}}</td>
		  			  		<td class="text-center table-primary" scope="col">{{$element->max}}</td>
		  			  		<td class="text-center" scope="col">{{$element->jan}}</td>
		  			  		<td class="text-center" scope="col">{{$element->feb}}</td>
		  			  		<td class="text-center" scope="col">{{$element->mar}}</td>
		  			  		<td class="text-center" scope="col">{{$element->apr}}</td>
		  			  		<td class="text-center" scope="col">{{$element->may}}</td>
		  			  		<td class="text-center" scope="col">{{$element->jun}}</td>
		  			  		<td class="text-center" scope="col">{{$element->jul}}</td>
		  			  		<td class="text-center" scope="col">{{$element->aug}}</td>
		  			  		<td class="text-center" scope="col">{{$element->sept}}</td>
		  			  		<td class="text-center" scope="col">{{$element->oct}}</td>
		  			  		<td class="text-center" scope="col">{{$element->nov}}</td>
		  			  		<td class="text-center" scope="col">{{$element->dec}}</td>
		  				</tr>
		  			@endif
		  		@endforeach
		  	</tbody>
		</table>	
	</div>
</form>
<div class="table-responsive">
	{{ $parts->appends(request()->query())->links() }}	
</div>



