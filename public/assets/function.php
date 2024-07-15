<?php


/**
 * MyFunction
 */
class MyFunction {

    public $wiki_version = "v1.0.0";
    
    public $conf_path;
    public $assets_path;
    public $pageUrl;
    public $description;
    public $pdo;
    public $thumbnail;

    public $id;
    public $title;
    public $content;
    public $created_at;
    public $updated_at;
    public $tags;

    public $author_id;

    public $user;
    public $user_id;
    public $user_name;
    public $user_icon;

    public $disLib;

    public $edit_bool;
    public $no_template;

    public $og_type;
    public $og_url;
    public $og_title;
    public $og_description;
    public $og_site_name;
    public $og_image;
    
    private $css_list = array();
    private $footer_content = array();
    
    private $language_code;

        /**
     * __construct
     *
     * @param  mixed $conf_path 設定ファイルのパス
     * @param  mixed $page_id ページID
     * @return void
     */
    public function __construct($conf_path = "./assets/config.php", $page_id=-99) {
        $this->conf_path = $conf_path;
        include($conf_path);
        $this->assets_path = str_replace("/assets/config.php", "", $conf_path);

        $this->pageUrl = $this->getUrl().'/';
        $this->description = $conf["description"];

        $title = $conf["title"];

        // OGP
        $this->og_title = $title;
        $this->og_description = $title;
        $this->og_site_name = $conf["title"];
        $this->og_image = "https://monster2408.com/blog/";
        if ($page_id != -1) {
            $this->og_type = "article";
            $this->og_url = "https://monster2408.com/blog/?p=" . $page_id;
        } else {
            $this->og_type = "website";
            $this->og_url = "https://monster2408.com/blog/";
        }

        $language_code = "en";
        if (isset($_GET["lang"])) {
            $language_code = $_GET["lang"];
        }
        $this->language_code = $language_code;
    }

    public function setNoTemplate($bool) {
        $this->no_template = $bool;
    }

    public function getNoTemplate() {
        return $this->no_template;
    }

    public function setThumbnail($thumbnail) {
        $this->thumbnail = $thumbnail;
    }

    public function getThumbnail() {
        return $this->thumbnail;
    }

    /**
     * getWikiVersion バージョンを取得する
     * @return string
     */
    public function getWikiVersion() {
        return $this->wiki_version;
    }
    
    /**
     * isLocal ローカルかどうかを返す(試験用)
     *
     * @return bool
     */
    public function isLocal() : bool {
        include($this->conf_path);
        return strpos($url, "localhost") !== false;
    }
    
    public function getDiscordUser() {
        return $this->user;
    }

    public function getDiscordLib() {
        return $this->disLib;
    }

    /**
     * 編集できるかどうかをセットする
     */
    public function setEditBool($bool) {
        $this->edit_bool = $bool;
    }

    /**
     * 編集できるかどうかを返す
     */
    public function getEditBool() {
        return $this->edit_bool;
    }

    public function getNoCacheUrl($file_path) {
        return $this->getUrl().$file_path."?time=".filemtime($this->assets_path.$file_path);
    }

    public function printMetaData() {
        include($this->conf_path);
        echo '<!-- CSS -->';
        echo '<link rel="stylesheet" href="'.$this->getNoCacheUrl("/assets/css/style.min.css").'" media="print" type="text/css" onload="this.media=\'all\'">';
        echo '<link rel="stylesheet" href="'.$this->getNoCacheUrl("/assets/css/header.min.css").'" media="print" type="text/css" onload="this.media=\'all\'">';
        echo '<link rel="stylesheet" href="'.$this->getNoCacheUrl("/assets/css/footer.min.css").'" media="print" type="text/css" onload="this.media=\'all\'">';
        echo $html["common_head"];
        $title = $this->getTitle();
        if (strpos($title, $conf["title"]) === false) {
            $title = $conf["title"] . " | " . $title;
        }
        echo '<title>'.$title.'</title>';
		echo '<meta property="og:url" content="'.$this->getPageUrl().'" />';
		echo '<meta property="og:title" content="'.$this->getTitle().'" />';
		echo '<meta property="og:description" content="'.$this->getDescription().'" />';
        echo '<meta property="og:image" content="'.$this->getThumbnail().'" />';
        echo '<meta name="msapplication-TileImage" content="'.$this->getThumbnail().'" />';
    }

    public function console_log( $data ){
        echo '<script>';
        echo 'console.log('. json_encode( $data ) .')';
        echo '</script>';
    }

    public function startsWith($haystack, $needle) {
        return (strpos($haystack, $needle) === 0);
    }

    public function getTitle() {
        return $this->title;
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function setPageUrl($url) {
        if ($this->startsWith($url, $this->getUrl()) === FALSE) {
            $url = $this->getUrl() . $url;
        }
        $this->pageUrl = $url;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function getPageUrl() {
        return $this->pageUrl;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getConf() {
        include($this->conf_path);
        return $conf;
    }

    public function getAssetsPath() {
        return $this->assets_path;
    }

    public function getUrl() {
        include($this->conf_path);
        return $conf["url"];
    }

    public function getSqlHost() {
        include($this->conf_path);
        return $MYSQL["host"];
    }

    public function getSqlDataBase() {
        include($this->conf_path);
        return $MYSQL["db"];
    }

    public function getSqlUser() {
        include($this->conf_path);
        return $MYSQL["user"];
    }

    public function getSqlPassWord() {
        include($this->conf_path);
        return $MYSQL["password"];
    }

    public function getPdo() {
        return $this->pdo;
    }

    public function addStyle($style_path) {
        $this->css_list = array_merge($this->css_list, array($style_path));
    }

    public function createTable() {
        $post_sql = 'create table if not exists posts (
            id bigint(20) auto_increment PRIMARY KEY, 
            title text not null, 
            content longtext, 
            user_id bigint(20) not null,
            created_at datetime not null,
            updated_at datetime not null,
            tags longtext,
            categories longtext
        );';

        $history_sql = 'create table if not exists history (
            id bigint(20) auto_increment PRIMARY KEY, 
            blog_id bigint(20) not null,
            title text not null, 
            content longtext, 
            user_id bigint(20) not null,
            updated_at datetime not null,
            tags longtext,
            categories longtext
        );';

        try {
            // $this->getPdo()->query($post_sql);
            $dsn = 'mysql:dbname='.$this->getSqlDataBase().';host='.$this->getSqlHost().';charset=utf8mb4';
            // $this->pdo = new PDO($dsn,$this->getSqlUser(),$this->getSqlPassWord());
            $pdo = new PDO($dsn,$this->getSqlUser(),$this->getSqlPassWord());
            $pdo->query($post_sql);
            $pdo->query($history_sql);
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    private function getPrivateId() {
        if (isset($_GET["p"])) {
            $id = $_GET["p"];
            if ($id <= 0) {
                $id = -1;
            }
            
            try {
                $sql = "select id from posts where id = $id";
                $stmt = $this->getPdo()->query($sql);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($result == false) {
                    $id = -1;
                } else {
                    $id = $result["id"];
                }
            } catch (PDOException $e) {
                echo $e->getMessage();
                $id = -1;
            }
            return $id;
        } else {
            return -1;
        }
    }

    public function getLanguageCode() {
        return $this->language_code;
    }
}