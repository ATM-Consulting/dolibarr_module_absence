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
				array(dol_buildpath('/absence/compteur.php?action=view&fk_user='.$fk_user,1), $langs->trans('CounterOf') . ' ' . $nomUser . ' ' . $prenomUser, 'compteur')
				,array(dol_buildpath('/absence/compteur.php?action=log&fk_user='.$fk_user,1), $langs->trans('Log'), 'log')
				
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
function mailConges(&$absence,$presence=false){
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
					,'commentaireValideur'=>utf8_encode($absence->commentaireValideur)
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
function mailCongesValideur(&$PDOdb, &$absence,$presence=false){
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
			envoieMailValideur($PDOdb, $absence, $idVal,$presence);
		}
	}
	
}


//fonction permettant l'envoi de mail aux valideurs de la demande d'absence
function envoieMailValideur(&$PDOdb, &$absence, $idValideur,$presence=false){
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
		return 0;
	}
	
}

function _recap_abs(&$PDOdb, $idGroupeRecherche, $idUserRecherche, $date_debut, $date_fin) {
	global $db, $langs;	
	
	if(empty($date_debut)) return false;

	$date_debut = date('Y-m-d', Tools::get_time($date_debut));
	$date_fin = date('Y-m-d', Tools::get_time($date_fin));
	
	$TStatPlanning = TRH_Absence::getPlanning($PDOdb, $idGroupeRecherche, $idUserRecherche, $date_debut, $date_fin);
//var_dump($TStatPlanning);
	$first=true;

	if(empty($TStatPlanning)) return false;

	$html = '<table class="planning" border="0">';
	$html .= "<tr class=\"entete\">";

	$html .= '<tr>
				<td>' . $langs->trans('Name') . '</td>
				<td>' . $langs->trans('PresenceDay') . '</td>
				<td>' . $langs->trans('PresenceHour') . '</td>
				<td>' . $langs->trans('AbsenceDay') . '</td>
				<td>' . $langs->trans('AbsenceHour') . '</td>
				<td>' . $langs->trans('Presence') . ' + ' . $langs->trans('PublicHolidayDay') . '</td>
				<td>' . $langs->trans('Absence') . ' + ' . $langs->trans('PublicHolidayDay') . '</td>
				<td>' . $langs->trans('PublicHolidayDay') . '</td>
				
				
			</tr>';

	foreach($TStatPlanning as $idUser=>$TStat) {
		$u=new User($db);
		$u->fetch($idUser);
		
		$stat=array();
		
		foreach($TStat as $date=>$row) {

			@$stat['presence']+=$row['nb_jour_presence'];
			@$stat['presence_heure']+=$row['nb_heure_presence'];
			@$stat['absence']+=$row['nb_jour_absence'];
			@$stat['absence_heure']+=$row['nb_heure_absence'];
			@$stat['presence+ferie']+=$row['nb_jour_presence'] + $row['nb_jour_ferie'];
			@$stat['absence+ferie']+=$row['nb_jour_absence'] + $row['nb_jour_ferie'] ;
			@$stat['ferie']+=$row['nb_jour_ferie'] ;
		}
		
		if(empty($u->lastname)) $u->lastname = $u->login;
		
		$html .= '<tr><td style="text-align:left;">'.$u->getNomUrl().'</td>';
		
		$html .= '<td>'.$stat['presence'].'</td>';
		$html .= '<td>'.$stat['presence_heure'].'</td>';
		$html .= '<td>'.$stat['absence'].'</td>';
		$html .= '<td>'.$stat['absence_heure'].'</td>';
		$html .= '<td>'.$stat['presence+ferie'].'</td>';
		$html .= '<td>'.$stat['absence+ferie'].'</td>';
		$html .= '<td>'.$stat['ferie'].'</td></tr>';
		
		
	}
	

	$html .= '</table><p>&nbsp;</p>';

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
				$date_debut =date('d/m/Y', $absence->date_debut_planning);	
			}
			else {
				$date_debut =date('01/m/Y', $t_current);	
			}
			
			$t_fin_periode= strtotime(date('Y-m-t',  $t_current));
			
			if($t_fin_periode>=$absence->date_fin_planning) {
				$date_fin =date('d/m/Y', $absence->date_fin_planning);	
			}
			else {
				$date_fin =date('d/m/Y', $t_fin_periode);	
			}
			
			if($annee!=$annee_old) $html.= '<p style="text-align:left;font-weight:bold">'.$annee.'</strong><br />';

			$html.= _planning($PDOdb, $absence, $idGroupeRecherche, $idUserRecherche, $date_debut, $date_fin );
		
			$annee_old = $annee;
		
			
			$t_current=strtotime('+1 month', $t_current);
		}

		if($user->rights->absence->myactions->creerAbsenceCollaborateur) $html.= _recap_abs($PDOdb, $idGroupeRecherche, $idUserRecherche, date('d/m/Y',$absence->date_debut_planning), date('d/m/Y',$absence->date_fin_planning));
		
		return $html;
	
}

function _getSQLListValidation($userid) {
	// TODO AA encore une grosse merde bien collante sous la semelle

	global $db, $conf,$user;
		
 	//LISTE DES GROUPES À VALIDER
 	$sql=" SELECT DISTINCT fk_usergroup, nbjours, validate_himself, level
 			FROM `".MAIN_DB_PREFIX."rh_valideur_groupe`
			WHERE fk_user=".$userid." 
			AND type='Conges' AND pointeur !=1 ";
			//AND entity IN (0,".$conf->entity.")";

	$res = $db->query($sql);
	$TabGroupe=array();
	$k=0;
	while($obj = $db->fetch_object($res)) {
				$TabGroupe[$k]['fk_usergroup']=$obj->fk_usergroup;
				$TabGroupe[$k]['nbjours']=$obj->nbjours;
				$TabGroupe[$k]['validate_himself']=$obj->validate_himself;
				$TabGroupe[$k]['level']=$obj->level;
				$k++;
	}
	
	//LISTE USERS À VALIDER
	if($k==1){		//on n'a qu'un groupe de validation
		$sql=" SELECT DISTINCT u.fk_user, 
				a.rowid as 'ID', a.date_cre  as 'DateCre',a.date_debut, a.date_fin, a.duree,
			  	ta.libelleAbsence as libelle,a.fk_user,  s.firstname, s.lastname,
			 	a.etat";
			
				if($conf->multicompany->enabled) $sql.=",e.label as entity";	  	
				
				$sql.= ", a.avertissement, ta.typeAbsence,'' as 'action'
				FROM `".MAIN_DB_PREFIX."rh_valideur_groupe` as v
				INNER JOIN ".MAIN_DB_PREFIX."usergroup_user as u ON ( v.fk_usergroup=u.fk_usergroup )
				INNER JOIN ".MAIN_DB_PREFIX."rh_absence as a ON(a.fk_user = u.fk_user)
				INNER JOIN ".MAIN_DB_PREFIX."user as s ON (u.fk_user=s.rowid)
				INNER JOIN ".MAIN_DB_PREFIX."rh_type_absence as ta ON (ta.typeAbsence=a.type)
				";
			
				if($conf->multicompany->enabled) $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."entity as e ON (e.rowid = a.entity) ";
							
				$sql.= "
				WHERE v.fk_user=".$userid." 
				AND a.etat LIKE 'AValider'
				AND v.fk_usergroup=".$TabGroupe[0]['fk_usergroup'];
				
				if($TabGroupe[0]['level']==1){	//on teste le niveau de validation : si il est de niveau 1, il faut qu'il puisse voir le 2 et 3
					$sql.=" AND ( a.niveauValidation>=1)";
				}else if($TabGroupe[0]['level']==2){
					$sql.=" AND ( a.niveauValidation>=2)";
				}
				else if($TabGroupe[0]['level']==3){
					$sql.=" AND a.niveauValidation>=3";
				}

				
			if($TabGroupe[0]['validate_himself']==0){
				$sql.=" AND u.fk_user NOT IN (SELECT a.fk_user FROM ".MAIN_DB_PREFIX."rh_absence as a where a.fk_user=".$userid.")";
			}

			return $sql;
	}else if($k>1){		//on a plusieurs groupes de validation
		$sql=" SELECT DISTINCT u.fk_user, 
				a.rowid as 'ID', a.date_cre as 'DateCre',a.date_debut, a.date_fin, 
			  	ta.libelleAbsence as libelle,a.fk_user,  s.firstname, s.lastname,
			 	a.etat, a.avertissement";
			
				if($conf->multicompany->enabled) $sql.=",e.label as entity";	  	
				
				$sql.= ", a.duree, ta.typeAbsence,'' as 'action'
				FROM `".MAIN_DB_PREFIX."rh_valideur_groupe` as v
				INNER JOIN ".MAIN_DB_PREFIX."usergroup_user as u ON ( v.fk_usergroup=u.fk_usergroup )
				INNER JOIN ".MAIN_DB_PREFIX."rh_absence as a ON(a.fk_user = u.fk_user)
				INNER JOIN ".MAIN_DB_PREFIX."user as s ON (u.fk_user=s.rowid)
				INNER JOIN ".MAIN_DB_PREFIX."rh_type_absence as ta ON (ta.typeAbsence=a.type)";
			
				if($conf->multicompany->enabled) $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."entity as e ON (e.rowid = a.entity) ";
							
				$sql.= " 
				WHERE v.fk_user=".$userid." 
				AND a.etat LIKE 'AValider'";
 		
 		$j=0;
		foreach($TabGroupe as $TGroupe){ 	//on affiche les absences des différents groupe de validation
			if($j==0){
				if($TabGroupe[$j]['level']==1){	//on teste le niveau de validation  si il est de niveau 1, il faut qu'il puisse voir le 2 et 3
					$sql.=" AND ( (v.fk_usergroup=".$TabGroupe[$j]['fk_usergroup']."
					AND (a.niveauValidation>=1)
					AND NOW() >= ADDDATE(a.date_cre, ".$TabGroupe[$j]['nbjours'].")
					)";
				}else if($TabGroupe[$j]['level']==2){
					$sql.=" AND ( (v.fk_usergroup=".$TabGroupe[$j]['fk_usergroup']."
					AND (a.niveauValidation>=2)
					AND NOW() >= ADDDATE(a.date_cre, ".$TabGroupe[$j]['nbjours'].")
					)";
				}
				else if($TabGroupe[$j]['level']==3){
					$sql.=" AND ( (v.fk_usergroup=".$TabGroupe[$j]['fk_usergroup']."
					AND a.niveauValidation>=3
					AND NOW() >= ADDDATE(a.date_cre, ".$TabGroupe[$j]['nbjours'].")
					)";
				}
				
			}else{
				if($TabGroupe[$j]['level']==1){	//on teste le niveau de validation
					$sql.=" OR ( v.fk_usergroup=".$TabGroupe[$j]['fk_usergroup']."
						AND (a.niveauValidation>=1) 
						AND NOW() >= ADDDATE(a.date_cre, ".$TabGroupe[$j]['nbjours'].")
						)";
				}
				else if($TabGroupe[$j]['level']==2){
					$sql.=" OR ( v.fk_usergroup=".$TabGroupe[$j]['fk_usergroup']."
						AND (a.niveauValidation>=2) 
						AND NOW() >= ADDDATE(a.date_cre, ".$TabGroupe[$j]['nbjours'].")
						)";
				}
				else if($TabGroupe[$j]['level']==3){
					$sql.=" OR ( v.fk_usergroup=".$TabGroupe[$j]['fk_usergroup']."
						AND a.niveauValidation>=3
						AND NOW() >= ADDDATE(a.date_cre, ".$TabGroupe[$j]['nbjours'].")
						)";
				}	
			}
 			
			$j++;
 		}
 		$sql.=")";
		
		return $sql;
	}
	else if($user->rights->absence->myactions->voirToutesAbsencesListe) {
			 $sql=" SELECT DISTINCT a.fk_user,
                    a.rowid as 'ID', a.date_cre as 'DateCre',a.date_debut, a.date_fin,
                    ta.libelleAbsence as libelle,a.fk_user,  s.firstname, s.lastname,
                    a.etat";
							
					if($conf->multicompany->enabled) $sql.=",e.label as entity";	  	
					
					$sql.= ", a.avertissement, a.duree, ta.typeAbsence,'' as 'action'
                    FROM ".MAIN_DB_PREFIX."rh_absence as a
                	INNER JOIN ".MAIN_DB_PREFIX."user as s ON (a.fk_user = s.rowid)
                	INNER JOIN ".MAIN_DB_PREFIX."rh_type_absence as ta ON ( ta.typeAbsence=a.type ) ";

					if($conf->multicompany->enabled) $sql.= " LEFT JOIN ".MAIN_DB_PREFIX."entity as e ON (e.rowid = a.entity) ";
								
					$sql.= "
                    WHERE 1
                    AND a.etat LIKE 'AValider'";

		return $sql;
	}
 	else {
		return false;
	}
 
	
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
	global $langs,$user,$db;
	
	dol_include_once('/valideur/class/valideur.class.php');
	
//on va obtenir la requête correspondant à la recherche désirée
	// Test si somme des trois groupes = (99999 * 3) Tous les select sur Aucun alors recherche vide
	if(array_sum($idGroupeRecherche) == 299997)$idGroupeRecherche = array('0'=>0); //TODO mais c'est quoi cette merde ?!
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
	//var_dump($tabUserMisEnForme);
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
						$subclass .= ' vert';
						$TTotal[$dateJour]+=1;
					}
					else $subclass .= ' rouge';
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
	
	$html .=  '</tr></table><p>&nbsp;</p>';
	
	return $html;
}
