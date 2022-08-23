#!/usr/bin/php
<?php
/*
 * SCRIPT à exécuter avant "reglesCongesFinCloture.php"
 * Gère la bascule ET le cumul mensuel
 * 
 * $_REQUEST['force_http'] => permet de lancer le script via navigateur
 * $_REQUEST['force_bascule'] => permet de déclancher la bascule de tous les compteurs (annuel & mensuel)
 * $_REQUEST['force_cumul_mensuel'] => permet de déclancher un cumul des RTT cumulés mensuel (comme si nous étions le 1er d'un mois)
 * $_REQUEST['force_rollback'] => permet de faire un rollback au lieu d'un commit (attention la table rh_compteur doit être en innoDB)
 */
if (!isset($_REQUEST['force_http']))
{
	$sapi_type = php_sapi_name();
	$script_file = basename(__FILE__);
	$path = dirname(__FILE__).'/';
	// Test if batch mode
	if (substr($sapi_type, 0, 3) != 'cli')
	{
		echo "Error: ".$script_file." you must use PHP for CLI mode.\n";
		exit(-1);
	}
}

define('INC_FROM_CRON_SCRIPT', true);

chdir(__DIR__);

require('../../config.php');
require('../../class/absence.class.php');
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';

$PDOdb = new TPDOdb;
$PDOdb->db->debug = true;

$TCompteur = array();


$sql = 'SELECT rowid FROM '.MAIN_DB_PREFIX.'rh_compteur';
$resql = $db->query($sql);
if ($resql)
{
	while ($obj = $db->fetch_object($resql))
	{
		$o = new TRH_Compteur;
		$o->load($PDOdb, $obj->rowid);
		$TCompteur[] = $o;
	}
}

$timezone = !empty($conf->global->ABSENCE_TIMEZONE) ? $conf->global->ABSENCE_TIMEZONE : 'Europe/Paris';
$tz = new DateTimeZone($timezone);

$now = new DateTime('now', $tz);
$now->setTime(0, 0, 0); // H:i:s => 00:00:00

$first_day_of_now = dol_get_first_day($now->format('Y'), $now->format('m')); // timestamp

$md_now = $now->format('md'); // je récupère le mois et jour pour savoir si je suis sur un 1er jour du mois (cumul des RTT mensuel)
$md_first_day_of_now = date('md', $first_day_of_now);

if (!empty($conf->global->ABSENCE_HIDE_BASCULE_ALL_TO_ZERO)) echo 'WARNING : ABSENCE_HIDE_BASCULE_ALL_TO_ZERO is enable'."<br />\n";
echo 'Date du jour = '.dol_print_date($now->getTimestamp(), 'day')."<br />\n";

$PDOdb->beginTransaction();
foreach ($TCompteur as $compteur)
{
	echo '* Compteur id = '.$compteur->getId().' date_rttCloture = '.dol_print_date($compteur->date_rttCloture, 'day')."<br />\n";

	$date_rttCloture = new DateTime('now', $tz);
	$date_rttCloture->setTimestamp($compteur->date_rttCloture);
	$date_rttCloture->modify('+1 day');
	$date_rttCloture->setTime(0, 0, 0); // H:i:s => 00:00:00
	
	// Bascule UNIQUEMENT si le lendemain de ma date de cloture est égale à la date d'exécution du script (pas de <= pour éviter les bascules intempestives en cours d'année si un compteur est mal init)
	if ($date_rttCloture->format("Y-m-d H:i:s") === $now->format("Y-m-d H:i:s")|| isset($_REQUEST['force_bascule']))
	{
		if (isset($_REQUEST['force_bascule'])) echo '---- BASCULE force_bascule<br />'."\n";
		else echo '---- BASCULE<br />'."\n";

		echo '---- Type acquisition = '.$compteur->rttTypeAcquisition."<br />\n";
		
		// Création de cette conf suite au tk7005
		if (!empty($conf->global->ABSENCE_HIDE_BASCULE_ALL_TO_ZERO))
		{
			$compteur->rttNonCumuleReportNM1 = 0;
			$compteur->rttCumuleReportNM1 = 0;
			$compteur->rttCumulePris = 0;
			$compteur->rttNonCumulePris = 0;
			$compteur->rttCumuleAcquis = 0;
			$compteur->rttNonCumuleAcquis = 0;
			$compteur->rttCumulePris = 0;
			$compteur->rttCumuleTotal = 0;
			$compteur->rttNonCumuleTotal = 0;
			$compteur->rttAcquisAnnuelCumuleInit = 0;
			$compteur->rttAcquisAnnuelNonCumuleInit = 0;
		}
		else // Comportement standard
		{
			if ($compteur->reportRtt == 1)
			{
				echo '---- report RTT cumulés = '.$compteur->rttCumuleTotal.' & report RTT non cumulés = '.$compteur->rttNonCumuleTotal."<br />\n";
				$compteur->rttNonCumuleReportNM1 = $compteur->rttNonCumuleTotal;
				$compteur->rttCumuleReportNM1 = $compteur->rttCumuleTotal;
			}

			// A voir, pcq les RRT non cumulés ne sont pas init avec l'acquisition mensuelle, ça reste théoriquement de l'annuelle ($compteur->rttNonCumuleAcquis = $compteur->rttAcquisAnnuelNonCumuleInit;)
			if ($compteur->reportRtt != 1 && $compteur->rttTypeAcquisition === 'Mensuel')
			{
				$compteur->rttCumulePris = $compteur->rttCumulePrisN1;
				$compteur->rttNonCumulePris = $compteur->rttNonCumulePrisN1;

				$compteur->rttCumuleAcquis = 0;
				$compteur->rttNonCumuleAcquis = 0;
			}

			if ($compteur->rttTypeAcquisition === 'Annuel')
			{
				$compteur->rttCumulePris = $compteur->rttCumulePrisN1;
				$compteur->rttNonCumulePris = $compteur->rttNonCumulePrisN1;

				$compteur->rttCumuleAcquis = $compteur->rttAcquisAnnuelCumuleInit;
				$compteur->rttNonCumuleAcquis = $compteur->rttAcquisAnnuelNonCumuleInit;

				$compteur->rttCumuleTotal = $compteur->rttCumuleAcquis + $compteur->rttCumuleReportNM1 - $compteur->rttCumulePris;
				$compteur->rttNonCumuleTotal = $compteur->rttNonCumuleAcquis + $compteur->rttNonCumuleReportNM1 - $compteur->rttNonCumulePris;
			}
		}

		$compteur->rttCumulePrisN1 = 0;
		$compteur->rttNonCumulePrisN1 = 0;

		$compteur->date_rttCloture = strtotime('+1 year', $compteur->date_rttCloture);
		$compteur->rttannee += 1;

		echo '---- Prochaine date_rttCloture = '.dol_print_date($compteur->date_rttCloture, 'day')."<br />\n";
		
		$compteur->save($PDOdb);
	}


	// Nous sommes sur un 1er jour du mois, donc on crédite les compteurs mensuel
	if (($compteur->rttTypeAcquisition === 'Mensuel' && $md_now === $md_first_day_of_now) || isset($_REQUEST['force_cumul_mensuel']))
	{
		echo '---- Cumul mensuel rttCumuleAcquis += '.$compteur->rttAcquisMensuelInit."<br />\n";
		// Attention à prendre avec des pincettes car c'est du spécifique
		$compteur->rttCumuleAcquis += $compteur->rttAcquisMensuelInit;
		$compteur->save($PDOdb);
	}
}

if (isset($_REQUEST['force_rollback'])) $PDOdb->rollBack();
else $PDOdb->commit();

$PDOdb->close();
