<?php
	require('config.php');
	require('./class/absence.class.php');
	require('./lib/absence.lib.php');

	$langs->load('absence@absence');

	$ATMdb=new TPDOdb;
	$absence=new TRH_Absence;

	_planningResult($ATMdb,$absence, 'edit');

	$ATMdb->close();


function _planningResult(&$ATMdb, &$absence, $mode) {
	global $langs, $conf, $db, $user;
	/*echo $form->hidden('fk_user', $user->id);
	echo $form->hidden('entity', $conf->entity);
	*/
	$date_debut=strtotime( date('Y-m-01') );
	$date_fin=strtotime( date('Y-m-t') );
	$idGroupeRecherche=$idGroupeRecherche2=$idGroupeRecherche3=0;
	$idUserRecherche = (GETPOST('mode')=='auto') ? $user->id : 0;

	if(!isset($_GET['action_search'])) {

		if(!empty($_COOKIE['TRHPlanning']) ){

			$idGroupeRecherche=!empty($_COOKIE['TRHPlanning']['groupe']) ? $_COOKIE['TRHPlanning']['groupe'] : 0;
			$idGroupeRecherche2=!empty($_COOKIE['TRHPlanning']['groupe2']) ? $_COOKIE['TRHPlanning']['groupe2'] : 0;
			$idGroupeRecherche3=!empty($_COOKIE['TRHPlanning']['groupe3']) ? $_COOKIE['TRHPlanning']['groupe3'] : 0;
			$idUserRecherche = !empty($_COOKIE['TRHPlanning']['fk_user']) ? $_COOKIE['TRHPlanning']['fk_user'] : 0;

			if(!empty($_COOKIE['TRHPlanning']['date_debut_search'])) {
				$date_debut=$_COOKIE['TRHPlanning']['date_debut_search'];
				$date_debut_time= str_replace('/', '-', $date_debut);
				$date_debut_time=strtotime($date_debut_time);
				$date_debut_time_1_month = strtotime("+1 month -1 day", $date_debut_time);
				$date_debut_recherche = $date_debut;
			}

			if(!empty($_COOKIE['TRHPlanning']['date_fin_search'])) {
				$date_fin=$_COOKIE['TRHPlanning']['date_fin_search'];
                $date_fin_time = str_replace('/', '-', $date_fin);
				$date_fin_time=strtotime($date_fin_time);
				if(isset($date_debut_time_1_month) && $date_debut_time_1_month < $date_fin_time)
				{
					$date_fin = date('d/m/Y',$date_debut_time_1_month);
				}
				$date_fin_recherche = $date_fin;
			}
		}

	}
	else{


		if(isset($_REQUEST['groupe'])) {
			$idGroupeRecherche=$_REQUEST['groupe'];
			setcookie('TRHPlanning[groupe]', $idGroupeRecherche,strtotime( '+30 days' ),'/');

		}

		if(isset($_REQUEST['groupe2'])) {
			$idGroupeRecherche2=$_REQUEST['groupe2'];
			setcookie('TRHPlanning[groupe2]', $idGroupeRecherche2,strtotime( '+30 days' ),'/');
		}
		if(isset($_REQUEST['groupe3'])) {
			$idGroupeRecherche3=$_REQUEST['groupe3'];
			setcookie('TRHPlanning[groupe3]', $idGroupeRecherche3,strtotime( '+30 days' ),'/');
		}

		if(isset($_REQUEST['date_debut_search'])) {
			 $date_debut=$_REQUEST['date_debut_search'];
			 $date_debut_recherche = $date_debut;
			 setcookie('TRHPlanning[date_debut_search]', $date_debut,strtotime( '+30 days' ),'/');
		}
		if(isset($_REQUEST['date_fin_search'])) {
			$date_fin=$_REQUEST['date_fin_search'];
			$date_fin_recherche = $date_fin;
			setcookie('TRHPlanning[date_fin_search]', $date_fin,strtotime( '+30 days' ),'/');
		}
		if(isset($_REQUEST['fk_user'])){
			 $idUserRecherche=$_REQUEST['fk_user'];
			 setcookie('TRHPlanning[fk_user]', $idUserRecherche,strtotime( '+30 days' ),'/');
		}

	}



	//TODO object USerGroup !
	if($idGroupeRecherche!=0){	//	on recherche le nom du groupe
		$sql="SELECT nom FROM ".MAIN_DB_PREFIX."usergroup
		WHERE rowid =".$idGroupeRecherche;
		$ATMdb->Execute($sql);
		while($ATMdb->Get_line()) {
			$nomGroupeRecherche=$ATMdb->Get_field('nom');
		}
	}else{
		$nomGroupeRecherche='Tous';
	}

	$TGroupe = $TUser = array();

	// ConsultCollabSchedule = Visualiser l'emploi du temps des collaborateurs
	if(!empty($user->rights->absence->myactions->voirTousEdt)) {

		$TGroupe[0]  = $langs->trans('AllThis');
		$sqlReq="SELECT rowid, nom FROM ".MAIN_DB_PREFIX."usergroup WHERE entity IN (0,".$conf->entity.")";
		$ATMdb->Execute($sqlReq);
		while($ATMdb->Get_line()) {
			$TGroupe[$ATMdb->Get_field('rowid')] = $ATMdb->Get_field('nom');
		}

		$TUser=array($langs->trans('AllThis'));
		$sql=" SELECT DISTINCT u.rowid, u.lastname, u.firstname
				FROM ".MAIN_DB_PREFIX."user as u LEFT JOIN ".MAIN_DB_PREFIX."usergroup_user as ug ON (u.rowid=ug.fk_user)
				";

		if($idGroupeRecherche>0) {
			$sql.=" WHERE ug.fk_usergroup=".$idGroupeRecherche;
		}

		$sql.=" ORDER BY u.lastname, u.firstname";
	}
	// ConsultGroupCollabAbsencesPresencesOnSchedule = Voir les absences ou présences des collaborateurs de mes groupes sur le calendrier
	elseif(!empty($user->rights->absence->myactions->voirGroupesAbsences))  {

		$TGroupe[99999]  = $langs->trans('None');

		$sqlReq="SELECT g.rowid, g.nom FROM ".MAIN_DB_PREFIX."usergroup g
			LEFT JOIN ".MAIN_DB_PREFIX."usergroup_user as ug ON (g.rowid=ug.fk_usergroup)
		WHERE g.entity IN (0,".$conf->entity.")
		AND ug.fk_user=".$user->id;

		// TODO faudrait il pas croiser les données avec les groupes que l'utilisateur "Valide" ? (@see tk8838)

		$ATMdb->Execute($sqlReq);
		while($ATMdb->Get_line()) {
			$TGroupe[$ATMdb->Get_field('rowid')] = $ATMdb->Get_field('nom');
		}

		$TUser[0] = $langs->trans('AllThis');
		$TUser[$user->id] = $user->firstname.' '.$user->lastname;
	}
	else{
		$TUser[$user->id] = $user->firstname.' '.$user->lastname;
	}


	if (!empty($sql))
	{
		//print $sql;
		$ATMdb->Execute($sql);
		while($ATMdb->Get_line()) {
			$TUser[$ATMdb->Get_field('rowid')]=$ATMdb->Get_field('lastname')." ".$ATMdb->Get_field('firstname');
		}
	}

	llxHeader('', $langs->trans('Summary'));
	print dol_get_fiche_head(adminRecherchePrepareHead($absence, '')  , '', $langs->trans('Planning'));


	$form=new TFormCore($_SERVER['PHP_SELF'],'formPlanning','GET');
	echo $form->hidden('jsonp', 1);
	echo $form->hidden('action_search', 1);
	$form->Set_typeaff($mode);

	$TStatPlanning=array();

	$TBS=new TTemplateTBS();
	print $TBS->render('./tpl/planningUser.tpl.php'
		,array(
		)
		,array(
			'recherche'=>array(
				'TGroupe'=> (empty($TGroupe) ? "Vous n'avez pas les droits pour faire une sélection de groupe" : $form->combo('','groupe',$TGroupe,$idGroupeRecherche).$form->combo('','groupe2',$TGroupe,$idGroupeRecherche2).$form->combo('','groupe3',$TGroupe,$idGroupeRecherche3))
				,'btValider'=>$form->btsubmit($langs->trans('Submit'), 'valider')
				,'TUser'=>$form->combo('','fk_user',$TUser,$idUserRecherche)

				,'date_debut'=> $form->calendrier('', 'date_debut_search', $date_debut, 12)
				,'date_fin'=> $form->calendrier('', 'date_fin_search', $date_fin, 12)
				,'titreRecherche'=>load_fiche_titre($langs->trans('SearchSummary'),'', 'title.png', 0, '')
				,'titrePlanning'=>load_fiche_titre($langs->trans('CollabsSchedule'),'', 'title.png', 0, '')
			)
			,'userCourant'=>array(
				'id'=>$user->id
				,'nom'=>$user->lastname
				,'prenom'=>$user->firstname
				,'droitRecherche'=>!empty($user->rights->absence->myactions->rechercherAbsence)?1:0
			)
			,'view'=>array(
				'mode'=>$mode
				,'head'=>dol_get_fiche_head(adminRecherchePrepareHead($absence, '')  , '', $langs->trans('Schedule'))
			)
			,'translate' => array(
				'InformSearchAbsencesParameters' => $langs->trans('InformSearchAbsencesParameters'),
				'StartDate' => $langs->trans('StartDate'),
				'EndDate' => $langs->trans('EndDate'),
				'Group' => $langs->trans('Group'),
				'Or' => $langs->trans('Or'),
				'User' => $langs->trans('User')
			)
		)
	);



	?>
	<div id="plannings" style="background-color:#fff">

	<style type="text/css">

	table.planning tr td.jourTravailleNON,table.planning tr td[rel=pm].jourTravailleAM,table.planning tr td[rel=am].jourTravaillePM, span.jourTravailleNON  {

			background-color:#858585;
	}

	table.planning {
		border-collapse:collapse; border:1px solid #ccc; font-size:9px;
	}
	table.planning td {
		border:1px solid #ccc;
		text-align: center;
	}

	table.planning tr:nth-child(even) {
		background: #ddd;
	}
	table.planning tr:nth-child(odd) {
		background: #fff;
	}

	table.planning tr td.rouge, span.rouge{
			background-color:#C03000 !important;
	}
	table.planning tr td.lighter, span.lighter{
		background:url("./img/fond_hachure_01.png");
        box-shadow: inset 0em 0em 0em 10em rgba(255, 255, 255, 0.3);
    }

	table.planning tr td.vert, span.vert{
		/*	background:url("./img/fond_hachure_01.png");*/
			background-color:#248f39 !important;
	}
	table.planning tr td.rougeRTT, span.rougeRTT {
			background-color:#d87a00 !important;
	}
	table.planning tr td.jourFerie, span.jourFerie {
			background-color:#666;
	}

	table.planning tr.footer {
			font-weight:bold;
			background-color:#eee;
	}
	.just-print {
  			display:none;
  	}

	div.bodyline {
		z-index:1050;
	}

    <?php
    for($i=1;$i<=15;$i++) {
    	print ' .persocolor'.$i.' { background-color:'.TRH_TypeAbsence::getColor($i).' !important;  }';
    }

    ?>
	@media print {

  		.no-print, #id-left,#tmenu_tooltip,.login_block  {
  			display:none;
  		}
  		.just-print {
  			display:block;
  		}
	}
	</style>


	<script type="text/javascript">

	function popAddAbsence(date, fk_user) {
		$('#popAbsence').remove();
		$('body').append('<div id="popAbsence"></div>');

		var url = "<?php echo dol_buildpath('/absence/absence.php?action=new',1) ?>&dfMoment=apresmidi&ddMoment=matin&fk_user="+fk_user+"&date_debut="+date+"&date_fin="+date+"&popin=1";
        var selector = "#fiche-abs>#form1";

		$.ajax({
            url: url
            , method: 'GET'
            , success: function(data)
            {
                $(data).find(selector).first().appendTo('#popAbsence');
                $(data).find('#workflowScript').first().appendTo('#popAbsence');

                $('#popAbsence form').submit(function() {
					var formdata = new FormData();
					var ins = $('input[name^="userfile"]')[0].files.length;
					for (var x = 0; x < ins; x++) {
						formdata.append("userfile[]", $('input[name^="userfile"]')[0].files[x]);
					}
					let formVal = $(this).serializeArray();
					for (var i=0; i<formVal.length; i++) formdata.append(formVal[i].name, formVal[i].value);

                    $.ajax({
                        url: "<?php echo dol_escape_js(dol_buildpath('/absence/script/interface.php', 1)) ?>?post=saveAbsence&inc=main"
                        , method: 'POST'
                        , data: formdata
						, processData: false
						, contentType: false
                        , success : function (data)
                        {
                            if(data.saved)
                            {
                                refreshPlanning();
                                $("#popAbsence").dialog('close');
                                $.jnotify(data.TMessages.ok, 'ok');
                            }
                            else
                            {
                                $.jnotify(data.TMessages.error, 'error');
                            }

                            if(data.TMessages.warning)
                            {
                                $.jnotify(data.TMessages.warning, 'warning');
                            }
                        }
                    });


                    return false;
                });

                $('#popAbsence').dialog(
                {
                    title:"Créer une nouvelle absence ou présence"
                    , width:'700'
                    , position: { my: "center", at: "center center+40", of: window }
                    , modal:true
                });
            }
        });
	}

	</script>
	<?php

	if(!empty( $_GET['action_search'] ) || GETPOST('mode')=='auto' || $idUserRecherche>0) {

		if($idUserRecherche>0 && empty( $date_debut_recherche )) {

			if(GETPOST('mode')=='auto') {
				$absence->date_debut_planning = $date_debut;
				$absence->date_fin_planning = $date_fin;

			}
			else{
				$absence->date_debut_planning = strtotime( date('Y-m-01', strtotime('-1 month') ) );
				$absence->date_fin_planning = strtotime( date('Y-m-t', strtotime('+3 month') ) );

			}

		}
		else {
			$absence->set_date('date_debut_planning', $date_debut_recherche);
			$absence->set_date('date_fin_planning',$date_fin_recherche);
		}

		if(GETPOST('jsonp') == 1) {

			?><script type="text/javascript">

				$(document).ready(function() {
					refreshPlanning()

				});

			</script>
			<div id="planning_html">
					<img src="img/Loading.gif" width="100%" />
			</div>
			<?php

		}
		else{
			$TGroupes = array();

			if(! empty($idGroupeRecherche)) $TGroupes[] = $idGroupeRecherche;
			if(! empty($idGroupeRecherche2)) $TGroupes[] = $idGroupeRecherche2;
			if(! empty($idGroupeRecherche3)) $TGroupes[] = $idGroupeRecherche3;

			echo getPlanningAbsence($ATMdb, $absence, $TGroupes, $idUserRecherche);

		}


	}


	echo $form->end_form();

	?></div>
	<script type="text/javascript">
	function refreshPlanning() {

				$('#planning_html').prepend('<div>Rafraîchissement en cours...</div>');

				$.ajax({
					url: "script/interface.php"
					,dataType: "jsonp"
					,async: true
		    		,crossDomain: true
					,data: {
						get:'planning'
						,date_debut_search: "<?php echo $absence->date_debut_planning; ?>"
						,date_fin_search: "<?php echo $absence->date_fin_planning; ?>"
						,groupe : <?php echo (int)$idGroupeRecherche ?>
						,groupe2 : <?php echo (int)$idGroupeRecherche2 ?>
						,groupe3 : <?php echo (int)$idGroupeRecherche3 ?>
						,fk_user : <?php echo (int)$idUserRecherche ?>
						,jsonp : 1
						,inc:'main'
					}

				})
				.done(function (response) {
					$('#planning_html').html( response ); // server response

					$("table.planning td").each(function() {
						if ($(this).attr("title") != undefined)
						{
							$(this).append("<span class=\"just-print\">"+ $(this).attr("title")+"</span>" );
						}

					});

					if ($.tipTip) $(".classfortooltip").tipTip({maxWidth: "600px", edgeOffset: 10, delay: 50, fadeIn: 50, fadeOut: 50});
					else $(".classfortooltip").tooltip({maxWidth: "600px", edgeOffset: 10, delay: 50, fadeIn: 50, fadeOut: 50});
				});
			}

	</script>
	<?php

	global $mesg, $error;
	dol_htmloutput_mesg($mesg, '', ($error ? 'error' : 'ok'));
	llxFooter();


}

