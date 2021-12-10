<?php 
    session_start();
    $mode = "input";
    $errmessage = array();
    // $errmessage[] = "テスト文字列";
    if( isset($_POST["back"]) && $_POST["back"]){
        // 何もしない
    }else if(isset($_POST["confirm"]) && $_POST["confirm"]){
        // 名前について
        if(!$_POST['yourname']){
            $errmessage[]="名前を入力してください";
        }else if(mb_strlen($_POST['yourname']) >100){
            $errmessage[] = "名前は100文字以内にしてください";
        }
        $_SESSION['yourname'] = htmlspecialchars( $_POST["yourname"],ENT_QUOTES);


        // Eめーるについて
        if(!$_POST["email"]){
            $errmessage[] = "メールアドレスを入力してください";
        }else if(mb_strlen($_POST["email"]) >200){
            $errmessage[] = "メールアドレスは200字以内にしてください。";
        }else if(!filter_var($_POST["email"],FILTER_VALIDATE_EMAIL)){
            $errmessage[] = "メールアドレスが不正です";
        }
        $_SESSION['email'] = htmlspecialchars($_POST["email"],ENT_QUOTES);


        // 本文欄の検証
        if(!$_POST['message']){
            $errmessage[] = "お問い合わせ内容を入力してください";
        }else if(mb_strlen($_POST["message"]) > 500){
            $errmessage[] = "お問い合わせ内容は500字以内にしてください";
        }
        $_SESSION['message'] = htmlspecialchars( $_POST['message'] ,ENT_QUOTES);

        if($errmessage){
            $mode = 'input';
        } else {
            $mode = "confirm";

        }


    }else if(isset($_POST["send"]) && $_POST["send"]){
        // フォーム送信完了の処理
        $message = "お問い合わせを受け付けました\r\n"
            ."名前". $_SESSION["yourname"]. "\r\n"
            ."email". $_SESSION["email"]. "\r\n"
            ."お問い合わせ内容". $_SESSION["message"]. "\r\n"
            // 改行コードをそろえる
            .preg_replace("/\r\n|\r|\n/", "\r\n", $_SESSION["message"]);
        // 入力者へのメール送信
        mail($_SESSION['email'], "お問い合わせありがとうございます", $message);
        // 管理者へのメール送信
        mail("gentinsp@gmail.com", "お問い合わせありがとうございます", $message);
        $_SESSION = array();
        $mode = "send";
    }else{
        // getで来た用のセッションの初期化
        $_SESSION = array();
    }
    // isset($_POST["aa"])で配列のキーがあるか確認している。
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MailformSample</title>
    <style>
       li{
           display:block;
       }
       label{
           display:block;
       }
    </style>
</head>
<body>

    <?php if($mode == "input"){ ?>
        <span>入力画面です</span>
        <!-- 入力画面 -->
        <!-- エラーが発生時の処理 -->
        <?php
        if( $errmessage ){
            echo '<div class="alert alert-danger" role="alert">';
            echo implode('<br>',$errmessage);
            echo '</div>';
        } 
        ?>
        <form action="./index.php" method="post">
            <ul>
                <li>
                    <label>
                        名前
                    </label>
                    <input type="text" name="yourname" value="<?php if(isset($_SESSION["yourname"])){echo $_SESSION['yourname'];}?>">
                </li>
                <li>
                    <label> Eメール</label>
                    <input type="email" name="email" value="<?php if(isset($_SESSION['email'])){echo $_SESSION['email'];}?>">
                </li>
                <li>
                    <label>本文 </label>
                    <textarea name="message"><?php if(isset($_SESSION['message'])){echo $_SESSION['message'];}?></textarea>
                </li>
                <li>
                    <input type="submit" name="confirm" value="確認" class="submit-btn">
                </li>
            </ul>
        </form>
       
    <?php  } else if( $mode == "confirm") { ?>
         <span>確認画面です</span>
         <!-- 確認画面 -->
         <form action="./index.php" method="post">
        <ul>
            <li>
               名前：<?php echo $_SESSION['yourname'] ?>
            </li>
            <li>
                Eメール：
                <?php echo $_SESSION["email"] ?>
            </li>
            <li>
                本文：
                <?php echo nl2br($_SESSION["message"]) ?>
            </li>
            <li>
                <input type="submit" name="back" value="戻る">
                <input type="submit" name="send" value="送信">
            </li>
        </ul>
    </form>
        
    <?php }else{?>
        <!-- 完了画面 -->
        送信しました。お問い合わせありがとうございました。
    <?php }?>
</body>
</html>