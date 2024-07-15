<?php

include('../assets/function.php');
$func = new MyFunction('../assets/config.php');

$func->addStyle('/assets/css/style.min.css');
// $func->setTitle("PC周辺機器");

function is_page() {
    return empty($_GET['p']) === false;
}

$top_image = $func->getUrl() . '/assets/img/monster2408-blog-top.jpg';
if (is_page()) {
    $top_image = $func->getUrl() . '/assets/img/monster2408-blog-top.jpg';
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html lang="<?php echo $func->getLanguageCode(); ?>">
    <head>
        <?php $func->printMetaData(); ?>
    </head>
    <body>
        <?php include($func->getAssetsPath().'/assets/include/header.php'); ?>
        <div class="contents">
            <div class="body p-1">
                <div class="top-inner">
                    <img src="<?php echo $top_image; ?>" alt="">
                </div>
                <div class="inner">
                    <div class="recommend-title new-post-list"><h2>新着記事</h2></div>
                    <div class="recommend-area" id="new-post-area"><h2><i class="fa-solid fa-spinner fa-spin"></i>読み込み中<i class="fa-solid fa-spinner fa-spin"></i></h2></div>
                </div>
            </div>
        </div>
        <?php include($func->getAssetsPath().'/assets/include/footer.php'); ?>
        <?php
            echo '<script src="'.$func->getNoCacheUrl("/assets/js/feed-load.js").'"></script>';
            echo '<script>load_feed(-1);</script>';
        ?>
    </body>
</html>