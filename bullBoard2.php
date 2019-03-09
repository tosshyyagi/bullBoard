<?php
	$dsn='データベース名'; //データベース名
	$user='username';	//username
	$password = 'password';		//password
	$pdo = new PDO($dsn,$user,$password,array(PDO::ATTR_ERRMODE =>
	PDO::ERRMODE_WARNING));
	$sql = "CREATE TABLE IF NOT EXISTS contribute"."("."id INT,"."name char(32),"."comment TEXT,"."datetime datetime,"."pass TEXT".");";
	$stmt = $pdo->query($sql);
	
	$id1 = $pdo ->query("select * from contribute");
	$id1->execute();
	if($id1->rowCount() > 0){
		$id = $id1->rowCount() + 1;
	}
	else{
		$id = 1;
	}
	//echo $id; 確認用
	$name = $_POST['name'];
	$name = htmlspecialchars($name);
	$comment = $_POST['comment'];
	$comment = htmlspecialchars($comment);
	$pass = $_POST['password'];
	$pass = htmlspecialchars($pass);
	$edit = $_POST['edit'];
	$edit = htmlspecialchars($edit);
	$dele = $_POST['delete'];
	$dele = htmlspecialchars($dele);
	$dmode = $_POST['dmode'];
	$dmode = htmlspecialchars($dmode);
	$emode = $_POST['emode'];
	$emode = htmlspecialchars($emode);
	$emode2 = $_POST['emode2'];
	$emode2 = htmlspecialchars($emode2);

	$tim = date("Y/m/d H:i:s");
	
	if(empty($pass)==false && empty($dmode)==True && empty($emode)==True){
		$sql = $pdo -> prepare("INSERT INTO contribute (id, name, comment, datetime, pass) VALUES (:id,:name,:comment,:datetime,:pass)");
		$sql -> bindParam(':id',$id,PDO::PARAM_INT);
		$sql -> bindParam(':name',$name,PDO::PARAM_STR);
		$sql -> bindParam(':comment',$comment,PDO::PARAM_STR);
		$sql -> bindParam(':datetime',$tim,PDO::PARAM_STR);
		$sql -> bindParam(':pass',$pass,PDO::PARAM_STR);
		$sql -> execute();
	}
	
	$judge = 0;
	if(empty($dmode)==false){
		$t = $pdo -> query("SELECT pass FROM contribute where id=$dmode");
		$jarray = $t -> fetch();
		$jnum = $jarray['pass'];
		if($jnum == $pass){
			$judge = 1;
		}
	}
	if(empty($emode)==false){
		$t = $pdo -> query("SELECT pass FROM contribute where id=$emode");
		$jarray = $t -> fetch();
		$jnum = $jarray['pass'];
		if($jnum == $pass){
			$judge =1;
		}
	}
	//echo $jnum;
	if(empty($dmode)==false && $judge == 1){
		/*for($i==$dmode; $i<=$id; $i++){				//連番を詰めたい。
			if($i>$dmode){
			$ex ="SELECT id FROM contribute WHERE id=$i";
			$results = $pdo -> query($ex);
			$result = $results -> fetch();
			$idup = $result['id'];
			$idup2 = --$idup;
			$sql ='INSERT INTO contribute (id, name, comment, datetime, pass) VALUES (:id,:name,:comment,:datetime,:pass)';
			$stmt = $pdo->prepare($sql);
			$stmt -> bindParam(':id',$idup2,PDO::PARAM_INT);
			$stmt->execute();
			}
		}*/
		$sql ='update contribute set name=:name where id=:id';
		$stmt = $pdo->prepare($sql);
		$aldel="削除されました";
		$stmt -> bindParam(':name',$aldel,PDO::PARAM_STR);
		$stmt -> bindParam(':id',$dmode,PDO::PARAM_INT);
		$stmt->execute();
		
	}
	
	if(empty($emode)==false && $judge == 1){
		$ex ="SELECT name,comment FROM contribute WHERE id=$emode";
		$results = $pdo -> query($ex);
		$result = $results -> fetchAll();
		foreach($result as $row){
			$nname = $row[0];
			$ncomment = $row[1];
		}
	}
	//echo $emode;
	//echo $emode2;
	if(empty($emode2)==false){
		$sql ='update contribute set name=:name,comment=:comment where id=:id';
		$stmt = $pdo->prepare($sql);
		$stmt -> bindParam(':name',$name,PDO::PARAM_STR);
		$stmt -> bindParam(':comment',$comment,PDO::PARAM_STR);
		$stmt -> bindParam(':id',$emode2,PDO::PARAM_INT);
		$stmt->execute();
	}
	if(empty($nname)==false){//以下value値決め
			$vname=$nname;
	}
	else{
			$vname="名前";
	}
	if(empty($ncomment)==false){
			$vcomment=$ncomment;
	}
	else{
			$vcomment="コメント";
	}
	$vpass="";
	if(empty($dele)==false){
			$vpass="削除対象のパスワード";
	}
	if(empty($edit)==false){				//<br /><b>Notice</b>:  Undefined variable: vpass in <b>C:\xampp\htdocs\mission_4-1.php</b> on line <b>140</b><br />
			$vpass="編集対象のパスワード";
	}
	if(empty($nname) == false && empty($ncomment)==false){
			 $vemode2=$emode;
	}
?>
<html>
	<head>
		<meta charset="utf-8">
		<title>mission_4-1</title>
		<link rel="stylesheet" href="stylesheet.css">
	</head>
	<body>
		<h1>簡易掲示板</h1>
		<?php
			$sql ='SELECT * FROM contribute order by id';
			$stmt = $pdo->query($sql);
			$results = $stmt->fetchAll();
			foreach($results as $row){
				if($row['name']=="削除されました"){
					echo 'No.';
					echo $row['id'].' ';
					echo $row['name'].'<br>';
				}
				else{
					echo 'No.';
					echo $row['id'].' ';
					echo "<strong>".$row['name']."</strong>".' ';
					echo $row['datetime'].'<br>';
					echo $row['comment'].' ';
					echo '<br>';
				}
				echo '-----------------------------------------------------';
				echo '<br>';
			}
	  //$stmt = $pdo->query('drop table contribute');　//訂正用
		
		?>
		<h5>新規投稿の際にはパスワードを入力してください</h5>
		<form action="mission_4-1.php" method="post">
		<p>名前：<input type="text" name="name" value="<?php echo($vname)?>"></p>
		<p>コメント：<input type="text" name="comment" value="<?php echo($vcomment)?>" ></p>
		<p>パスワード：<input type="text" name="password" value="<?php echo($vpass)?>"></p>
		<input type='submit' name='submit'value='送信'>
		<h5>削除、編集の際にはまず、対象の番号を入力してください</h5>
		<p>削除番号：<input type="text" name="delete"></p>
		<p>編集番号：<input type="text" name="edit"></p>
		<?php
		if(empty($emode)==false || empty($dmode)==false){
			if($judge==0){
				echo "パスワードが違います";
				echo "<br>";
			}
		}
		?>
		<input type='submit' name='submit'value='送信'>
		<input type='hidden' name='dmode' value="<?php echo($dele)?>">
		<input type='hidden' name='emode' value="<?php echo($edit)?>">
		<input type='hidden' name='emode2' value="<?php echo($vemode2)?>">	
		</form>

	</body>
</html>