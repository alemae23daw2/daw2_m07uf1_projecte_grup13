<?php
	define('FITXER_GESTORS',"usuaris/gestors");
	define('GESTOR',"2");

	session_start();

	function fLlegeixFitxer($nomFitxer){
        if ($fp=fopen($nomFitxer,"r")) {
            $midaFitxer=filesize($nomFitxer);
            $dades = explode(PHP_EOL, fread($fp,$midaFitxer));
            array_pop($dades);
            fclose($fp);
        }
        return $dades;
    }

    function fContaEntrades($f){
		$conta = 0;
		$usuaris = fLlegeixFitxer($f);
        foreach ($usuaris as $usuari) {
            $conta += 1;
        }
		return $conta;
	}

    function fActualitzaUsuaris($nomUsuari,$ctsnya,$fullname, $mail, $telf){
		$ctsnya_hash=password_hash($ctsnya,PASSWORD_DEFAULT);
		$gestorID = fContaEntrades(FITXER_GESTORS) + 1;
		$dades_nou_usuari=$nomUsuari.":".$ctsnya_hash.":".$mail.":"."2".":"."G-$gestorID".":"."$fullname".":"."$telf".":"."\n";
		if ($fp=fopen(FITXER_GESTORS,"a")) {
			if (fwrite($fp,$dades_nou_usuari)){
				$afegit=true;
			}
			else{
				$afegit=false;
			}				
			fclose($fp);
		}
		else{
			$afegit=false;
		}
		return $afegit;
	}

	if ((isset($_POST['nom_nou_usuari'])) && (isset($_POST['cts_nou_usuari'])) && (isset($_POST['fullname_nou_usuari'])) && (isset($_POST['mail_nou_usuari'])) && (isset($_POST['telf_nou_usuari']))){		
		$afegit=fActualitzaUsuaris($_POST['nom_nou_usuari'],$_POST['cts_nou_usuari'],$_POST['fullname_nou_usuari'],$_POST['mail_nou_usuari'],$_POST['telf_nou_usuari']);
		$_SESSION['afegit']=$afegit;
        echo "L'usuari ha sigut afegit";
		header("refresh: 2; url=login.php");
	}
	
	define('FITXER_ADMIN',"usuaris/admin");
	define('ADMIN',"1");

	function fComprovaPermis(){
		$info = fLlegeixFitxer(FITXER_ADMIN);
		foreach ($info as $usuari) {
			$dadesUsuari = explode(":", $usuari);
			if($dadesUsuari[0] != $_SESSION['usuari']) continue;
			if($dadesUsuari[0] == $_SESSION['usuari']){
				return $dadesUsuari[3];
			}
		}
		return 26;
	}
	
	if(fComprovaPermis() != ADMIN){
		header("Location: auth_error.php");
	}
	
	if (!isset($_SESSION['usuari'])){
		header("Location: login_error.php");
	}
	if (!isset($_SESSION['expira']) || (time() - $_SESSION['expira'] >= 0)){
		header("Location: logout_expira_sessio.php");
	}	
?>
<!DOCTYPE html>
<html lang="ca">
	<head>
		<meta charset="utf-8">
		<title>Registre</title>
	</head>
	<body>
		<h3><b>Registre d'usuaris del visualitzador de l'agenda</b></h3>
		<p><b>Indica les dades de l'usuari a registrar dins de l'aplicació: </b></p>			
		<form action="registreGestor.php" method="POST">
			<p>
				<label>Nom del nou Gestor:</label> 
				<input type="text" name="nom_nou_usuari" required>
			</p>
			<p>
				<label>Contrasenya del nou Gestor:</label> 
				<input type="password" name="cts_nou_usuari" required>
			</p>
            <p>
				<label>Nom Complet del nou Gestor:</label> 
				<input type="text" name="fullname_nou_usuari" required>
			</p>
			<p>
				<label>Correu del nou Gestor:</label> 
				<input type="text" name="mail_nou_usuari" required>
			</p>
			<p>
				<label>Telefon del nou Gestor:</label> 
				<input type="text" name="telf_nou_usuari" required>
			</p>
			<input type="submit" value="Enregistra el nou usuari"/>
		</form>
		<button onclick="history.back()">Torna enrere</button>
		<label class="diahora"> 
        <?php
			echo "<p>Usuari actual: ".$_SESSION['usuari']."</p>";
			date_default_timezone_set('Europe/Andorra');
			echo "<p>Data i hora: ".date('d/m/Y h:i:s')."</p>";	
        ?>
        </label>
	</body>
</html>

