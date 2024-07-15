<?php

class ViewCounter {

    public $db_dir;
    public $db;

    public function __construct($db_dir = "./assets/data/db/") {
        $this->db_dir = $db_dir;
        // フォルダがなければ作成
        if (!file_exists($db_dir)) {
            mkdir($db_dir, 0777, true);
        }
        // $db_dir."/view_counter.db" にdbファイルを作成
        $this->db = new PDO("sqlite:".$db_dir."/view_counter.db");
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->db->exec("CREATE TABLE IF NOT EXISTS view_counter (id integer, all_count bigint(20), today_count bigint(20), yesterday_count bigint(20));");
        $this->db->exec("CREATE TABLE IF NOT EXISTS view_counter_ip (id integer auto_increment, ip text not null);");
        $this->db->exec("CREATE TABLE IF NOT EXISTS view_counter_date (id integer not null, today_date datetime not null);");
    }

    public function getCounterData() {
        // IPアドレスを取得
        $ip = $_SERVER["REMOTE_ADDR"];
        // 今日の日付を取得
        $today_date = date("Y-m-d");

        // view_counter_dateのid=1のデータがあるか確認
        $sql = "SELECT * FROM view_counter_date WHERE id = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        // echo '<script>';
        // echo 'console.log('. json_encode( $result ) .')';
        // echo '</script>';
        // view_counter_dateのid=1のデータがなければ、データを作成する。
        if (count($result) == 0) {
            $sql = "INSERT INTO view_counter_date (id, today_date) VALUES (1, :today_date)";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":today_date", $today_date, PDO::PARAM_STR);
            $stmt->execute();
        } else {
            // view_counter_dateのid=1のデータがあれば、今日の日付と比較して違っていたら view_counter_ipのデータを削除する。
            foreach ($result as $row) {
                $date = $row["today_date"];
            }
            // $dateが空の場合は2000-01-01にする。
            if (isset($date) == false) $date = "2000-01-01";
            if ($date != $today_date) {
                $sql = "DELETE FROM view_counter_ip;";
                $stmt = $this->db->prepare($sql);
                $stmt->execute();
                $sql = "UPDATE view_counter_date SET today_date = :today_date WHERE id = 1;";
                $stmt = $this->db->prepare($sql);
                $stmt->bindValue(":today_date", $today_date, PDO::PARAM_STR);
                $stmt->execute();


                // テーブルの view_counter のtoday_countをyesterday_countに移動する。
                $sql = "SELECT * FROM view_counter WHERE id = 1";
                $stmt = $this->db->prepare($sql);
                $stmt->execute();
                $result = $stmt->fetchAll();
                foreach ($result as $row) {
                    $today_count = $row["today_count"];
                }
                if (isset($today_count) == false) $today_count = 0;

                // $dateを日付に変換する
                $yesterday_date = date("Y-m-d", strtotime("-1 day"));

                if ($yesterday_date == $date) {
                    $yesterday_count = $today_count;
                    $today_count = 0;
                } else {
                    $yesterday_count = 0;
                    $today_count = 0;
                }
                $sql = "UPDATE view_counter SET yesterday_count = :yesterday_count, today_count = :today_count WHERE id = 1;";
                $stmt = $this->db->prepare($sql);
                $stmt->bindValue(":today_count", $today_count, PDO::PARAM_INT);
                $stmt->bindValue(":yesterday_count", $yesterday_count, PDO::PARAM_INT);
                $stmt->execute();
            }
        }
        // view_counterのid=1のデータがあるか確認
        $sql = "SELECT * FROM view_counter WHERE id = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        // echo '<script>';
        // echo 'console.log('. json_encode( $result ) .')';
        // echo '</script>';
        // view_counterのid=1のデータがなければ、データを作成する。
        if (count($result) == 0) {
            $sql = "INSERT INTO view_counter (id, all_count, today_count, yesterday_count) VALUES (1, 0, 0, 0)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
        }

        // view_counter_ipの中に同じIPアドレスがあるか確認
        $sql = "SELECT * FROM view_counter_ip WHERE ip = :ip";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":ip", $ip, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetchAll();
        // echo '<script>';
        // echo 'console.log('. json_encode( $result ) .')';
        // echo '</script>';
        // 同じIPアドレスがなければ、テーブルの view_counter のデータを更新する。
        if (count($result) == 0) {
            $sql = "SELECT * FROM view_counter WHERE id = 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll();
            foreach ($result as $row) {
                $all_count = $row["all_count"];
                $today_count = $row["today_count"];
            }
            if (isset($all_count) == false) $all_count = 0;
            if (isset($today_count) == false) $today_count = 0;
            $all_count++;
            $today_count++;
            $sql = "UPDATE view_counter SET all_count = :all_count, today_count = :today_count WHERE id = 1;";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":all_count", $all_count, PDO::PARAM_INT);
            $stmt->bindValue(":today_count", $today_count, PDO::PARAM_INT);
            $stmt->execute();
            $sql = "INSERT INTO view_counter_ip (ip) VALUES (:ip)";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":ip", $ip, PDO::PARAM_STR);
            $stmt->execute();
        }
        // テーブルの view_counter のデータを取得する。
        $sql = "SELECT * FROM view_counter WHERE id = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        foreach ($result as $row) {
            $final_all_count = $row["all_count"];
            $final_today_count = $row["today_count"];
            $final_yesterday_count = $row["yesterday_count"];
        }
        // echo '<script>';
        // echo 'console.log(view_counter)';
        // echo '</script>';
        // echo '<script>';
        // echo 'console.log('. json_encode( $result ) .')';
        // echo '</script>';
        if (isset($final_today_count) == false) $final_today_count = 0;
        if (isset($final_yesterday_count) == false) $final_yesterday_count = 0;
        if (isset($final_all_count) == false) $final_all_count = 0;
        return array("all_count" => $final_all_count, "today_count" => $final_today_count, "yesterday_count" => $final_yesterday_count);
    }

}