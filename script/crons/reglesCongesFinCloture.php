#!/usr/bin/php
<?php
/*
 * SCRIPT à exécuter après "reglesRtt.php" & "reglesCongesMensuel.php"
 * Gère UNIQUEMENT la bascule
 * 
 * $_REQUEST['force_http'] => permet de lancer le script via navigateur
 * $_REQUEST['force_bascule'] => permet de déclancher la bascule de tous les compteurs
 * $_REQUEST['force_rollback'] => permet de faire un rollback au lieu d'un commit (attention la table rh_compteur doit être en innoDB)
 */

if (!isset($_REQUEST['force_http']))
{
        $sapi_type = php_sapi_name();
        $script_file = basename(__FILE__);
        $path=dirname(__FILE__).'/';
        // Test if batch mode
        if (substr($sapi_type, 0, 3) != 'cli') {
            echo "Error: ".$script_file." you must use PHP for CLI mode.\n";
                exit(-1);
        }
}

 	define('INC_FROM_CRON_SCRIPT', true);
	
	chdir(__DIR__);
	
	require('../../config.php');
	require('../../class/absence.class.php');

	$PDOdb=new TPDOdb;
//	$PDOdb->debug=true;

    $timezone = !empty($conf->global->ABSENCE_TIMEZONE) ? $conf->global->ABSENCE_TIMEZONE : 'Europe/Paris';
    $tz = new DateTimeZone($timezone);

	$now = new DateTime('now', $tz);
	$now->setTime(0, 0, 0); // H:i:s => 00:00:00
	
	$o=new TRH_Compteur;
	$o->init_db_by_vars($PDOdb); // TODO remove or not : on sait jamais, dans la nuit :-/
	
	
	//on récupère la date de fin de cloture des congés
	$sqlReqCloture="SELECT rowid FROM ".MAIN_DB_PREFIX."rh_compteur";
	$PDOdb->Execute($sqlReqCloture);
	$Tab=array();
	while($PDOdb->Get_line()) {
		$Tab[] = $PDOdb->Get_field('rowid');
	}

	if (!empty($conf->global->ABSENCE_HIDE_BASCULE_ALL_TO_ZERO)) echo 'WARNING : ABSENCE_HIDE_BASCULE_ALL_TO_ZERO is enable'."<br />\n";
	
	$PDOdb->beginTransaction();
	foreach($Tab as $fk_compteur)
	{
		$compteur=new TRH_Compteur;
		$compteur->load($PDOdb, $fk_compteur);
		
		echo '* Compteur id = '.$compteur->getId().' date_congesCloture = '.dol_print_date($compteur->date_congesCloture, 'day')."<br />\n";
		
		$date_congesCloture = new DateTime('now', $tz);
		$date_congesCloture->setTimestamp($compteur->date_congesCloture);
		$date_congesCloture->modify('+1 day');
		$date_congesCloture->setTime(0, 0, 0); // H:i:s => 00:00:00
		
		// Bascule UNIQUEMENT si le lendemain de ma date de cloture est égale à la date d'exécution du script (pas de <= pour éviter les bascules intempestives en cours d'année si un compteur est mal init)
		if($date_congesCloture->format("Y-m-d H:i:s") === $now->format("Y-m-d H:i:s") || isset($_REQUEST['force_bascule']))
		{
			if (isset($_REQUEST['force_bascule'])) echo '---- BASCULE force_bascule<br />'."\n";
			else echo '---- BASCULE<br />'."\n";
			
			// report des congés autorisé, uniquement des congés positif, car les négatif passeront en déjà pris sur N
			if(!empty($conf->global->ABSENCE_REPORT_CONGE) && $compteur->congePrecReste>0) {
				if (!empty($conf->global->ABSENCE_HIDE_BASCULE_ALL_TO_ZERO)) $compteur->reportCongesNM1 = 0;
				else $compteur->reportCongesNM1 = $compteur->congePrecReste;
				$compteur->congePrecReste = 0;
			}
			else {
				$compteur->reportCongesNM1 = 0;
				if($compteur->congePrecReste>0) $compteur->congePrecReste = 0;
			}
			
			// Création de cette conf suite au tk7005
			if (!empty($conf->global->ABSENCE_HIDE_BASCULE_ALL_TO_ZERO))
			{
				$compteur->congesPrisNM1 = 0;
				$compteur->acquisExerciceNM1 = 0;
				$compteur->acquisAncienneteNM1 = 0;
				$compteur->acquisHorsPeriodeNM1 = 0;
				$compteur->nombrecongesAcquisAnnuel = 0;
				$compteur->nombreCongesAcquisMensuel = 0;
			}
			else
			{
				$compteur->congesPrisNM1=$compteur->congesPrisN - $compteur->congePrecReste; // ex : -4, incrémente le déjà pris de 4
			
				$compteur->acquisExerciceNM1 = ceil($compteur->acquisExerciceN) + $compteur->nombrecongesAcquisAnnuel;

				$compteur->acquisAncienneteNM1 = $compteur->acquisAncienneteN;
				$compteur->acquisHorsPeriodeNM1 = $compteur->acquisHorsPeriodeN;
			}
						
			$compteur->acquisExerciceN = 0;
			$compteur->acquisHorsPeriodeN = 0;
			$compteur->congesPrisN = 0;
			$compteur->date_congesCloture = strtotime('+1 year',$compteur->date_congesCloture);
			
			echo '---- Prochaine date_congesCloture = '.dol_print_date($compteur->date_congesCloture, 'day')."<br /><br />\n\n";
			
			$compteur->save($PDOdb);
		}
	}

if (isset($_REQUEST['force_rollback'])) $PDOdb->rollBack();
else $PDOdb->commit();

$PDOdb->close();
