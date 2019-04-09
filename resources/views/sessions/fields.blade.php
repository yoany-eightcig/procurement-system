@if ($element->quantity != 0 && $element->ave != 0 || $element->ave != null)
	<tr> 
  		<td class="text-center" scope="col">{{ $element->sku }}</td>
  		<td class="text-center" scope="col">{{ $element->name }}</td>
  		<td class="text-center" scope="col">{{ $element->quantity }}</td>
  		<td class="text-center" scope="col">{{ $element->unissued }}</td>
  		<td class="text-center" scope="col">{{ $element->on_order }}</td>
  		<td class="text-center table-primary border border-dark" scope="col">{{$element->ave}}</td>
  		<td class="text-center table-primary border border-dark" scope="col">{{$element->max}}</td>
      <td class="text-center table-success border border-dark" scope="col">{{$element->dec}}</td>
  		<td class="text-center table-success border border-dark" scope="col">{{$element->jan}}</td>
  		<td class="text-center table-success border border-dark" scope="col">{{$element->feb}}</td>
  		<td class="text-center table-success border border-dark" scope="col">{{$element->mar}}</td>
  		<td class="text-center " scope="col">{{$element->apr}}</td>
  		<td class="text-center" scope="col">{{$element->may}}</td>
  		<td class="text-center" scope="col">{{$element->jun}}</td>
  		<td class="text-center" scope="col">{{$element->jul}}</td>
  		<td class="text-center" scope="col">{{$element->aug}}</td>
  		<td class="text-center" scope="col">{{$element->sept}}</td>
  		<td class="text-center" scope="col">{{$element->oct}}</td>
  		<td class="text-center" scope="col">{{$element->nov}}</td>
	</tr>
@endif
