<!-- BEGIN: slider -->
<div class="slider">
    <div class="top_slider">
        <button id="slider_prev" type="button">prev</button>
        <button id="slider_next" type="button">next</button>
        <div class="items">
        	<!-- BEGIN: loop -->
            <div class="item">
                <div>
                    <div class="thumb"> <a href="{TT.link}" title="{TT.title}" rel="bookmark"> <img class="ntImage-img" src="{TT.image}"  alt="" title="" /> </a> <a class="readmore" href="{TT.link}" title=""> Read more <span> &raquo; </span> </a> </div>
                    <!--//thumb-->
                    <div class="item_content">
                        <h3> <a href="{TT.link}" title="{TT.title}" rel="bookmark">{TT.title}</a> </h3>
                        <ul class="slider_meta">
                            <li> <a class="author_link" href="#" title=""> admin </a> </li>
                            <li> <a class="date"> June 22, 2013 </a> </li>
                        </ul>
                        <p>{TT.description}</p>
                    </div>
                </div>
            </div>
            <!-- END: loop -->
        </div>
    </div>
    
    <ul class="small_thumbs">
    	<!-- BEGIN: thumb -->
        <li> <a href="{TT.link}"> <img src="{TT.thumb}"  alt="{TT.title}" title="{TT.title}" /> </a> </li> 
        <!-- END: thumb -->
    </ul>
</div>
<!-- END: slider -->





<!-- BEGIN: main -->
<div class="block-portal">
	<div class="block-content">
        <h3 class="block-title">Bài viết mới</h3>
        <section class="list-news">
            <!-- BEGIN: loop -->
            <article id="post-{NEWS.tutorial_id}">
                <div class="ct-latest-thumb">
                    <a href="{NEWS.link}">
                        <img width="105" height="105" src="{NEWS.image}" class="vnp-image" alt="Animated Checkboxes">
                    </a>
                </div>
                <header>
                    <h2><a href="{NEWS.link}">{NEWS.title}</a></h2>
                    <p class="ct-subline">
                        In <a href="#" class="info">Playground</a>
                        by <a href="#" class="info">{NEWS.author}</a>
                        <time pubdate="pubdate">{NEWS.add_time}</time>
                        <a href="#" title="Comment on Animated Checkboxes and Radio Buttons with SVG" class="info">35 Comments</a>
                    </p>
                </header>
                <p class="ct-feat-excerpt">
                    {NEWS.description}
                    <br />
                    <span><a href="{NEWS.link}" class="fr info">read more</a></span>
                </p>
                <div class="clear"></div>
            </article>
            <!-- END: loop -->
            <center>{PAGING}</center>
        </section>
   	</div>
</div>
<!-- END: main -->