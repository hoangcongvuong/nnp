<!-- BEGIN: main -->
<h1>{TAXONOMY.taxonomy_name}</h1>
<section class="list-news">
	<!-- BEGIN: loop -->
	<article id="post-{NEWS.news_id}">
        <div class="ct-latest-thumb">
            <a href="{NEWS.link}">
                <img width="105" height="105" src="{NEWS.image}" class="vnp-image" alt="Animated Checkboxes">
          	</a>
        </div>
        <header>
            <h2><a href="{NEWS.link}">{NEWS.title}</a></h2>
            <p class="ct-subline">
            	In <a href="#" class="info">Playground</a>
                by <a href="#" class="info">Mary Lou</a>
                <time pubdate="pubdate">Oct 15, 2013</time>
                <a href="#" title="Comment on Animated Checkboxes and Radio Buttons with SVG" class="info">35 Comments</a>
           	</p>
        </header>
        <p class="ct-feat-excerpt">
        	{NEWS.content}
            <br />
        	<span><a href="{NEWS.link}" class="fr info">read more</a></span>
        </p>
        <div class="clear"></div>
    </article>
    <!-- END: loop -->
    <center>{PAGING}</center>
</section>
<!-- END: main -->