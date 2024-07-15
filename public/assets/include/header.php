<?php
    $FULL_URI = (empty($_SERVER['HTTPS']) ? 'http://' : 'https://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $FILE_NAME = basename($FULL_URI); 

    include_once($func->getAssetsPath().'/assets/view_counter.php');
    if (strpos($func->getUrl(), "localhost") !== false) {
        $viewCounter = new ViewCounter($func->getAssetsPath().'/assets/data/db/');
    } else {
        $viewCounter = new ViewCounter($func->getAssetsPath().'/../assets/data/db/');
    }
    
    $viewCounterData = $viewCounter->getCounterData();
    $viewCountAll = $viewCounterData["all_count"];
    $viewCountToday = $viewCounterData["today_count"];
    $viewCountYesterday = $viewCounterData["yesterday_count"];
?>
<header class="d-flex">
    <div class="menu-btn">
        <span></span>
        <span></span>
        <span></span>
    </div>
    <div class="logo">
        <a href="<?php echo $func->getUrl(); ?>" class="blog-logo-text">もんすたぁのSkript講座</a>
    </div>
    <div class="nav">
        <ul>
            <!-- <li><a href="<?php echo $func->getUrl(); ?>/desk-item/"><span class="title">Desk Item</span><span class="sub-title">デスク周り</span></a></li>
            <li><a href="<?php echo $func->getUrl(); ?>/pc-accessory/"><span class="title">PC Accessory</span><span class="sub-title">PC周辺機器</span></a></li> -->
        </ul>
    </div>
</header>