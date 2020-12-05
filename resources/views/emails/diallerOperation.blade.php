{{-- Total Count : {{$campData}} --}}

<table border="1" cellpadding="1" cellspacing="1" width="100%">
	<thead>
		<tr>
			<th>S. No.</th>
			<th>Campaign Name</th>
			<th>Leads Loaded</th>
			<th>Records Per Head</th>
			<th>FTE Required</th>
		</tr>
	</thead>

	<tbody>
	
	@foreach($campData['result'] as $_campData)
		<tr>
			<td>{{ $_campData->campaign_id }}</td>
			<td>{{ $_campData->campaign_name }}</td>
			<td>{{ $_campData->leads_count }}</td>		
			<td>
				{{ $campData['recordsPerHead'][$_campData->campaign_id] }}
			</td>
			
			<td>
				{{ number_format($_campData->leads_count / $campData['recordsPerHead'][$_campData->campaign_id],2) }} 

			</td>
		</tr>
	@endforeach		
	</tbody>
</table>