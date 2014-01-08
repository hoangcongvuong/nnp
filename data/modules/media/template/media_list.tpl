<!-- BEGIN: main -->
<table class="table">
	<thead>
    	<tr>
        	<th></th>
            <th>Name</th>
            <th>Size</th>
            <th>Date</th>
            <th>Link</th>
            <th></th>
      	</tr>
  	</thead>
    <tbody>
    	<!-- BEGIN: loop -->
        <tr>
        	<td><img src="{MEDIA.link}" width="50"/></td>
            <td>{MEDIA.media_name}</td>
            <td>{MEDIA.media_size}</td>
            <td>{MEDIA.uploaded_time}</td>
            <td>{MEDIA.link}</td>
            <td>{MEDIA.feature}</td>
        </tr>
        <!-- END: loop -->
    </tbody>
</table>
<!-- END: main -->