<?php
	require('config.php');
	require('./class/absence.class.php');
	require('./lib/absence.lib.php');

	$langs->load('absence@absence');

	$PDOdb=new TPDOdb;
	$emploiTemps=new TRH_EmploiTemps;

	if(isset($_REQUEST['action'])) {
		switch($_REQUEST['action']) {

            case 'save_to_group':

                $fk_group =(int)GETPOST('fk_group');
                $fk_user = (int)GETPOST('fk_user');

                if($fk_group && $fk_user && !empty($user->rights->absence->myactions->CanChangeEmploiTempsForGroup)) {

                    $emploiTemps->loadByuser($PDOdb, $fk_user);

                    require_once DOL_DOCUMENT_ROOT . '/user/class/usergroup.class.php';
                    $group = new UserGroup($db);
                    $group->fetch($fk_group);
                    $TUser = $group->listUsersForGroup('',1);

                    foreach($TUser as $fk_user_in_group) {

                        if($fk_user_in_group == $fk_user) continue;

                        $e2 = new TRH_EmploiTemps;
                        $e2->loadByuser($PDOdb, $fk_user_in_group);

                        foreach ($e2->TJour as $jour) {
                            $e2->{'date_'.$jour."_heuredam"}=$emploiTemps->{'date_'.$jour."_heuredam"};
                            $e2->{'date_'.$jour."_heuredpm"}=$emploiTemps->{'date_'.$jour."_heuredpm"};

                            $e2->{'date_'.$jour."_heurefpm"}=$emploiTemps->{'date_'.$jour."_heurefpm"};
                            $e2->{'date_'.$jour."_heurefpm"}=$emploiTemps->{'date_'.$jour."_heurefpm"};

                            $e2->{$jour.'am'} = $emploiTemps->{$jour.'am'};
                            $e2->{$jour.'pm'} = $emploiTemps->{$jour.'pm'};

                        }

                        $e2->save($PDOdb);

                    }



                    setEventMessage($langs->trans('GroupScheduleApplied'));
                    _fiche($PDOdb, $emploiTemps,'view');

                }

                break;

			case 'edit'	:
				$emploiTemps->load($PDOdb, $_REQUEST['id']);
				_fiche($PDOdb, $emploiTemps,'edit');
				break;

			case 'save':
				//$PDOdb->db->debug=true;
				$emploiTemps->load($PDOdb, $_REQUEST['id']);

				$emploiTemps->razCheckbox($PDOdb, $emploiTemps);

				$emploiTemps->set_values($_REQUEST);

				$emploiTemps->tempsHebdo=$emploiTemps->calculTempsHebdo($PDOdb, $emploiTemps);

				$newId = $emploiTemps->save($PDOdb);

				if($newId>0){
				    header("Location: ".dol_buildpath('/absence/emploitemps.php', 1).'?action=view&id='.$newId);
				    setEventMessage($langs->trans('TimeTableModified'), 'mesgs');
				    exit();
				}
				else{
				    $mesg = '<div class="ok">' . $langs->trans('RegistedRequest') . '</div>';
				    _fiche($PDOdb, $emploiTemps,'view');
				}

				$mesg = '<div class="ok">' . $langs->trans('RegistedRequest') . '</div>';
				_fiche($PDOdb, $emploiTemps,'view');
				break;
			case 'archive':
				if(GETPOST('id','int')>0) $emploiTemps->load($PDOdb, GETPOST('id','int'));
				else $emploiTemps->loadByuser($PDOdb, GETPOST('fk_user','int'));

				$emploiTempsArchive = clone $emploiTemps;

				$PDOdb->Execute("SELECT MAX(date_fin) as date_fin
					FROM ".MAIN_DB_PREFIX."rh_absence_emploitemps WHERE fk_user=".$emploiTemps->fk_user." AND is_archive=1");
				$row = $PDOdb->Get_line();
				if($row) {
					$emploiTempsArchive->date_debut = strtotime($row->date_fin);
				}
				$emploiTempsArchive->date_fin = time();

				$emploiTempsArchive->rowid=0;
				$emploiTempsArchive->is_archive=1;

				// check planning override
				$sql = "SELECT COUNT(*) as count FROM ".MAIN_DB_PREFIX."rh_absence_emploitemps
                        WHERE fk_user=".$emploiTempsArchive->fk_user."  AND is_archive=1
                                AND date_debut < '".date('Y-m-d 00:00:00',$emploiTempsArchive->date_fin)."'
                                AND date_fin > '".date('Y-m-d 00:00:00',$emploiTempsArchive->date_debut)."'";
				$PDOdb->Execute($sql);
				$row = $PDOdb->Get_line();
				//var_dump($sql);
				if($row && $row->count > 0 ) {
				    setEventMessage($langs->trans('ArchiveErrorPlanningRangeAllreadyUsed'),'warnings');
				}
				else {

				    $newId = $emploiTempsArchive->save($PDOdb);
				    setEventMessage($langs->trans('ArchivedSchedule'));
				}

				_fiche($PDOdb, $emploiTemps,'view');

				break;

			case 'copytoNew':


			    if(GETPOST('id','int')>0) $emploiTemps->load($PDOdb, GETPOST('id','int'));
			    else $emploiTemps->loadByuser($PDOdb, GETPOST('fk_user','int'));

			    $emploiTempsArchive = clone $emploiTemps;


			    $date_debut = GETPOST('date_debut');
			    $date_fin = GETPOST('date_fin');

			    if(empty($date_debut) || empty($date_fin))
			    {
			        $PDOdb->Execute("SELECT MAX(date_fin) as date_fin
					FROM ".MAIN_DB_PREFIX."rh_absence_emploitemps WHERE fk_user=".$emploiTemps->fk_user." AND is_archive=1");

			        $emploiTempsArchive->date_debut = time();
			        $emploiTempsArchive->date_fin = time() + 3600 * 24 * 7;
			        if($row) {
			            $emploiTempsArchive->date_debut = strtotime($row->date_fin);
			            $emploiTempsArchive->date_fin = $date_debut + 3600 * 24 * 7;
			            if($emploiTempsArchive->date_fin < time ())
			            {
			                $emploiTempsArchive->date_fin = time();
			            }
			        }

			        setEventMessage($langs->trans('copytoNewDateWarning'), 'warnings');
			    }
			    else{
			        $emploiTempsArchive->date_debut = strtotime($date_debut);
			        $emploiTempsArchive->date_fin = strtotime($date_fin);
			    }





			    $emploiTempsArchive->rowid=0;
			    $emploiTempsArchive->is_archive=1;

			    $newId = $emploiTempsArchive->save($PDOdb);

			    header("Location: ".dol_buildpath('/absence/emploitemps.php', 1).'?action=edit&id='.$newId);
			    exit;

			case 'deleteArchive':

				$emploiTempsArchive=new TRH_EmploiTemps;
				$emploiTempsArchive->load($PDOdb, GETPOST('idArchive','int'));
				$emploiTempsArchive->delete($PDOdb);

				setEventMessage($langs->trans('ScheduleArchiveDeleted'));

				if(GETPOST('id','int')>0) $emploiTemps->load($PDOdb, GETPOST('id','int'));
				else $emploiTemps->loadByuser($PDOdb, GETPOST('fk_user','int'));
				_fiche($PDOdb, $emploiTemps,'view');

				break;

			case 'view':
					if(GETPOST('id','int')>0) $emploiTemps->load($PDOdb, GETPOST('id','int'));
					else $emploiTemps->loadByuser($PDOdb, GETPOST('fk_user','int'));
					_fiche($PDOdb, $emploiTemps,'view');
				break;

		}
	}
	elseif(isset($_REQUEST['id'])) {

	}
	else {
		if($user->rights->absence->myactions->voirTousEdt){
			$emploiTemps->loadByuser($PDOdb, GETPOST('fk_user','int'));
			_liste($PDOdb, $emploiTemps);
		}else{

			$emploiTemps->loadByuser($PDOdb, $user->id);
			_fiche($PDOdb, $emploiTemps,'view');
		}

	}

	$PDOdb->close();
	llxFooter();


function _liste(&$PDOdb, &$emploiTemps) {
	global $langs, $conf, $db, $user;
	llxHeader('', $langs->trans('ListOfAbsence'));
	getStandartJS();
	print dol_get_fiche_head(edtPrepareHead($emploiTemps, 'emploitemps')  , 'emploitemps', $langs->trans('Absence'));

	$r = new TSSRenderControler($emploiTemps);
	$sql="SELECT DISTINCT e.rowid as 'ID', e.date_cre as 'DateCre',
	 e.fk_user as 'Id Utilisateur', '' as 'Emploi du temps', u.login
	,u.rowid as 'fk_user',u.firstname, u.lastname
		FROM ".MAIN_DB_PREFIX."rh_absence_emploitemps as e INNER JOIN ".MAIN_DB_PREFIX."user as u ON (u.rowid=e.fk_user)
		WHERE 1 AND e.is_archive=0 AND u.statut = 1 ";

	if($user->rights->absence->myactions->voirTousEdt!="1"){
		$sql.=" AND e.fk_user=".$user->id;
	}
	$form=new TFormCore($_SERVER['PHP_SELF'],'formtranslateList','GET');
	$TOrder = array('lastname'=>'ASC');
	if(isset($_REQUEST['orderDown']))$TOrder = array($_REQUEST['orderDown']=>'DESC');
	if(isset($_REQUEST['orderUp']))$TOrder = array($_REQUEST['orderUp']=>'ASC');

	$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1;
	$r->liste($PDOdb, $sql, array(
		'limit'=>array(
			'page'=>$page
			,'nbLine'=>'30'
		)
		,'link'=>array(
			'ID'=>'<a href="?id=@ID@&action=view&fk_user='.$user->id.'">@val@</a>'
			, $langs->trans('Schedule')=>'<a href="?id=@ID@&action=view&fk_user='.$user->id.'"<a>' . $langs->trans('Schedule') . '</a>'
		)
		,'title'=>array(
			'firstname'=> $langs->trans('FirstName')
			,'lastname'=> $langs->trans('LastName')
			,'login'=> $langs->trans('Login')
		)
		,'translate'=>array()
		,'hide'=>array('DateCre','ID', 'Id Utilisateur','fk_user')
		,'type'=>array()
		,'liste'=>array(
			'titre'=> $langs->trans('CollabScheduleList')
			,'image'=>img_picto('','title.png', '', 0)
			,'picto_precedent'=>img_picto('','previous.png', '', 0)
			,'picto_suivant'=>img_picto('','next.png', '', 0)
			,'noheader'=> (int)isset($_REQUEST['socid'])
			,'messageNothing'=> $langs->trans('NoScheduleToShow')
			,'order_down'=>img_picto('','1downarrow.png', '', 0)
			,'order_up'=>img_picto('','1uparrow.png', '', 0)

		)
		,'orderBy'=>$TOrder
		,'search'=>array(
			'firstname'=>true
			,'lastname'=>true
			,'login'=>true
		)
		,'eval'=>array(
				'lastname'=>'_getNomUrl(@fk_user@, "@val@")'
				//,'firstname'=>'htmlentities("@val@", ENT_COMPAT , "ISO8859-1")' // [PH] Je sais j'ai encore mis quelque chose en commentaire MAIS c'est pcq'on a plus besoin de convertir en ISO depuis la maj d'abricot pour l'encodage en utf-8
		)

	));
	$form->end();
	llxFooter();
}
function _getNomUrl($fk_user,$nom) {
global $db;
	$user=new User($db);

	$user->id = $fk_user;
	$user->lastname=$nom;

	return $user->getNomUrl(1);
}

function _fiche(&$PDOdb, &$emploiTemps, $mode) {
	global $db, $user,$idUserCompt, $idComptEnCours,$conf, $langs,$mysoc;
    $id = GETPOST('id', 'int');
	llxHeader('', $langs->trans('Schedule'));
	$emploiTemps->load($PDOdb, $id);
	$form=new TFormCore($_SERVER['PHP_SELF'],'form1','POST');
	$form->Set_typeaff($mode);
	echo $form->hidden('id', $id);
	echo $form->hidden('action', 'save');
	echo $form->hidden('fk_user', $emploiTemps->fk_user);


	$userCourant=new User($db);
	$userCourant->fetch($emploiTemps->fk_user);

	$TPlanning=array();
	foreach($emploiTemps->TJour as $jour) {
		foreach(array('am','pm') as $pm) {
			$TPlanning[$jour.$pm]=$form->checkbox1('',$jour.$pm,'1',$emploiTemps->{$jour.$pm}==1?true:false);
		}
	}

	$TJoursTempsPartiel=array();
	foreach($emploiTemps->TJour as $jour) {
		$TJoursTempsPartiel[$jour.'_is_tempspartiel']=$form->checkbox1('',$jour.'_is_tempspartiel','1',$emploiTemps->{$jour.'_is_tempspartiel'}==1?true:false);
	}

	$THoraire=array();
	foreach($emploiTemps->TJour as $jour) {
		foreach(array('dam','fam','dpm','fpm') as $pm) {
			$pm2 = strpos($pm ,'pm') !==false ? 'pm' : 'am';
			$THoraire[$jour.'_heure'.$pm]=($emploiTemps->{$jour.$pm2} || $mode =='edit')  ?
				$form->timepicker('','date_'.$jour.'_heure'.$pm, empty($emploiTemps->{$jour.$pm2})?'':date('H:i',$emploiTemps->{'date_'.$jour.'_heure'.$pm}) ,5,5,'','text','H:i','00:00', $maxTime = '24:00')
				: ' - ';
		}
	}

	$TEntity=array();
	$TEntity=$emploiTemps->load_entities($PDOdb);

	$r=new TListviewTBS('listArchive');
	$listeArchive = $r->render($PDOdb, "SELECT
	 	rowid as ID, date_debut,date_fin,tempsHebdo, '' as 'Actions'
	 FROM ".MAIN_DB_PREFIX."rh_absence_emploitemps
	 WHERE fk_user=".$userCourant->id." AND is_archive=1 ORDER BY date_debut DESC",array(

	 	'type'=>array('date_debut'=>'date', 'date_fin'=>'date')
		,'translate'=>array(
			'date_debut'=>array('30/11/-0001'=>'-')
			,'date_fin'=>array('30/11/-0001'=>'-')
		)
		,'link'=>array(
		    'ID'=>'<a href="?id=@ID@&action=view">@val@</a>',
			'Actions'=>'
			<a href="?id=@ID@&action=view">' . $langs->trans('View') . '</a> &nbsp;  &nbsp;
			<a href="?id=@ID@&action=edit">' . $langs->trans('Update') . '</a> &nbsp;  &nbsp;
			<a href="?id='.$emploiTemps->getId().'&idArchive=@ID@&action=deleteArchive">' . $langs->trans('Delete') . '</a>'
		)
		,'title'=>array(
			'date_debut'=> $langs->trans('StartDate')
			,'date_fin'=> $langs->trans('EndDate')
			,'tempsHebdo'=> $langs->trans('WeeklyWorkingTimeInHour')
		)
	    ,'liste' => array(
	         'titre' => $langs->trans('PlanningListByPeriod'),
	     )
	 ));

	$TEmploiTemps = $emploiTemps->get_values();

	$TEmploiTemps['date_debut'] = $form->calendrier('', 'date_debut', $emploiTemps->get_date('date_debut')  );
	$TEmploiTemps['date_fin'] = $form->calendrier('', 'date_fin',  $emploiTemps->get_date('date_fin'));

	if($user->rights->absence->myactions->modifierEdt || ($user->rights->absence->myactions->modifierEdtByHierarchy && _userCanModifyEdt($emploiTemps->fk_user)))	$can_modify_edt = 1;


	// to return on default planning
	$defaultEmploiTemps = new TRH_EmploiTemps();
	$defaultEmploiTemps->load_by_fkuser($PDOdb, $emploiTemps->fk_user);
	$defaultPlanningUrl = '';
	if($defaultEmploiTemps->getId() > 0 && $emploiTemps->getId() != $defaultEmploiTemps->getId()){
	    $defaultPlanningUrl = dol_buildpath('/absence/emploitemps.php', 1).'?action=view&id='.$defaultEmploiTemps->getId();
	}

	$cardTitle = $langs->trans('ScheduleOf', $userCourant->firstname, $userCourant->lastname);

	if($emploiTemps->is_archive){
	    $cardTitle .= ' ';
	}
	else{
	    $cardTitle .= '('.$langs->trans('DefaultSchedule').')';
	}

	$TBS=new TTemplateTBS();
	print $TBS->render('./tpl/emploitemps.tpl.php'
		,array(
		)
		,array(
			'planning'=>$TPlanning
			,'horaires'=>$THoraire
			,'emploiTemps'=>$TEmploiTemps
			,'joursTempsPartiel'=>$TJoursTempsPartiel
			,'userCourant'=>array(
				'id'=>$userCourant->id
				,'tempsHebdo'=>$emploiTemps->tempsHebdo
			)

			,'view'=>array(
				'mode'=>$mode
				,'head'=>dol_get_fiche_head(edtPrepareHead($emploiTemps, 'emploitemps')  , 'emploitemps', $langs->trans('Absence'))
				,'compteur_id'=>$emploiTemps->getId()
			    ,'titreEdt'=>load_fiche_titre($cardTitle,'', 'title.png', 0, '')
			    ,'defaultPlanningUrl' => $defaultPlanningUrl
			    ,'defaultPlanning' => !empty($defaultPlanningUrl)?'no':'yes'
				// Avant y'avait ça : (ça posait un souci sur l'affichage des caractères accentués)
				//,'titreEdt'=>load_fiche_titre($langs->trans('ScheduleOf', htmlentities($userCourant->firstname, ENT_COMPAT , 'ISO8859-1'), htmlentities($userCourant->lastname, ENT_COMPAT , 'ISO8859-1')),'', 'title.png', 0, '')

				,'listeArchive'=>$listeArchive
			)
			,'droits'=>array(
				'modifierEdt'=>$can_modify_edt
			)
			,'translate' => array(
				'Morning' 				=> $langs->trans('Morning'),
				'Afternoon' 			=> $langs->trans('Afternoon'),
				'Midday' 				=> $langs->trans('Midday'),
				'Evening' 				=> $langs->trans('Evening'),
				'Monday' 				=> $langs->trans('Monday'),
				'Tuesday' 				=> $langs->trans('Tuesday'),
				'Wednesday' 			=> $langs->trans('Wednesday'),
				'Thursday' 				=> $langs->trans('Thursday'),
				'Friday' 				=> $langs->trans('Friday'),
				'Saturday' 				=> $langs->trans('Saturday'),
				'Sunday' 				=> $langs->trans('Sunday'),
				'Beginning' 			=> $langs->trans('Beginning'),
				'End' 					=> $langs->trans('End'),
				'MayRespectHourFormat' 	=> $langs->trans('MayRespectHourFormat'),
				'TotalTimeWeeklyWork' 	=> $langs->trans('TotalTimeWeeklyWork'),
				'Company' 				=> $langs->trans('Company'),
				'Register' 				=> $langs->trans('Register'),
				'Cancel' 				=> $langs->trans('Cancel'),
				'Modify' 				=> $langs->trans('Modify'),
				'Archive' 				=> $langs->trans('Archive'),
			    'is_tempspartiel'		=> $langs->trans('is_tempspartiel'),
			    'GoToDefaultPlanning'   => $langs->trans('GoToDefaultPlanning'),
			    'AbsenceCopy'           => $langs->trans('AbsenceCopy'),
			    'archiveHelpToolTip'    => $langs->trans('archiveHelpToolTip'),
			    'copytoNewHelpToolTip'  => $langs->trans('copytoNewHelpToolTip'),
			)

		)

	);

	echo $form->end_form();
	// End of page

	if(!empty($user->rights->absence->myactions->CanChangeEmploiTempsForGroup) && $mode == 'view') {
	    $form=new TFormCore($_SERVER['PHP_SELF'],'form2','POST');
        echo $form->hidden('action', 'save_to_group');
        echo $form->hidden('fk_user', $emploiTemps->fk_user);

        $formDoli=new Form($db);

        echo $langs->trans('ApplyEmploiTempToGroup').' : ';
        echo $formDoli->select_dolgroups(-1,'fk_group',1);

        echo $form->btsubmit('Appliquer', 'btgroupapply', '', 'butAction');

        $form->end();


	}


	printModalJsForm_copynew($PDOdb,$emploiTemps);



	global $mesg, $error;
	dol_htmloutput_mesg($mesg, '', ($error ? 'error' : 'ok'));
	llxFooter();
}


function printModalJsForm_copynew($PDOdb,$emploiTemps){
    global $langs;


    $PDOdb->Execute("SELECT MAX(date_fin) as date_fin FROM ".MAIN_DB_PREFIX."rh_absence_emploitemps WHERE fk_user=".$emploiTemps->fk_user." AND is_archive=1");
    $row = $PDOdb->Get_line();
    $date_debut = time();
    $date_fin = time() + 604800;
    if($row) {

        if(!empty($row->date_fin)) $date_debut = strtotime($row->date_fin);
        $date_fin = $date_debut + 604800;
        if($date_fin < time ())
        {
            $date_fin = time();
        }
    }



    print '<div id="dialog-form-copynew" title="'.$langs->trans('copytoNewModalTitle').'">';
    print '<p class="validateTips">'.$langs->trans('AllFormAreRequired').'</p>';

    $form=new TFormCore($_SERVER['PHP_SELF'],'form-copynew','POST');

    echo $form->hidden('action', 'copytoNew');
    echo $form->hidden('fk_user', $emploiTemps->fk_user);

    print '<div id="errors-dialog-copynew" ></div>';
    print '<table >';
    print '<tr>';
    print '<td>'.$langs->trans('StartDate').'</td>';
    print '<td>'.$langs->trans('EndDate').'</td>';
    print '</tr>';
    print '<tr>';
    print '<td>';
    print '<input required type="date" name="date_debut" id="copynew_date_debut" value="'.date('Y-m-d',$date_debut).'" >';
    print '</td>';
    print '<td>';
    print '<input required type="date" name="date_fin" id="copynew_date_fin" value="'.date('Y-m-d',$date_fin).'" >';
    print '</td>';
    print '</tr>';
    print '</table>';
    print '<!-- Allow form submission with keyboard without duplicating the dialog button --><input type="submit" tabindex="-1" style="position:absolute; top:-1000px">';

    $form->end();
    print '</div>';

    ?>
<script>
    $( function() {
        var dialog, form;
         	function copytoNewHelpToolTip (){
            	//check traitement
            	var date_debut = $("#copynew_date_debut").val();
            	var date_fin   = $("#copynew_date_fin").val();
            	var formIsValid = true;

            	$.getJSON( "<?php print dol_buildpath("absence/script/interface.php", 1) ?>?get=checkPlanningOverride&date_debut_search=" + date_debut + "&date_fin_search=" + date_fin + "&fk_user=<?php print $emploiTemps->fk_user; ?>"
                    , function( data ) {
                        //console.log(data);

                        var errors = [];

                        if(data.errors != undefined && data.errors.length > 0){
                            $.each( data.errors, function( key, val ) {
                            	errors.push( val);
                            });
                            formIsValid = false;
                        }

                        if(data.count > 0){
                        	errors.push( "<?php print $langs->trans("PlanningRangeAllreadyUsed"); ?>" );
                            formIsValid = false;
                        }

                        if(errors.length > 0){
                            var htmlerrors = $( "<div/>", {
                              "class": "error",
                              html: errors.join( "<br/>" )
                            });

                            $( "#errors-dialog-copynew" ).html(htmlerrors);
                        }



                  }).done(function() {
                	    console.log( "second success" );
                  })
                  .fail(function() {
                    console.log( "error" );
                  })
                  .always(function() {
                	  if(formIsValid == true){
                          dialog.find( "form" ).submit();
                      	return true;
                  	}
                  	else{
                      	return false;
                  	}

                  });
             	console.log(formIsValid);

        	}

            dialog = $( "#dialog-form-copynew" ).dialog({
                autoOpen: false,
                modal: true,
                buttons: {
                    "<?php echo $langs->transnoentitiesnoconv('Validate'); ?>": copytoNewHelpToolTip,
                    "<?php echo $langs->transnoentitiesnoconv('Cancel'); ?>": function() {
                        dialog.dialog( "close" );
                    }
                },
                close: function() {

                }
            });

            $( "#copytoNewHelpToolTipBtn" ).on( "click", function( event ) {
                event.preventDefault();
                dialog.dialog( "open" );
            });



    } );
</script>
    <?php
}

/**
 * Détermine si l'emploi du temps sur lequel on se trouve appartient à un utilisateur dont le user courant est supérieur hiérarchique
 */
function _userCanModifyEdt($id_user_edt) {

	global $db, $user;

	// Tableau d'utilisateur hiérarchiquement en dessous du user courant.
	// Le tableau contient également l'utilisateur courant, car on part du principe que si l'utilisateur a le droit de modifier l'edt de ses collaborateurs en dessous, il peut également modifier le sien.
	$TCollaborateurs = array();

	$sql = 'SELECT rowid
			FROM '.MAIN_DB_PREFIX.'user
			WHERE fk_user = '.$user->id.'
			OR rowid = '.$user->id;

	$resql = $db->query($sql);

	while($res = $db->fetch_object($resql)) {
		$TCollaborateurs[] = $res->rowid;
	}

	return in_array($id_user_edt, $TCollaborateurs);

}




