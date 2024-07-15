function load_post(post_id) {
    function reqListener() {
        var domDoc = this.responseXML;
        var items = domDoc.getElementsByTagName("item");
        var title_area = document.getElementById("post-title");
        var contentArea = document.getElementById("content-area");
        var is_post = false;
        for (var i = 0; i < items.length; i++) {
            var item = items[i];
            var title = item.getElementsByTagName("title")[0].textContent;
            var link = item.getElementsByTagName("guid")[0].textContent;
            // link から記事IDを取得
            var link_id = link.split("?p=")[1];
            var pubDate = item.getElementsByTagName("pubDate")[0].textContent;
            var date = new Date(pubDate);
            var dateStr = date.getFullYear() + "/" + (date.getMonth() + 1) + "/" + date.getDate();
            var description = item.getElementsByTagName("content:encoded")[0].textContent;
            // descriptionにimgタグがあるかどうかを判定
            var image = "https://monster2408.com/blog/wp-content/themes/cocoon-master/images/no-image-320.png";
            if (description.match(/<img[^>]+src="([^">]+)"/) != null) {
                image = description.match(/<img[^>]+src="([^">]+)"/)[1];
            }
            if (link_id != post_id) {
                continue;
            } else {
                document.title = title + " | もんすたぁのブログ";
                title_area.innerHTML = title;
                contentArea.innerHTML = description;
                is_post = true;
                break;
            }
        }
        if (!is_post) {
            title_area.innerHTML = "<h2>記事が見つかりませんでした</h2>";
        }
    }
    const req = new XMLHttpRequest();
    req.addEventListener("load", reqListener);
    var url = "https://monster2408.com/blog/feed/";
    // 現在URLがlocalhostの場合はローカルのXMLを読み込む
    if (location.href.match(/localhost/)) {
        url = "https://localhost/test.xml";
    }
    req.open("GET", url);
    req.setRequestHeader("Content-Type", "text/xml");
    req.send();
}