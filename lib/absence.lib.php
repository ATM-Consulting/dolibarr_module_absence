<?php

function pointeusePrepareHead() {
	global $langs;

	return array(
		array(dol_buildpath('/absence/pointeuse.php',1), $langs->trans('PunchClock'),'fiche')
	);
}


function absencePrepareHead(&$obj, $type='absence') {
	global $user, $langs;

	switch ($type) {
		case 'absence':

			if($obj->getId()>0) {
				return array(
					array(dol_buildpath('/absence/absence.php?id='.$obj->getId(),1)."&action=view", $langs->trans('Card'),'fiche')
					,array(dol_buildpath('/absence/calendrierAbsence.php?idUser='.$user->id.'&id='.$obj->getId(),1), $langs->trans('Calendar'),'calendrier')
					,array(dol_buildpath('/absence/document.php?id='.$obj->getId(),1), $langs->trans('Documents'),'document')
				);

			}
			else{
				return array();
			}

			break;
		case 'presence':
			if($obj->getId()>0) {
			return array(
				array(dol_buildpath('/absence/presence.php?id='.$obj->getId()."&action=view",1), $langs->trans('Card'),'fiche')
				,array(dol_buildpath('/absence/calendrierAbsence.php?idUser='.$user->id.'&id='.$obj->getId(),1), $langs->trans('Calendar'),'calendrier')
			);
			}
			else{
				return array();
			}
			break;
		case 'absenceCreation':
			return array(
				array(dol_buildpath('/absence/absence.php?action=new',1), $langs->trans('Card'),'fiche')
			);
			break;
		case 'presenceCreation':
			return array(
				array(dol_buildpath('/absence/presence.php?action=new',1), $langs->trans('Card'),'fiche')
			);
			break;


	}
}


function compteurPrepareHead(&$obj, $type='compteur', $fk_user, $nomUser='', $prenomUser='') {
	global $user, $langs;

	switch ($type) {

		case 'compteur':
			//eif($user->rights->absence->myactions->modifierParamGlobalConges=="1"){
			return array(
				array(dol_buildpath('/absence/compteur.php?action=view&fk_user='.$fk_user.'&id='.$obj->getId(),1), $langs->trans('CounterOf') . ' ' . $nomUser . ' ' . $prenomUser, 'compteur')
				,array(dol_buildpath('/absence/compteur.php?action=log&fk_user='.$fk_user.'&id='.$obj->getId(),1), $langs->trans('Log'), 'log')

			);
			break;
	}
}

function adminCompteurPrepareHead(&$obj, $type='compteur') {
	global $user, $langs;
	switch ($type) {

		case 'compteur':
			return array(
			array(dol_buildpath('/absence/compteur.php?action=compteurAdmin',1), $langs->trans('HolidayCounter'), 'compteur')
			);
			break;
	}
}

function adminCongesPrepareHead($type='compteur') {
	global $user, $langs;
	switch ($type) {

		case 'compteur':
			return array(
				array(dol_buildpath('/absence/adminConges.php',1), $langs->trans('GlobalHolidaysData'),'adminconges')
				,array(dol_buildpath('/absence/typeAbsence.php',1), $langs->trans('AbsencesTypes'),'typeabsence')
				,array(dol_buildpath('/absence/typePresence.php',1), $langs->trans('PresencesTypes'),'typepresence')
			);
			break;
	}}

function adminRecherchePrepareHead(&$obj, $type='recherche') {
	global $user;
	switch ($type) {

		case 'recherche':
			return array(
				array(dol_buildpath('/absence/rechercheAbsence.php',1), $langs->trans('SearchAbsence'),'recherche')
			);
			break;
		case 'planning':
			return array(
				array(dol_buildpath('/absence/rechercheAbsence.php',1), $langs->trans('SearchAbsence'),'recherche')
			);
			break;
	}
}

function edtPrepareHead(&$obj, $type='absence') {
	global $user, $langs,$conf;

	switch ($type) {

		case 'emploitemps':

		    // to return on default planning
		    $PDOdb = new TPDOdb();
		    $defaultEmploiTemps = new TRH_EmploiTemps();
		    $defaultEmploiTemps->load_by_fkuser($PDOdb, $obj->fk_user);

			$Tab=array(
			    array(dol_buildpath(
			        ($obj->getId() > 0 ? '/absence/emploitemps.php?action=view&id='.$defaultEmploiTemps->getId() : '/absence/emploitemps.php') ,1)
					, $langs->trans('Schedule')
					,'emploitemps')
			);

            if($conf->jouroff->enabled) $Tab[] = array(dol_buildpath('/jouroff/admin/jouroff_setup.php?fk_user='.$user->id,1), $langs->trans('HolidaysOrNoWorkingDays'),'joursferies');

            return $Tab;

			break;

	}
}

function reglePrepareHead(&$obj, $type='regle') {
	global $user, $langs;

	switch ($type) {
		case 'regle':
			return array(
				array(dol_buildpath('/absence/regleAbsence.php?fk_user='.$user->id,1), $langs->trans('AbsencesRules'),'regle')
			);
			break;
		case 'import':
			return array(
				array(dol_buildpath('/ressource/documentRegle.php',1), $langs->trans('Card'),'fiche')
			);
			break;
	}
}

//fonction qui permet d'enregistrer le libellé d'une absence suivant son type
function saveLibelle($type){ //TODO deprecated
	global $langs;

	switch($type){
		case 'rttcumule':
			return $langs->trans('CumulatedDayOff');
		break;
		case 'rttnoncumule':
			return $langs->trans('NonCumulatedDayOff');
		break;
		case 'conges':
			return $langs->trans('HolidaysAbsence');
		break;
		case 'maladiemaintenue':
			return $langs->trans('SicknessAbsenceMaintained');
		break;
		case 'maladienonmaintenue':
			return $langs->trans('SicknessAbsenceNonMaintained');
		break;
		case 'maternite':
			return $langs->trans('MaternityAbsence');
		break;
		case 'pathologie':
			return $langs->trans('PathologyAbsence');
		break;
		case 'paternite':
			return $langs->trans('PaternityAbsence');
		break;
		case 'chomagepartiel':
			return $langs->trans('PartialUnemploymentAbsence');
		break;
		case 'nonremuneree':
			return $langs->trans('HolidayAbsenceWithoutBalance');
		break;
		case 'accidentdetravail':
			return $langs->trans('WorkAccidentAbsence');
		break;
		case 'maladieprofessionnelle':
			return $langs->trans('ProSicknessAbsence');
		break;
		case 'congeparental':
			return $langs->trans('HolidayParentalAbsence');
		break;
		case 'accidentdetrajet':
			return $langs->trans('RoadAccidentAbsence');
		break;
		case 'mitempstherapeutique':
			return $langs->trans('TherapeuticMidTimeAbsence');
		break;
		case 'mariage':
			return $langs->trans('Mariage');
		break;
		case 'deuil':
			return $langs->trans('Mourning');
		break;
		case 'naissanceadoption':
			return $langs->trans('BornOrAdoption');
		break;
		case 'enfantmalade':
			return $langs->trans('SickChild');
		break;
		case 'demenagement':
			return $langs->trans('Moving');
		break;
		case 'cours':
			return $langs->trans('SessionAbsence');
		break;
		case 'preavis':
			return $langs->trans('PreparedAbsence');
		break;
		case 'rechercheemploi':
			return $langs->trans('SearchJobAbsence');
		break;
		case 'miseapied':
			return $langs->trans('WarningAbsence');
		break;
		case 'nonjustifiee':
			return $langs->trans('NoJustifiedAbsence');
		break;
		case 'cppartiel':
			return $langs->trans('HolidayPartialTime');
		break;

	}
}

//fonction qui permet de renvoyer le code de l'absence
function saveCodeTypeAbsence(&$PDOdb, $type){ // TODO deprecated
	$ta = new TRH_TypeAbsence;
	$ta->load_by_type($PDOdb, $type);

	return $ta->codeAbsence;
}

//fonction permettant de retourner le libelle de l'état de l'absence (à Valider...)
function saveLibelleEtat($etat){
	global $langs;

	switch($etat){
		case 'Avalider':
			return $langs->trans('WaitingValidation');
		break;
		case 'Validee':
			return $langs->trans('Accepted');
		break;
		case 'Refusee':
			return $langs->trans('Refused');
		break;

	}
}





//arrondi variable float à 2 virgules
function round2Virgule($variable){
	if($variable==0){
		return '0';
	}else {
		return number_format($variable,2,'.','');
	}
}

//retourne la date au format "d/m/Y"
function php2dmy($phpDate){
    return date("d/m/Y", $phpDate);
}


//fonction permettant l'envoi de mail
function mailConges(&$absence,$presence=false, $TPieceJointe = array()){
	global $db, $langs,$conf, $user;

	//$from = USER_MAIL_SENDER;
	$from = !empty($user->email) ? $user->email : $conf->global->MAIN_MAIL_EMAIL_FROM;
	if(!empty($conf->global->RH_USER_MAIL_OVERWRITE)) $from = $conf->global->RH_USER_MAIL_OVERWRITE;

	$dont_send_mail = GETPOST('dontSendMail');

	/*
	 * Mail destinataire
	 */
	$userAbsence = new User($db);
	$userAbsence->fetch($absence->fk_user);

	$sendto=$userAbsence->email;
	$name=$userAbsence->lastname;
	$firstname=$userAbsence->firstname;


	$TBS=new TTemplateTBS();
	if($absence->etat=='Avalider'){

		if(!$presence){
			$subject = $langs->transnoentities('HolidayRequestCreation');
			$tpl = dol_buildpath('/absence/tpl/mail.absence.creation.tpl.php');
		}
		else{
			$subject = $langs->transnoentities('PresenceRequestCreation');
			$tpl = dol_buildpath('/absence/tpl/mail.presence.creation.tpl.php');
		}

		$message = $TBS->render($tpl
			,array()
			,array(
				'absence'=>array(
					'nom'=> htmlentities($name, ENT_COMPAT | ENT_HTML401, 'ISO-8859-1')
					,'prenom'=> htmlentities($firstname, ENT_COMPAT | ENT_HTML401, 'ISO-8859-1')
					,'date_debut'=> php2dmy($absence->date_debut)
					,'date_fin'=>php2dmy($absence->date_fin)
					,'libelle'=>htmlentities($absence->libelle, ENT_COMPAT | ENT_HTML401, 'UTF-8')
					,'libelleEtat'=>htmlentities($absence->libelleEtat, ENT_COMPAT | ENT_HTML401, 'UTF-8')

				)
				,'translate' => array(
					'Hello' => $langs->transnoentities('Hello'),
					'MailYourRequestOf' => $langs->transnoentities('MailYourRequestOf'),
					'DateInterval' => $langs->transnoentities('DateInterval', php2dmy($absence->date_debut), php2dmy($absence->date_fin)),
					'MailActionCreate' => $langs->transnoentities('MailActionCreate'),
					'MailStateIsNow' => $langs->transnoentities('MailStateIsNow')
				)
			)
		);


	}
	else if($absence->etat=='Validee'){
		if(!$presence){
			$subject = $langs->transnoentities('HolidayRequestAcceptance');
			$tpl = dol_buildpath('/absence/tpl/mail.absence.acceptation.tpl.php');
		}
		else{
			$subject = $langs->transnoentities('PresenceRequestAcceptance');
			$tpl = dol_buildpath('/absence/tpl/mail.presence.acceptation.tpl.php');
		}

		$message = $TBS->render($tpl
			,array()
			,array(
				'absence'=>array(
					'nom'=> htmlentities($name, ENT_COMPAT | ENT_HTML401, 'ISO-8859-1')
	                ,'prenom'=> htmlentities($firstname, ENT_COMPAT | ENT_HTML401, 'ISO-8859-1')
	                ,'date_debut'=> php2dmy($absence->date_debut)
	                ,'date_fin'=>php2dmy($absence->date_fin)
	                ,'libelle'=>htmlentities($absence->libelle, ENT_COMPAT | ENT_HTML401, 'UTF-8')
	                ,'libelleEtat'=>htmlentities($absence->libelleEtat, ENT_COMPAT | ENT_HTML401, 'UTF-8')
					,'commentaireValideur'=>utf8_encode($absence->commentaireValideur)
				)
				,'translate' => array(
					'Hello' => $langs->transnoentities('Hello'),
					'SuperiorComment' => $langs->transnoentities('SuperiorComment'),
					'MailYourRequestOf' => $langs->transnoentities('MailYourRequestOf'),
					'DateInterval' => $langs->transnoentities('DateInterval', php2dmy($absence->date_debut), php2dmy($absence->date_fin)),
					//'MailActionChange' => $langs->transnoentities('MailActionChange', htmlentities($absence->libelleEtat, ENT_COMPAT | ENT_HTML401, 'UTF-8'))
					'MailActionChange' => $langs->transnoentities('MailActionChange', $absence->libelleEtat)
				)
			)
		);
		//echo $message;exit;
		if($conf->global->ABSENCE_ALERT_OTHER_VALIDEUR) {
			dol_include_once('/valideur/class/valideur.class.php');
			$PDOdb=new TPDOdb;
			$TValideur = TRH_valideur_groupe::getUserValideur($PDOdb, $user, $absence, 'Conges');

			foreach($TValideur as $fk_valideur) {
				$valideur=new User($db);
				$valideur->fetch($fk_valideur);
				$valideur->getrights('absence');

				if(!empty($valideur->email) && !empty($valideur->rights->absence->myactions->IfAllValideurAlertedAlerteMe) && !$dont_send_mail) {
					$mail = new TReponseMail($from,$valideur->email,'['.$langs->trans('AbsenceCopy').'] '. $subject,$message);

					$result = $mail->send(true, 'utf-8');
				//	print "{$valideur->email}<br />";
				}

			}

		}

	}
	else if($absence->etat=='Refusee'){
		if(!$presence){
			$subject = $langs->transnoentities('HolidayRequestDenied');
			$tpl = dol_buildpath('/absence/tpl/mail.absence.refus.tpl.php');
		}
		else{
			$subject = $langs->transnoentities('PresenceRequestDenied');
			$tpl = dol_buildpath('/absence/tpl/mail.presence.refus.tpl.php');
		}

		$absence->libelleEtat=saveLibelleEtat($absence->etat);

		$message = $TBS->render($tpl
			,array()
			,array(
				'absence'=>array(
					/*'nom'=>utf8_encode($name)
					,'prenom'=>utf8_encode($firstname)
					,'date_debut'=>php2dmy($absence->date_debut)
					,'date_fin'=>php2dmy($absence->date_fin)
					,'libelle'=>utf8_encode($absence->libelle)
					,'libelleEtat'=>utf8_encode($absence->libelleEtat)
					*/'nom'=> htmlentities($name, ENT_COMPAT | ENT_HTML401, 'ISO-8859-1')
                                        ,'prenom'=> htmlentities($firstname, ENT_COMPAT | ENT_HTML401, 'ISO-8859-1')
                                        ,'date_debut'=> php2dmy($absence->date_debut)
                                        ,'date_fin'=>php2dmy($absence->date_fin)
                                        ,'libelle'=>htmlentities($absence->libelle, ENT_COMPAT | ENT_HTML401, 'UTF-8')
                                        ,'libelleEtat'=>htmlentities($absence->libelleEtat, ENT_COMPAT | ENT_HTML401, 'UTF-8')
					,'commentaireValideur'=>utf8_encode(empty($absence->commentaireValideur) ? $_REQUEST['commentaireValideur'] : $absence->commentaireValideur)
				)
				,'translate' => array(
					'Hello' => $langs->transnoentities('Hello'),
					'MailYourRequestOf' => $langs->transnoentities('MailYourRequestOf'),
					'DateInterval' => $langs->transnoentities('DateInterval', php2dmy($absence->date_debut), php2dmy($absence->date_fin)),
					//'MailActionChange' => $langs->transnoentities('MailActionChange', htmlentities($absence->libelleEtat, ENT_COMPAT | ENT_HTML401, 'UTF-8')),
					'MailActionChange' => $langs->transnoentities('MailActionChange', $absence->libelleEtat),
					'ValidatorCommentRequestDenied' => $langs->transnoentities('ValidatorCommentRequestDenied')
				)
			)
		);
	}

	if(!empty($sendto) && !$dont_send_mail) {
		$mail = new TReponseMail($from,$sendto,$subject,$message);

		if(!empty($conf->global->ABSENCE_ADD_INVITATION_TO_ACCEPT_MAIL)) {
			$fileics = absenceCreateICS($absence);
			$mail->add_piece_jointe('absence-'.$absence->getId().'-'.date('Ymdhis').'.ics', $fileics, 'application/ics');
		}

        foreach($TPieceJointe as $pj) {
            $mail->add_piece_jointe($pj, $conf->absence->dir_output.'/'.dol_sanitizeFileName($absence->rowid).'/'.$pj);     // C'est moche mais c'est comme ça
        }

		$result = $mail->send(true, 'utf-8');
		/*if($result) setEventMessage('Email envoyé avec succès à l\'utilisateur');
		else setEventMessage('Erreur lors de l\'envoi du mail à l\'utilisateur');*/
	}

	return 1;
}
function absenceCreateICS(&$absence){
	global $langs;


	$tmfile = tempnam('/tmp','ICS');
	file_put_contents($tmfile, $absence->getICS());

	return $tmfile;
}
//fonction permettant la récupération
function mailCongesValideur(&$PDOdb, &$absence,$presence=false, $TPieceJointe = array()){
	global $conf,$user;

	dol_include_once('/valideur/class/valideur.class.php');
	$TValideur = TRH_valideur_groupe::getUserValideur($PDOdb, $user, $absence, 'Conges');

	if($conf->global->RH_ABSENCE_ALERT_NONJUSTIF_SUPERIOR && $absence->code=='nonjustifiee') {
		$sql="SELECT fk_user FROM ".MAIN_DB_PREFIX."user WHERE rowid=".(int)$absence->fk_user;
		$PDOdb->Execute($sql);
		$PDOdb->Get_line();
		$fk_sup = $PDOdb->Get_field('fk_user');
		if(!empty($fk_sup) && !in_array($conf->global->RH_ABSENCE_ALERT_NONJUSTIF_USER, $TValideur)) $TValideur[] = $fk_sup;
	}

	if($conf->global->RH_ABSENCE_ALERT_NONJUSTIF_USER && $absence->code=='nonjustifiee') {
		if(!in_array($conf->global->RH_ABSENCE_ALERT_NONJUSTIF_USER, $TValideur))  $TValideur[] = $conf->global->RH_ABSENCE_ALERT_NONJUSTIF_USER;
	}

	if(!empty($TValideur)){
		foreach($TValideur as $idVal){
			envoieMailValideur($PDOdb, $absence, $idVal,$presence, $TPieceJointe);
		}
	}

}


//fonction permettant l'envoi de mail aux valideurs de la demande d'absence
function envoieMailValideur(&$PDOdb, &$absence, $idValideur,$presence=false, $TPieceJointe = array()){
	global $db, $langs, $user, $conf;

	$from = !empty($user->email) ? $user->email : $conf->global->MAIN_MAIL_EMAIL_FROM;
	if(!empty($conf->global->RH_USER_MAIL_OVERWRITE)) $from = $conf->global->RH_USER_MAIL_OVERWRITE;

	$userr = new User($db);
	$userr->fetch($absence->fk_user);

	$name=$userr->lastname;
	$firstname=$userr->firstname;

	/*
	 * Mail destinataire
	 */

	$userV = new User($db);
        $userV->fetch($idValideur);

        $nameValideur=$userV->lastname;
        $firstnameValideur=$userV->firstname;
	$sendto = $userV->email;

	$TBS=new TTemplateTBS();

	if($absence->etat == 'deleted') {
	if(!$presence){
		$subject = $langs->transnoentities('NewAbsenceRequestWaitingValidationDeleted');
		$tpl = dol_buildpath('/absence/tpl/mail.absence.deletedValideur.tpl.php');
	}
	else{
		$subject = $langs->transnoentities('NewPresenceRequestWaitingValidationDeleted');
		$tpl = dol_buildpath('/absence/tpl/mail.absence.deletedValideur.tpl.php');
	}

	}
	else{
		if(!$presence){
			$subject = $langs->transnoentities('NewAbsenceRequestWaitingValidation');
			$tpl = dol_buildpath('/absence/tpl/mail.absence.creationValideur.tpl.php');
		}
		else{
			$subject = $langs->transnoentities('NewPresenceRequestWaitingValidation');
			$tpl = dol_buildpath('/absence/tpl/mail.presence.creationValideur.tpl.php');
		}

	}


	$message = $TBS->render($tpl
		,array()
		,array(
			'absence'=>array(
				'nom'=>$name
				,'prenom'=>$firstname
				,'valideurNom'=>$nameValideur
				,'valideurPrenom'=>$firstnameValideur
				,'date_debut'=>php2dmy($absence->date_debut)
				,'date_fin'=>php2dmy($absence->date_fin)
				,'libelle'=>($absence->etat == 'deleted' ? $absence->libelle : '<a href="'.dol_buildpath('/absence/absence.php?id='.$absence->getId().'&action=view',2).'">'.$absence->libelle.'</a>')
				,'libelleEtat'=>$absence->libelleEtat
			)
			,'translate' => array(
				'Hello' => $langs->trans('Hello'),
				'MailNewRequest' => $langs->trans('MailNewRequest'),
				'DateInterval' => $langs->trans('DateInterval', php2dmy($absence->date_debut), php2dmy($absence->date_fin)),
				'MailActionCreate' => $langs->trans('MailActionCreate'),
				'By' => $langs->trans('By'),
				'ValidatorMustWatchIt' => $langs->trans('ValidatorMustWatchIt')
			)
		)
	);

	$dont_send_mail = GETPOST('dontSendMail');

	if(!$dont_send_mail){
		$mail = new TReponseMail($from,$sendto,$subject,$message);

        foreach($TPieceJointe as $pj) $mail->add_piece_jointe($pj, $conf->absence->dir_output.'/'.dol_sanitizeFileName($absence->rowid));

	    	$result = $mail->send(true, 'utf-8');

		if($result) setEventMessage('Email envoyé avec succès au valideur '.$sendto);
                else setEventMessage('Erreur lors de l\'envoi du mail à un valideur '.$sendto,'errors');
	}

	return 1;
}

function supprimerAccent($chaine){
	$chaine = strtr($chaine,"ÀÂÄÇÈÉÊËÌÎÏÑÒÔÕÖÙÛÜ","AAACEEEEIIINOOOOUUU");
	$chaine = strtr($chaine,"àáâãäåçèéêëìíîïñòóôõöùúûüýÿ","aaaaaaceeeeiiiinooooouuuuyy");
	return $chaine;
}

//permet d'additionner deux heures ensemble
function additionnerHeure($dureeTotale, $dureeDiff){
	list($heureT, $minuteT) = explode(':', $dureeTotale);
	//echo "heureT : ".$heureT." minutesT : ".$minuteT;
	list($heureD, $minuteD) = explode(':', $dureeDiff);

	$heureT=$heureT+$heureD;
	$minuteT=$minuteT+$minuteD;

	while($minuteT>60){
		$minuteT-=60;
		$heureT+=1;
	}

	return $heureT.":".$minuteT;
}


//donne la différence entre 2 heures (respecter l'ordre début et fin)
function difheure($heuredeb,$heurefin)
	{

		$hd=explode(":",$heuredeb);
		$hf=explode(":",$heurefin);
		$hd[0]=(int)($hd[0]);$hd[1]=(int)($hd[1]);$hd[2]=(int)($hd[2]);
		$hf[0]=(int)($hf[0]);$hf[1]=(int)($hf[1]);$hf[2]=(int)($hf[2]);
		if($hf[2]<$hd[2]){$hf[1]=$hf[1]-1;$hf[2]=$hf[2]+60;}
		if($hf[1]<$hd[1]){$hf[0]=$hf[0]-1;$hf[1]=$hf[1]+60;}
		if($hf[0]<$hd[0]){$hf[0]=$hf[0]+24;}
		return (($hf[0]-$hd[0]).":".($hf[1]-$hd[1]).":".($hf[2]-$hd[2]));
	}



function horaireMinuteEnCentieme($horaire){
	list($heure, $minute) = explode(':', $horaire);
	$horaireCentieme=$heure+$minute/60;
	return $horaireCentieme;
}

//retourne la date au format "Y-m-d H:i:s"
function php2Date($phpDate){
    return date("Y-m-d H:i:s", $phpDate);
}
function getHistoryCompteurForUser($fk_user,$id_absence,$duree=null,$type=null, $etat=null) {
global $compteurCongeResteCurrentUser,$PDOdb_getHistoryCompteurForUser;

	if(!isset($PDOdb_getHistoryCompteurForUser)) $PDOdb_getHistoryCompteurForUser=new TPDOdb;
	if(!isset($compteurCongeResteCurrentUser)) {

		$compteur =new TRH_Compteur;
		$compteur->load_by_fkuser($PDOdb_getHistoryCompteurForUser, $fk_user);

		$congePrecTotal = $compteur->acquisExerciceNM1 + $compteur->acquisAncienneteNM1 + $compteur->acquisHorsPeriodeNM1 + $compteur->reportCongesNM1;
		$compteurCongeResteCurrentUser = $congePrecTotal - $compteur->congesPrisNM1;
    }

	if(is_null($duree) || is_null($etat) || is_null($type)) {
		$absence = new TRH_Absence;
		$absence->load($PDOdb_getHistoryCompteurForUser, $id_absence);

		$duree = $absence->duree;
		$etat = $absence->etat;
		$type = $absence->type;
	}

	if($etat!='Refusee' && $duree>0 && ($type=='conges' || $type=='cppartiel')) {
		$compteurCongeResteCurrentUser+=$duree;
		return $compteurCongeResteCurrentUser;
		//return '<div align="right">'.number_format($compteurCongeResteCurrentUser,2,',',' ').'</div>';
	}
	else {
		return $compteurCongeResteCurrentUser;
	}

}

function _recap_abs(&$PDOdb, $idGroupeRecherche, $idUserRecherche, $date_debut, $date_fin) {
	global $db, $langs, $conf, $hookmanager, $user;

	if(empty($date_debut)) return false;

	$date_debut = date('Y-m-d', Tools::get_time($date_debut));
	$date_fin = date('Y-m-d', Tools::get_time($date_fin));

	if(!$user->rights->absence->myactions->creerAbsenceCollaborateur) { //Dans ce cas l'utilisateur ne doit pouvoir voir que SES compteurs.
		$idGroupeRecherche = 0;
		$idUserRecherche = $user->id;
	}
	$TStatPlanning = TRH_Absence::getPlanning($PDOdb, $idGroupeRecherche, $idUserRecherche, $date_debut, $date_fin);
//var_dump($TStatPlanning);
	$first=true;

	if(empty($TStatPlanning)) return false;

	$html = '<table class="planning" border="0">';
	$html .= "<tr class=\"entete\">";

	$html .= '<tr>
				<td>' . $langs->trans('Name') . '</td>
				<td>' . $langs->trans('PresenceDay') . '</td>
				<td>' . $langs->trans('AbsenceDay') . '</td>';
	if(empty($conf->global->ABSENCE_HIDE_PUBLIC_HOLIDAY_DAY)) $html .=	'<td>' . $langs->trans('Presence') . ' + ' . $langs->trans('PublicHolidayDay') . '</td>
				<td>' . $langs->trans('Absence') . ' + ' . $langs->trans('PublicHolidayDay') . '</td>
				<td>' . $langs->trans('PublicHolidayDay') . '</td>';
	$html .=    '<td>' . $langs->trans('RemainingBeforeShort') . '</td>
				<td>' . $langs->trans('RemainingCurrent') . '</td>
				<td>' . $langs->trans('acquisRecuperationShort') . '</td>
				<td>' . $langs->trans('soldeRestant') . '</td>
				<td>' . $langs->trans('HeuresPresence') . '</td>
			</tr>';

	foreach($TStatPlanning as $idUser=>$TStat) {
		$u=new User($db);
		$u->fetch($idUser);

		$compteur = new TRH_Compteur;
		$compteur->load_by_fkuser($PDOdb, $idUser);

		$congePrecTotal=$compteur->acquisExerciceNM1 +$compteur->acquisAncienneteNM1+$compteur->acquisHorsPeriodeNM1+$compteur->reportCongesNM1;
		$congePrecReste=round2Virgule($congePrecTotal-$compteur->congesPrisNM1);

		$congeCourantTotal=round2Virgule($compteur->acquisExerciceN+$compteur->acquisAncienneteN	+$compteur->acquisHorsPeriodeN - $compteur->congesPrisN);

		$stat=array();
        $stat['Heurepresence'] = 0;

        foreach($TStat as $date=>$row) {

			@$stat['presence']+=$row['nb_jour_presence'];
			@$stat['absence']+=$row['nb_jour_absence'];
			@$stat['presence+ferie']+=$row['nb_jour_presence'] + $row['nb_jour_ferie'];
			@$stat['absence+ferie']+=$row['nb_jour_absence'] + $row['nb_jour_ferie'] ;
			@$stat['ferie']+=$row['nb_jour_ferie'] ;
			if (!empty($row['typeAbsence']))
			{
				foreach ($row['typeAbsence'] as $rowtype)
				{
					if ($rowtype->isPresence && $rowtype->unite == 'heure')
					{
						@$stat['Heurepresence']+= $rowtype->dureeHeure;
					}
				}
			}

		}

		if(empty($u->lastname)) $u->lastname = $u->login;

		$html .= '<tr><td style="text-align:left;">'.$u->getNomUrl().'</td>';

		$html .= '<td>'.$stat['presence'].'</td>';
		$html .= '<td>'.$stat['absence'].'</td>';
        if(empty($conf->global->ABSENCE_HIDE_PUBLIC_HOLIDAY_DAY)) {
		$html .= '<td>'.$stat['presence+ferie'].'</td>';
		$html .= '<td>'.$stat['absence+ferie'].'</td>';
		$html .= '<td>'.$stat['ferie'].'</td>';
        }
		$html .= '<td>'.$congePrecReste.'</td>';
		$html .= '<td>'.$congeCourantTotal.'</td>';
		$html .= '<td>'.round2Virgule($compteur->acquisRecuperation).'</td>';
		$soldeRestant = price2num($congePrecReste) + price2num($congeCourantTotal) + price2num(round2Virgule($compteur->acquisRecuperation));
		$html .= '<td>'.round2Virgule($soldeRestant).'</td>';
		$html .= '<td>'.$stat['Heurepresence'].'</td>';
		$html .= '</tr>';

	}


	$html .= '</table><p>&nbsp;</p>';

	if (is_object($hookmanager))
	{
		$hookmanager->initHooks(array('absenceInterface'));

		$parameters = array(
			'html' => $html
			,'TStatPlanning' => $TStatPlanning
			,'idGroupeRecherche'=> $idGroupeRecherche
		);

		// Note that $action and $object may be modified by some hooks
		$reshook = $hookmanager->executeHooks('getRecapAbsence', $parameters, $object, $action);
		if (empty($reshook)) $html.= $hookmanager->resPrint;
		else if ($reshook > 0) $html = $hookmanager->resPrint;
	}

	return $html;
}

function getPlanningAbsence(&$PDOdb, &$absence, $idGroupeRecherche, $idUserRecherche) {
global $conf,$db,$user;

		$html='';

		$t_current = $absence->date_debut_planning;

		$annee_old = '';

		$t_max= strtotime(date('Y-m-t',  $absence->date_fin_planning));

		while($t_current<=$t_max) {

			$annee = date('Y', $t_current);
			if($t_current==$absence->date_debut_planning) {
//				$date_debut =date('d/m/Y', $absence->date_debut_planning);
				$date_debut = strtotime(date('Y-m-d 00:00:00', $absence->date_debut_planning));
			}
			else {
//				$date_debut =date('01/m/Y', $t_current);
				$date_debut = strtotime(date('Y-m-01 00:00:00', $t_current));
			}

			$t_fin_periode= strtotime(date('Y-m-t',  $t_current));

			if($t_fin_periode>=$absence->date_fin_planning) {
//				$date_fin =date('d/m/Y', $absence->date_fin_planning);
				$date_fin = strtotime(date('Y-m-d 23:59:59', $absence->date_fin_planning));
			}
			else {
//				$date_fin =date('d/m/Y', $t_fin_periode);
				$date_fin = strtotime(date('Y-m-d 23:59:59', $t_fin_periode));
			}

			if($annee!=$annee_old) $html.= '<p style="text-align:left;font-weight:bold">'.$annee.'</strong><br />';

			$html.= _planning($PDOdb, $absence, $idGroupeRecherche, $idUserRecherche, $date_debut, $date_fin, $TStatPlanning );

			$annee_old = $annee;


			$t_current=strtotime('+1 month', $t_current);
		}

		$html.= _recap_abs($PDOdb, $idGroupeRecherche, $idUserRecherche, date('d/m/Y',$absence->date_debut_planning), date('d/m/Y',$absence->date_fin_planning));

		return $html;

}

function _getSQLListValidation($userid)
{
	if (!class_exists('TRH_valideur_groupe'))
	{
		if (!defined('INC_FROM_DOLIBARR')) define('INC_FROM_DOLIBARR', 1);
		dol_include_once('/valideur/config.php');
		dol_include_once('/valideur/class/valideur.class.php');
	}

	return TRH_valideur_groupe::getSqlListObject('Conges', array('onlyValidable' => true));
}

/**
 * @param $PDOdb
 * @param TRH_Absence $absence
 * @param $idGroupeRecherche
 * @param $idUserRecherche
 * @param $date_debut
 * @param $date_fin
 * @return string
 */
function _planning(&$PDOdb, &$absence, $idGroupeRecherche, $idUserRecherche, $date_debut, $date_fin) {
	global $langs,$user,$db, $hookmanager;

	dol_include_once('/valideur/class/valideur.class.php');
    $TColor = array();
//on va obtenir la requête correspondant à la recherche désirée
	// Test si somme des trois groupes = (99999 * 3) Tous les select sur Aucun alors recherche vide
	if(array_sum($idGroupeRecherche) == 299997)$idGroupeRecherche = array(); //TODO mais c'est quoi cette merde ?!
	if(array_sum($idGroupeRecherche)>0) $idUserRecherche = 0; // si un groupe est sélectionner on ne prend pas en compte l'utilisateur


	$TPlanningUser=$absence->requetePlanningAbsence2($PDOdb, $idGroupeRecherche, $idUserRecherche, $date_debut, $date_fin);
//var_dump($TPlanningUser);exit;

	$TJourTrans=array(
		1=>substr($langs->trans('Monday'),0,1)
		,2=>substr($langs->trans('Tuesday'),0,1)
		,3=>substr($langs->trans('Wednesday'),0,1)
		,4=>substr($langs->trans('Thursday'),0,1)
		,5=>substr($langs->trans('Friday'),0,1)
		,6=>substr($langs->trans('Saturday'),0,1)
		,7=>substr($langs->trans('Sunday'),0,1)
	);
	$html='';
	$tabUserMisEnForme=array();
    $html .= '<div style="display: flex; width: 100%;">';
	$html .= '<table class="planning" border="0">';
	$html .= "<tr class=\"entete\">";
	$html .= "<td ></td>";
	foreach($TPlanningUser as $planning=>$val){
		$planning=date('d/m/Y', $planning);
		$std = new TObjetStd;
		$std->set_date('date_jour', $planning);

		$html .=  '<td colspan="2">'.$TJourTrans[date('N', $std->date_jour)].' '.substr($planning,0,5).'</td>';
		foreach($val as $id=>$TPresent){
			$tabUserMisEnForme[$id][$planning]=$TPresent;
		}
	}
	$html .=  "</tr>";
	$TTotal=array();

	global $TCacheUser;
	if(empty($TCacheUser)) $TCacheUser=array();

	$isValideur =  TRH_valideur_groupe::isValideur($PDOdb, $user->id, $idGroupeRecherche);

	foreach($tabUserMisEnForme as $idUser => $planning){

		if(empty($TCacheUser[$idUser])) {
			$user_courant=new User($db);
			$user_courant->fetch($idUser);
			$TCacheUser[$idUser] = $user_courant;
		}
		$user_courant = $TCacheUser[$idUser];

		$html .=  '<tr >';
		$html .=  '<td style="text-align:right; font-weight:bold;height:20px;" nowrap="nowrap">'.$user_courant->getFullName($langs).'</td>';
//$planning=array();

		/** @var TRH_absenceDay $TAbsencePresence */
		foreach ($planning as $dateJour => $TAbsencePresence)
		{
			if (empty($TTotal[$dateJour])) $TTotal[$dateJour] = 0;
			$class='';

			$std = new TObjetStd;
			$std->set_date('date_jour', $dateJour);
			if (TRH_JoursFeries::estFerie($PDOdb, $std->get_date('date_jour','Y-m-d') )) { $isFerie = 1; $class .= ' jourFerie';  } else { $isFerie = 0; }

			$estUnJourTravaille = TRH_EmploiTemps::estTravaille($PDOdb, $idUser, $std->get_date('date_jour','Y-m-d')); // OUI/NON/AM/PM
			$classTravail= ' jourTravaille'.$estUnJourTravaille;

			$labelJour = '+';//$labelJour = $TJourTrans[date('N', strtotime($dateJour))];

			if (empty($TAbsencePresence))
			{
				if( isset($_REQUEST['no-link']) || (!$user->rights->absence->myactions->creerAbsenceCollaborateur && !$isValideur) ) {
					$linkPop='&nbsp;';
					if ($user->rights->absence->myactions->creerAbsence && $user->id == $idUser)
						$linkPop = '<a title="'.$langs->trans('addAbsenceUser').'" href="javascript:popAddAbsence(\''.$std->get_date('date_jour','Y-m-d').'\', '.$idUser.');" class="no-print">'.$labelJour.'</a>';
				} else {
					$linkPop = '<a title="'.$langs->trans('addAbsenceUser').'" href="javascript:popAddAbsence(\''.$std->get_date('date_jour','Y-m-d').'\', '.$idUser.');" class="no-print">'.$labelJour.'</a>';
				}

				// case libre
				$html .=  '<td class="'.$class.$classTravail.'" rel="am">'.$linkPop.'</td>
					<td class="'.$class.$classTravail.'" rel="pm">'.$linkPop.'</td>';

				if(!$isFerie && ($estUnJourTravaille=='AM' || $estUnJourTravaille=='PM')){
					$TTotal[$dateJour]+=0.5;
				}
				else if(!$isFerie && $estUnJourTravaille=='OUI'){
					$TTotal[$dateJour]+=1;
				}
			}
			else
			{
				$countPresence = count($TAbsencePresence);
				/** @var TRH_absenceDay $ouinon */
				foreach ($TAbsencePresence as $ouinon)
				{
					$toString = $ouinon->__toString();
					$type = $ouinon->getTypeMoment();

					$subclass = '';
					if( isset($_REQUEST['no-link']) || (!$user->rights->absence->myactions->creerAbsenceCollaborateur && !$isValideur) )
					{
						$linkPop='&nbsp;';
						if ($user->rights->absence->myactions->creerAbsence && $user->id == $idUser && !$_REQUEST['no-link'])
						{
							if($ouinon->idAbsence>0 && !$ouinon->isPresence) { $linkPop = '<a title="'.$langs->trans('Show').'" href="'.dol_buildpath('/absence/absence.php?id='.$ouinon->idAbsence.'&action=view',1).'" class="no-print">a</a>'; }
							else if($ouinon->idAbsence>0 && $ouinon->isPresence) { $linkPop = '<a title="'.$langs->trans('Show').'" href="'.dol_buildpath('/absence/presence.php?id='.$ouinon->idAbsence.'&action=view',1).'" class="no-print">p</a>'; }
							else $linkPop = '<a title="'.$langs->trans('addAbsenceUser').'" href="javascript:popAddAbsence(\''.$std->get_date('date_jour','Y-m-d').'\', '.$idUser.');" class="no-print">'.$labelJour.'</a>';
						}
					}
					else
					{
						if($ouinon->idAbsence>0 && !$ouinon->isPresence) { $linkPop = '<a title="'.$langs->trans('Show').'" href="'.dol_buildpath('/absence/absence.php?id='.$ouinon->idAbsence.'&action=view',1).'" class="no-print">a</a>'; }
						else if($ouinon->idAbsence>0 && $ouinon->isPresence) { $linkPop = '<a title="'.$langs->trans('Show').'" href="'.dol_buildpath('/absence/presence.php?id='.$ouinon->idAbsence.'&action=view',1).'" class="no-print">p</a>'; }
						else $linkPop = '<a title="'.$langs->trans('addAbsenceUser').'" href="javascript:popAddAbsence(\''.$std->get_date('date_jour','Y-m-d').'\', '.$idUser.');" class="no-print">'.$labelJour.'</a>';
					}

					$labelAbs = $ouinon->label;
					if(!empty($ouinon->description)) $labelAbs.=' : '.$ouinon->description;

					if(mb_detect_encoding($labelAbs,'UTF-8', true) === false  ) $labelAbs = utf8_encode($labelAbs);

					if(strpos($toString, 'RTT')!==false) $subclass .= ' rougeRTT';
					else if($ouinon->isPresence)
					{
                        if($ouinon->colorId > 0) $subclass.= ' persocolor'.$ouinon->colorId;
						else $subclass .= ' vert';
						$TTotal[$dateJour]+=1;
					}
					else if($ouinon->colorId > 0) $subclass.= ' persocolor'.$ouinon->colorId;
					else $subclass .= ' rouge';
					$TColor[$ouinon->label] = $subclass;
					if($ouinon->etat == 'Avalider'){
					    $subclass .= ' lighter';
                    }
					if(!empty($class) || !empty($subclass)) $subclass.= ' classfortooltip';

					if($ouinon->colorId > 0) $subclass.= ' persocolor'.$ouinon->colorId;

					$subclass=$class.' '.$subclass;

					// DAM = début congés matin, suite à plusieurs jours consécutifs, donc l'aprem est forcément pris par le meme congés
					if($type == 'DAM')
					{
						$html.= '<td class="'.$subclass.$classTravail.'" title="'.$labelAbs.'" rel="am">'.$linkPop.'</td>
									<td class="'.$subclass.$classTravail.'" title="'.$labelAbs.'" rel="pm">'.$linkPop.'</td>';
					}
					// DPM = début congés apres midi, si je pose 3 jours à partir d'un lundi après midi, il est possible de pose la matinée avec un autre type absence
					else if($type == 'DPM')
					{
						if ($countPresence == 2)
						{
							$html.= '<td class="'.$subclass.$classTravail.'" title="'.$labelAbs.'" rel="pm">'.$linkPop.'</td>';
						}
						else
						{
							$html.= '<td class="vert'.$classTravail.'" rel="am">&nbsp;</td>
									<td class="'.$subclass.$classTravail.'" title="'.$labelAbs.'" rel="pm">'.$linkPop.'</td>';
						}
					}
					// FAM = fin congés matin, si plusieurs jours consécutifs, il est possible de pose l'apres midi avec un autre type absence
					else if($type == 'FAM')
					{
						if ($countPresence == 2)
						{
							$html.= '<td class="'.$subclass.$classTravail.'"  title="'.$labelAbs.'" rel="am">'.$linkPop.'</td>';
						}
						else
						{
							$html.= '<td class="'.$subclass.$classTravail.'"  title="'.$labelAbs.'" rel="am">'.$linkPop.'</td>
									<td class="vert'.$classTravail.'"  rel="pm">&nbsp;</td>';
						}
					}
					// FPM = fin congés apres midi, suite à plusieurs jours consécutifs, donc le matin est forcément pris par le meme congés
					else if($type == 'FPM')
					{
						$html.= '<td class="'.$subclass.$classTravail.'" title="'.$labelAbs.'" rel="am">'.$linkPop.'</td>
									<td class="'.$subclass.$classTravail.'" title="'.$labelAbs.'" rel="pm">'.$linkPop.'</td>';
					}
					else if($type == 'AM')
					{
						// 2 = 2 types de congés le meme jour
						if ($countPresence == 2)
						{
							$html.= '<td class="'.$subclass.$classTravail.'"  title="'.$labelAbs.'" rel="am">'.$linkPop.'</td>';
						}
						else
						{
							$html.= '<td class="'.$subclass.$classTravail.'"  title="'.$labelAbs.'" rel="am">'.$linkPop.'</td>
									<td class="vert'.$classTravail.'"  rel="pm">&nbsp;</td>';
						}
					}
					else if($type == 'PM')
					{
						// 2 = 2 types de congés le meme jour
						if ($countPresence == 2)
						{
							$html.= '<td class="'.$subclass.$classTravail.'" title="'.$labelAbs.'" rel="pm">'.$linkPop.'</td>';
						}
						else
						{
							$html.= '<td class="vert'.$classTravail.'" rel="am">&nbsp;</td>
									<td class="'.$subclass.$classTravail.'" title="'.$labelAbs.'" rel="pm">'.$linkPop.'</td>';
						}
					}
					else
					{
						$html.= '<td class="'.$subclass.$classTravail.'" title="'.$labelAbs.'" rel="am">'.$linkPop.'</td>
									<td class="'.$subclass.$classTravail.'"  title="'.$labelAbs.'" rel="pm">'.$linkPop.'</td>';
					}
				}
			}
		}
		$html .=  "</tr>";
	}

	$html .=  '<tr class="footer"><td>'.$langs->trans('TotalPresent').'</td>';
	foreach($TTotal as $date=>$nb) {
		$html .=  '<td align="center" colspan="2">'.$nb.'</td>';
	}

	$html .=  '</tr></table>';
    if(! empty($TColor)) {
        $html .= '&nbsp;<fieldset style="width: 300px; height: max-content;">';
        $html .= '<legend>'.$langs->trans('Legend').'</legend>';

        foreach($TColor as $label => $colorClass) {
            $html .= '<span class="'.$colorClass.'" style="height: 15px;width: 15px;border-radius: 50%;display: inline-block;"></span>';
            $html .= '&nbsp;'.$label.'<br/>';
            $html .= '<span class="'.$colorClass.' lighter" style="height: 15px;width: 15px;border-radius: 50%;display: inline-block;"></span>';
            $html .= '&nbsp;'.$label.'&nbsp;'.$langs->trans('tovalid').'<br/>';
        }
        $html .= '<span class="jourTravailleNON" style="height: 15px;width: 15px;border-radius: 50%;display: inline-block;"></span>';
        $html .= '&nbsp;'.$langs->trans('NoWorkedDays').'<br/>';
        $html .= '<span class="jourFerie" style="height: 15px;width: 15px;border-radius: 50%;display: inline-block;"></span>';
        $html .= '&nbsp;'.$langs->trans('PublicHoliday');
        $html .= '</fieldset>';
    }
    $html .= '</div>';
	$html .= '<p>&nbsp;</p>';

	if (is_object($hookmanager))
	{
		$hookmanager->initHooks(array('absenceInterface'));

		$parameters = array(
			'html' => $html
			,'TTotal' => $TTotal
			,'TCacheUser' => $TCacheUser
			,'tabUserMisEnForme' =>$tabUserMisEnForme
			,'TPlanningUser' => $TPlanningUser
			,'TJourTrans' => $TJourTrans
			,'isValideur' => $isValideur
			,'idGroupeRecherche'=> $idGroupeRecherche
		);
// Note that $action and $object may be modified by some hooks
		$reshook = $hookmanager->executeHooks('getPlanningAbsence', $parameters, $object, $action);
		if (empty($reshook)) $html.= $hookmanager->resPrint;
		else if ($reshook > 0) $html = $hookmanager->resPrint;
	}

	return $html;
}


/**
 *  Show tab footer of a card.
 *  Note: $object->next_prev_filter can be set to restrict select to find next or previous record by $form->showrefnav.
 *
 *  @param	Object	$object			Object to show
 *  @param	string	$paramid   		Name of parameter to use to name the id into the URL next/previous link
 *  @param	string	$morehtml  		More html content to output just before the nav bar
 *  @param	int		$shownav	  	Show Condition (navigation is shown if value is 1)
 *  @param	string	$fieldid   		Nom du champ en base a utiliser pour select next et previous (we make the select max and min on this field). Use 'none' for no prev/next search.
 *  @param	string	$fieldref   	Nom du champ objet ref (object->ref) a utiliser pour select next et previous
 *  @param	string	$morehtmlref  	More html to show after ref
 *  @param	string	$moreparam  	More param to add in nav link url.
 *	@param	int		$nodbprefix		Do not include DB prefix to forge table name
 *	@param	string	$morehtmlleft	More html code to show before ref
 *	@param	string	$morehtmlstatus	More html code to show under navigation arrows
 *  @param  int     $onlybanner     Put this to 1, if the card will contains only a banner (this add css 'arearefnobottom' on div)
 *	@param	string	$morehtmlright	More html code to show before navigation arrows
 *  @return	void
 */
function dol_absence_banner_tab($object, $paramid, $morehtml='', $shownav=1, $fieldid='rowid', $fieldref='ref', $morehtmlref='', $moreparam='', $nodbprefix=0, $morehtmlleft='', $morehtmlstatus='', $onlybanner=0, $morehtmlright='')
{
    global $conf, $form, $user, $langs, $db;

    $error = 0;

    $maxvisiblephotos=1;
    $showimage=1;
    $entity=(empty($object->entity)?$conf->entity:$object->entity);
    $showbarcode=empty($conf->barcode->enabled)?0:($object->barcode?1:0);
    if (! empty($conf->global->MAIN_USE_ADVANCED_PERMS) && empty($user->rights->barcode->lire_advance)) $showbarcode=0;
    $modulepart='unknown';



    if (class_exists("Imagick"))
    {
        if ($object->element == 'propal')            $modulepart='propal';
        if ($object->element == 'commande')          $modulepart='commande';
        if ($object->element == 'facture')           $modulepart='facture';
        if ($object->element == 'fichinter')         $modulepart='ficheinter';
        if ($object->element == 'contrat')           $modulepart='contract';
        if ($object->element == 'supplier_proposal') $modulepart='supplier_proposal';
        if ($object->element == 'order_supplier')    $modulepart='supplier_order';
        if ($object->element == 'invoice_supplier')  $modulepart='supplier_invoice';
        if ($object->element == 'expensereport')     $modulepart='expensereport';
    }

    $width='80'; $cssclass='photoref';
    $nophoto=dol_buildpath('absence/img/absenceOLD.png',1);
    $morehtmlleft.='<div class="floatleft inline-block valignmiddle divphotoref"><img class="photo'.$modulepart.($cssclass?' '.$cssclass:'').'" alt="No photo" border="0"'.($width?' width="'.$width.'"':'').' src="'.$nophoto.'"></div>';





    if ($showbarcode) $morehtmlleft.='<div class="floatleft inline-block valignmiddle divphotoref">'.$form->showbarcode($object).'</div>';



    //$morehtmlstatus.=$langs->trans("Status").' ('.$langs->trans("Buy").') ';
    if (! empty($conf->use_javascript_ajax) && $user->rights->produit->creer && ! empty($conf->global->MAIN_DIRECT_STATUS_UPDATE)) {
        $morehtmlstatus.=ajax_object_onoff($object, 'status_buy', 'tobuy', 'ProductStatusOnBuy', 'ProductStatusNotOnBuy');
    } else {
        $morehtmlstatus.='<span class="statusrefbuy">'.$object->getLibStatut(5,1).'</span>';
    }


    if($object->fk_user>0){
        $absUser = new User($db);
        if($absUser->fetch($object->fk_user)){
            $morehtmlref.= ' - '.$absUser->getFullName($langs);
        }
    }


    $morehtmlref.='<div class="refidno">'.dol_print_date($object->date_debut).' '.$object->TddMoment[$object->ddMoment].' - '.dol_print_date($object->date_fin).' '.$object->TddMoment[$object->dfMoment].'</div>';
    $morehtmlref.='<div class="refidno">'.round2Virgule($object->duree).' '.($object->duree>1?$langs->trans('Days'):$langs->trans('Day')).'</div>';

    if (! empty($conf->global->MAIN_SHOW_TECHNICAL_ID))
    {
        $morehtmlref.='<div style="clear: both;"></div><div class="refidno">';
        $morehtmlref.=$langs->trans("TechnicalID").': '.$object->id;
        $morehtmlref.='</div>';
    }

    print '<div class="'.($onlybanner?'arearefnobottom ':'arearef ').'heightref valignmiddle" width="100%">';
    print $form->showrefnav($object, $paramid, $morehtml, $shownav, $fieldid, $fieldref, $morehtmlref, $moreparam, $nodbprefix, $morehtmlleft, $morehtmlstatus, $morehtmlright);
    print '</div>';
    print '<div class="underrefbanner clearboth"></div>';
}


function saveAbsence(TPDOdb &$PDOdb, TRH_Absence &$absence)
{
	global $langs, $user, $conf;

	$absence->load($PDOdb, $_REQUEST['id']);
	$absence->set_values($_REQUEST);

	$absence->set_date('date_debut', GETPOST('date_debutday').'/'.GETPOST('date_debutmonth').'/'.GETPOST('date_debutyear'));
	$absence->set_date('date_fin', GETPOST('date_finday').'/'.GETPOST('date_finmonth').'/'.GETPOST('date_finyear'));

	// prise en charge des présences d'unité "heure"
	$ta = new TRH_TypeAbsence;
	$ta->load_by_type($PDOdb, $absence->type);
	if($ta->isPresence) {
		if($ta->unite == 'heure') {
			require_once DOL_DOCUMENT_ROOT."/core/lib/date.lib.php";

			$dureeSingle = GETPOST('dureeSingle');

			$TDuree = explode(":", $dureeSingle);
			$operateur = '+';
			if(intval($TDuree[0]) < 0 || strpos($TDuree[0], '-') !== false) $operateur = '-';
			$dureeSingle = convertTime2Seconds(abs(intval($TDuree[0])), intval($TDuree[1]), 0) / 60;
			if($operateur == '-') $absence->set_date('date_hourStart', date("Y-m-d 23:59:59", strtotime($absence->date_hourStart)));
			$absence->set_date('date_hourEnd', date("Y-m-d H:i:s", strtotime($operateur.$dureeSingle.' minutes', $absence->date_hourStart)));

//		var_dump($operateur.$dureeSingle, date("Y-m-d H:i", $absence->date_hourStart), date("Y-m-d H:i", $absence->date_hourEnd)); return false;
		}
		else if(!empty($conf->global->ABSENCE_SHOW_PRESENCE_BY_PERIOD)) {
			$hourMorningStart = GETPOST('hourStartMorning');
			$hourMorningEnd = GETPOST('hourEndMorning');
			$hourAfternoonStart = GETPOST('hourStartAfternoon');
			$hourAfternoonEnd = GETPOST('hourEndAfternoon');
			if(! empty($hourMorningStart)) $absence->set_date('date_hourStart', date('Y-m-d '.$hourMorningStart.':s', $absence->date_hourStart));
			else  $absence->set_date('date_hourStart', date('Y-m-d 00:00:s', $absence->date_hourStart));
			if(! empty($hourMorningEnd)) $absence->set_date('date_hourMorningEnd', date('Y-m-d '.$hourMorningEnd.':s', $absence->date_hourStart));
			else $absence->set_date('date_hourMorningEnd', date('Y-m-d 00:00:s', $absence->date_hourStart));
			if(! empty($hourAfternoonStart)) $absence->set_date('date_hourAfternoonStart', date('Y-m-d '.$hourAfternoonStart.':s', $absence->date_hourEnd));
			else $absence->set_date('date_hourAfternoonStart', date('Y-m-d 00:00:s', $absence->date_hourEnd));
			if(! empty($hourAfternoonEnd)) $absence->set_date('date_hourEnd', date('Y-m-d '.$hourAfternoonEnd.':s', $absence->date_hourEnd));
			else $absence->set_date('date_hourEnd', date('Y-m-d 00:00:s', $absence->date_hourEnd));

		}
	}



	$absence->niveauValidation = 1;
	$existeDeja = $absence->testExisteDeja($PDOdb, $absence);

	if(! empty($existeDeja))
	{
		$absence->error = $langs->trans('ImpossibleCreation') . ' : ' . $langs->trans('ErrExistingRequestInPeriod', date('d/m/Y', strtotime($existeDeja[0])), date('d/m/Y',  strtotime($existeDeja[1])));
		return false;
	}

	$absence->code = saveCodeTypeAbsence($PDOdb, $absence->type);

	// Test de la cohérence des dates
	if(empty($user->rights->absence->myactions->declarePastAbsence) && ($absence->date_debut <= strtotime('midnight') || $absence->date_fin <= strtotime('midnight') )) {

		// Ok le mec n'a pas le droit de créer une absence dans le passé mais est-ce qu'il peut le jour même
		if ($user->rights->absence->myactions->declareToDayAbsence && $absence->date_debut >= strtotime('midnight') && $absence->date_fin >= strtotime('midnight'))
		{
			// RAS il peut créer l'absence le jour même
		}
		else
		{
			/*
				Si ce n'est pas un user avec droit, pas le droit de créer des anciennes absences
			*/
			$absence->error = $langs->trans('ErrOnlyUserWithPowerCanCreatePastAbsence');
			return false;
		}
	}

	$saveReturn = $absence->save($PDOdb);
	if (empty($saveReturn))
	{
		$absence->error = implode('<br />', $absence->errors);
		return false;
	}
	$absence->load($PDOdb, $_REQUEST['id']);
	// Submit file
	$TPieceJointe = array();
	if (! empty($_FILES['userfile']['name']))
	{
		$TPieceJointe = $_FILES['userfile']['name'];
	}

	// Quand l'input est vide $_FILES n'est pas vide ce qui crée une erreur
	if (!empty($_FILES['userfile']['name']) && count($_FILES['userfile']['name']) == 1 && empty($_FILES['userfile']['name'][0]))
	{
		unset($_FILES['userfile']['name']);
	}

	if (!empty($TPieceJointe)) $res = dol_add_file_process($conf->absence->dir_output.'/'.dol_sanitizeFileName($absence->getId()), 0, 1, 'userfile', '');

	if (GETPOST('autoValidatedAbsence') > 0)
	{
		$absence->setAcceptee($PDOdb, $user->id, false, $TPieceJointe);
	}
	// on vérifie si l'absence a été créée par l'user avant d'envoyer un mail
	elseif ($absence->fk_user == $user->id)
	{
		mailConges($absence, false, $TPieceJointe);
		mailCongesValideur($PDOdb,$absence, false, $TPieceJointe);
	}

	return true;
}

