<!-- BEGIN: main -->
<h2>Bình luận</h2>
<div class="hr-single"></div>
<div class="cmt-form fr">
    <form name="add_comment" action="{ACTION}" method="POST" id="add_comment" class="add_comment">
    	<input type="hidden" name="reply_form" id="reply_form" value="{REPLY_FORM}" />
    	<input type="hidden" name="ct_type_id" id="ct_type_id" value="{CM.ct_type_id}" />
        <input type="hidden" name="content_id" id="content_id" value="{CM.content_id}" />
        <input type="hidden" name="parent_id" id="parent_id" value="{CM.parent_id}" />
    	<div class="cmt-user-info fl">
            <img src="http://photo.tinhte.vn/data/avatars/m/227/227325.jpg?1375616813" height="60" width="60" />
        </div>
        <div id="comment-info">
            <textarea name="content" id="comment-content" style="width: 564px; height: 200px;"></textarea>
            
            <div class="author-info">
                <label for="author-name" class="fl">Tên bạn</label>
                <input type="text" class="fl" name="author_name" id="author-name" value="">
                <label for="author-name" class="fl">Email</label>
                <input type="text" class="fl" name="author_email" id="author-email" value="">
                <input class="submit-comment btn btn-primary" type="submit" name="add_comment_submit" id="comment-submit" value="Gửi bình luận">
            </div>
        </div>
        <div class="clear"></div>
        
    </form>
</div>
<!-- END: main -->