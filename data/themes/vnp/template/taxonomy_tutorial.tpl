<!-- BEGIN: main -->
<div class="block-portal">
	<div class="block-content">
        <h1>{TAXONOMY.taxonomy_name}</h1>
        <section class="list-news">
            <!-- BEGIN: loop -->
            <article id="post-{TUTS.news_id}">
                <div class="ct-latest-thumb">
                    <a href="{TUTS.link}">
                        <img width="105" height="105" src="{TUTS.image}" class="vnp-image" alt="{TUTS.title}">
                    </a>
                </div>
                <header>
                    <h2><a href="{TUTS.link}">{TUTS.title}</a></h2>
                    <p class="ct-subline">
                        In <a href="#" class="info">Playground</a>
                        by <a href="#" class="info">{TUTS.author}</a>
                        <time pubdate="pubdate">Oct 15, 2013</time>
                        <a href="#" title="Comment on Animated Checkboxes and Radio Buttons with SVG" class="info">35 Comments</a>
                    </p>
                </header>
                <p class="ct-feat-excerpt">
                    {TUTS.content}
                    <br />
                    <span><a href="{TUTS.link}" class="fr info">read more</a></span>
                </p>
                <div class="clear"></div>
            </article>
            <!-- END: loop -->
            <center>{PAGING}</center>
        </section>
   	</div>
</div>
<!-- END: main -->