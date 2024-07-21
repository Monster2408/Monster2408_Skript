function load_feed(new_post_limit = 10) {
    function reqListener() {
        function getCardHtml(title, image, dateStr, link, category = "") {
            if (category == "") {
                category = "未分類";
            }
            var html_text = '<div class="l-wrapper">';
            html_text += '<article class="card">';
            html_text += '<div class="card__header">';
            html_text += '<h3 class="card__title">' + title + '</h3>';
            html_text += '<figure class="card__thumbnail">';
            html_text += '<img src="' + image + '" class="card__image">';
            html_text += '</figure>';
            html_text += '</div>';
            html_text += '<div class="card__body">';
            html_text += '<p class="card__text">投稿日：' + dateStr + '</p>';
            html_text += '</div>';
            html_text += '<div class="card__footer">';
            html_text += '<p class="card__text"><a href="' + link + '" class="button -compact">記事を読む</a></p>';
            html_text += '</div>';
            html_text += '</article>';
            html_text += '</div>';
            return html_text;
        }
        function getPostCardHtml(title, image, dateStr, link, category = "") {
            if (category == "") {
                category = "未分類";
            }
            var html_text = '<li class="post-list__item">';
            html_text += '<a href="' + link + '" class="post-list__link">';
            html_text += '<div class="post-list__thumbnail">';
            html_text += '<img src="' + image + '" class="post-list__image">';
            html_text += '</div>';
            html_text += '<div class="post-list__content">';
            html_text += '<h3 class="post-list__title">' + title + '</h3>';
            html_text += '<p class="post-list__info">';
            html_text += '<span class="post-list__category">' + category + '</span>';
            html_text += '<span class="post-list__date">' + dateStr + '</span>';
            html_text += '</p>';
            html_text += '</div>';

            html_text += '</a>';
            html_text += '</li>';
            return html_text;
        }

        function getCardObject(title, image, dateStr, link, category = "") {
            if (category == "") {
                category = "未分類";
            }
            var card = {
                title: title,
                image: image,
                dateStr: dateStr,
                link: link,
                category: category
            };
            return card;
        }
        var domDoc = this.responseXML;
        console.log(domDoc);
        var items = domDoc.getElementsByTagName("item");
        const recommendArea = document.getElementById("recommend-area");
        const newPostArea = document.getElementById("new-post-area");
        var data_text = ""; 
        var post_list = [];
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
            var categories_temp = item.getElementsByTagName("category");
            var categories = [];
            // categoryは<![CDATA[カテゴリ名]]>という形式で格納されている
            var main_category = "未分類";
            for (var j = 0; j < categories_temp.length; j++) {
                var category = categories_temp[j].textContent;
                category = category.replace(/<!\[CDATA\[/, "");
                category = category.replace(/\]\]>/, "");
                if (category.startsWith("tag:")) {
                    continue;
                }
                if (category != "おすすめ") {
                    main_category = category;
                }
                categories.push(category);
            }
            // descriptionにimgタグがあるかどうかを判定
            var image = "https://monster2408.com/blog2/wp-content/themes/cocoon-master/images/no-image-320.png";
            if (description.match(/<img[^>]+src="([^">]+)"/) != null) {
                image = description.match(/<img[^>]+src="([^">]+)"/)[1];
            }
            if (categories.indexOf("非表示") != -1) {
                continue;
            }
            if (categories.indexOf("skript講座") == -1) {
                continue;
            }
            if (location.href.match(/localhost/)) {
                post_list.push(getCardObject(title, image, dateStr, "https://localhost/?p=" + link_id, main_category));
            } else {
                post_list.push(getCardObject(title, image, dateStr, "https://monster2408.com/skript-dev/?p=" + link_id, main_category));
            }
            if (categories.indexOf("おすすめ") == -1) {
                continue;
            }
            if (location.href.match(/localhost/)) {
                data_text += getCardHtml(title, image, dateStr, "https://localhost/?p=" + link_id, main_category);
            } else {
                data_text += getCardHtml(title, image, dateStr, "https://monster2408.com/skript-dev/?p=" + link_id, main_category);
            }
        }
        if (recommendArea != null) {
            recommendArea.innerHTML = data_text;
        }
        post_list.sort((a, b) => a.dateStr < b.dateStr ? 1 : -1);
        post_text = '<ul class="post-list">';
        var post_size = 0;
        for (var i = 0; i < post_list.length; i++) {
            var post = post_list[i];
            console.log(post);
            post_text += getPostCardHtml(post["title"], post["image"], post["dateStr"], post["link"], post["category"]);
            post_size++;
            if (new_post_limit != -1 && post_size >= new_post_limit) {
                break;
            }
        }
        post_text += '</ul>';
        if (newPostArea != null) {
            newPostArea.innerHTML = post_text;
        }
    }

    const req = new XMLHttpRequest();
    req.addEventListener("load", reqListener);
    var url = "https://monster2408.com/blog2/feed/";
    // 現在URLがlocalhostの場合はローカルのXMLを読み込む
    if (location.href.match(/localhost/)) {
        url = "https://localhost/test.xml";
    }
    req.open("GET", url);
    req.setRequestHeader("Content-Type", "text/xml");
    req.send();
}