<?php
/*
    作成者：松尾　匠馬
    最終更新日：2022/1/18
    目的：ユーザIDで変更したプロフィール情報(プロフィール画像、プロフィールメッセージ、ユーザ名)をテーブルに更新する(プロフィール)
    入力：user_id, user_password, user_name, user_mail, user_profile_image,
    　　　user_profile_message, gross_weight

    http通信例：
    http://localhost/OtegoLoss_WebAPI/User/UpdateProfile.php?user_id=u0000002
    &user_password=abcdefghijk
    &user_name=matsuo&user_mail=test@kochi-tech.ac.jp&gross_weight=100
    &user_profile_image=aaaaaaaaABCDEFG
    &user_profile_message=私は高知県出身の農家です。この季節はナスがおすすめです。


*/


#ステータスコードを追記する必要あり
//エラーリポート
error_reporting(E_ALL);
//json形式ファイルのheader
header("Content-Type: application/json; charset=utf-8");

try{
    $db = new PDO('mysql:dbname=software;host=localhost;charset=utf8','root','root');
    echo "接続OK";
    // データベース
    $data = "user";

    if(isset($_POST["user_id"]) && isset($_POST["user_password"]) 
        && isset($_POST["user_name"]) && isset($_POST["user_mail"])
        && isset($_POST["gross_weight"]) && isset($_POST["user_profile_image"]) 
        && isset($_POST["user_profile_message"])) {
        
        // numをエスケープ(xss対策)
        $param_userid = htmlspecialchars($_POST["user_id"]);
        $param_upassword = htmlspecialchars($_POST["user_password"]);
        $param_uname = htmlspecialchars($_POST["user_name"]);
        $param_umail = htmlspecialchars($_POST["user_mail"]);
        $param_gweight = htmlspecialchars($_POST["gross_weight"]);
        $param_uprofileimg = htmlspecialchars($_POST["user_profile_image"]);
        $param_uprofilemes = $_POST["user_profile_message"];

        // user_profile_imageは任意
        if ($param_uprofileimg == '') {
            $param_uprofileimg = NULL;
        }

        // user_profile_messageは任意
        if ($param_uprofilemes == '') {
            $param_uprofilemes = NULL;
        }

        /* 変更する対象が存在するかどうか確認 */
        $sql = "SELECT * FROM user WHERE user_id = :user_id";
        // クエリ(問い合わせ)
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $param_userid, PDO::PARAM_STR);
        $stmt->execute();
        $count = $stmt->rowCount();
        if ($count == 0) {
            // データベースとの接続を切断．
            print_r($stmt->errorinfo());
            unset($db);
            die('user_idが'.$param_userid.'のユーザは見つかりませんでした．');
        }
        echo 'user_idが'.$param_userid.'のユーザが'.$count.'件見つかりました。';

        // SQL文をセット
        $sql = "UPDATE $data 
                SET user_password = :user_password, user_name = :user_name, 
                    user_mail = :user_mail, user_profile_image = :user_profile_image,
                    user_profile_message = :user_profile_message,
                    gross_weight = :gross_weight
                WHERE user_id = :user_id";

        $stmt = $db->prepare($sql);


        // パラメーターをセット
        $stmt->bindValue(':user_id', $param_userid, PDO::PARAM_STR);
        $stmt->bindValue(':user_password', $param_upassword, PDO::PARAM_STR);
        $stmt->bindValue(':user_name', $param_uname, PDO::PARAM_STR);
        $stmt->bindValue(':user_mail', $param_umail, PDO::PARAM_STR);
        $stmt->bindValue(':user_profile_image', $param_uprofileimg, PDO::PARAM_STR);
        $stmt->bindValue(':user_profile_message', $param_uprofilemes, PDO::PARAM_STR);
        $stmt->bindValue(':gross_weight', $param_gweight, PDO::PARAM_INT);

        // dbにexecute
        $result = $stmt->execute();
        if (!$result) {
            // データベースとの接続を切断．
            print_r($stmt->errorinfo());
            unset($db);
            die('プロフィール情報の更新処理に失敗しました。');
        }
        echo 'プロフィール情報の更新処理が完了しました';

       

    } else {
        // paramの値が不適ならerrorと出力してプログラム終了
        echo "error";
    }

    
} catch(PDOException $e) {
    echo "error".$e->getMessage();
}

// データベースとの接続を切断．
unset($db);

?>