<?php 

session_start();
	$initial_directory = $_SESSION['cloud_directory'];


function secure($string){	return urldecode( $string ); } // return urldecode( htmlentities(stripslashes($string),NULL,'UTF-8') );
$_ = array();
foreach($_POST as $key=>$val){	$_[$key]=secure($val); }
foreach($_GET as $key=>$val){	$_[$key]=secure($val); }

$file1 = $initial_directory.$_['file1'];
$file2 = $initial_directory.$_['file2'];

$file1 = preg_replace("#'#", "'\''", $file1);
$file2 = preg_replace("#'#", "'\''", $file2);

$file1 = preg_replace("#\.\.#", "", $file1);
$file2 = preg_replace("#\.\.#", "", $file2);


exec("chmod -R 777 ".$initial_directory);

$result = "error";

switch($_['action']){ // --------Test réception---------------------------------------------------------------------------	

	case 'renommer':				//	modules	

		if(file_exists($file1)){
			rename( $file1, $file2);
			$result = "Fichier renommé";
		}
	break;
	case 'nouveau':

		$extension = pathinfo($file1, PATHINFO_EXTENSION);
		if($extension==''){
			mkdir($file1 , 0777);
			$result = "Dossier créé";
		}else{
			$monfichier = fopen($file1, 'a+');
			fclose($monfichier);
			chmod($file1,0777);
			$result = "Fichier créé";
		}

	break;
	case 'deplacer':

	$extension = pathinfo($file1, PATHINFO_EXTENSION);
	if($extension==''){
		exec("mv '".$file1."' '".$file2."'");
		$result = "Dossier déplacé";
	}else{
		exec("mv '".$file1."' '".$file2."'");
		$result = "Fichier déplacé";
	}
	break;
	case 'copier':
	$extension = pathinfo($file1, PATHINFO_EXTENSION);
	if($extension==''){
		exec("cp -r '".$file1."' '".$file2."'");
		$result = "Dossier déplacé";
	}else{
		exec("cp '".$file1."' '".$file2."'");
		$result = "Fichier déplacé";
	}
	break;
	case 'vider_la_corbeille':
			
		exec("cp -rf  /home/cloud/Corbeille /tmp");
		exec("rm -rf  /home/cloud/Corbeille/*");
		$result = "Corbeille vidé";
	break;

	case 'dezipper':
		
		exec("unzip '".$file1."' -d '".$file2."'");
		$result = "Fichier dézippé";
			
	break;



	case 'sauvegarder_cloud_editer':

		$text = $_['text'];
		$file = $initial_directory.$_['link'];
 
		$monfichier = fopen($file, 'w+');
		fseek($monfichier, 0);
		fwrite($monfichier, $text);
		fclose($monfichier);
		$result = "Fichier sauvé";


	break;

	case 'cloud_download':

	$file = $initial_directory.$_['file'];
	$file_name = $_['file_name'];
	$extension = pathinfo($file_name, PATHINFO_EXTENSION);


	if($extension==''){

		exec("cd ".$file." && zip -r /tmp/".$file_name.".zip ".$file_name);
		$file="/tmp/".$file_name.".zip";
		
//	}else if($extension=='html'){
		
	}else{
		$file.='/'.$file_name;
	}

	if(file_exists($file))
	{
			header('Content-Type: application/force-download');
			header("Content-Transfer-Encoding: binary");
			header('Content-Disposition: attachment; filename='.basename($file));
			header('Pragma: no-cache');
			header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
			header('Expires: 0');
			readfile($file);
			exit();
	}

	$result = "Fichier téléchargé";
	break;
}

echo '{"result":"'.$result.'"}';



?>