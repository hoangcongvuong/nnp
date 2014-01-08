<!-- BEGIN: main -->
<div class="clear"></div>
<ul class="list-comment">
	<!-- BEGIN: loop -->
    <li class="clearfix{CM.sub_class}" id="comment-{CM.comment_id}" {CM.padding_left}>
    	<div class="comment-author">
        	<img src="http://photo.tinhte.vn/data/avatars/m/227/227325.jpg?1375616813" height="60" width="60" />
            <br />
            <strong>{CM.author_name}</strong><br />
            <a class="reply-comment" id="reply-comment-{CM.comment_id}" href="#">Trả lời</a>
        </div>
        <div class="comment-content">
            {CM.content}
      	</div>
  	</li>
    <!-- END: loop -->
</ul>
<!-- END: main -->