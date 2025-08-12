<?php
include 'includes/header.php';

// get news_id from url
include 'config/db.php';

if (isset($_GET['news_id'])) {
    $news_id = $_GET['news_id'];
    $sql = "SELECT * FROM news WHERE news_id = $news_id";
    $result = mysqli_query($conn, $sql);
    $news = mysqli_fetch_assoc($result);
}
?>
            <div class="hotale-page-wrapper" id="hotale-page-wrapper">
                <div class="hotale-blog-title-wrap hotale-style-custom hotale-feature-image">
                    <div class="hotale-header-transparent-substitute"></div>
                    <div class="hotale-blog-title-top-overlay"></div>
                    <div class="hotale-blog-title-overlay" style="opacity: 1;"></div>
                    <div class="hotale-blog-title-bottom-overlay"></div>
                    <div class="hotale-blog-title-container hotale-container">
                        <div class="hotale-blog-title-content hotale-item-pdlr">
                            <header class="hotale-single-article-head hotale-single-blog-title-style-4 clearfix">
                                <div class="hotale-single-article-head-right">
                                    <h1 class="hotale-single-article-title"><?php echo $news['title']; ?></h1>
                                    <div class="hotale-blog-info-wrapper">
                                        <div class="hotale-blog-info hotale-blog-info-font hotale-blog-info-date post-date updated">
                                            <span class="hotale-blog-info-sep">•</span><span class="hotale-head"><i class="gdlr-icon-clock"></i></span><a href="single-blog.html"><?php echo $news['created_at']; ?></a>
                                        </div>
                                    </div>
                                </div>
                            </header>
                        </div>
                    </div>
                </div>
                <div class="hotale-content-container hotale-container gdlr-core-sticky-sidebar gdlr-core-js">
                    <div class="hotale-sidebar-wrap clearfix hotale-line-height-0 hotale-sidebar-style-right">
                        <div class="hotale-sidebar-center hotale-column-40 hotale-line-height">
                            <div class="hotale-content-wrap hotale-item-pdlr clearfix">
                                <div class="hotale-content-area">
                                    <article id="post-15230" class="post-15230 post type-post status-publish format-standard has-post-thumbnail hentry category-blog category-planning category-tips tag-nature tag-tips">
                                        <div class="hotale-single-article clearfix">
                                            <div class="hotale-single-article-content">
                     
                                                <div class="wp-block-image">
                                                    <figure class="alignleft size-large is-resized">
                                                        <img
                                                            src="<?php echo htmlspecialchars($news['image_url']); ?>"
                                                            alt="<?php echo htmlspecialchars($news['title']); ?>"
                                                            class="wp-image-15162"
                                                            width="425"
                                                            height="284"
                                                            sizes="(max-width: 425px) 100vw, 425px"
                                                        />
                                                    </figure>
                                                </div>

                                                <p>
                                                    <?php echo htmlspecialchars($news['content']); ?>
                                                </p>

                                        </div>
                                        <!-- hotale-single-article -->
                                    </article>
                                    <!-- post-id -->
                                </div>
                            
                                
                                
                                <!-- hotale-comments-area -->
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>



<?php include 'includes/footer.php'; ?>