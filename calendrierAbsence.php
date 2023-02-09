<?php
	require('config.php');
	require('./class/absence.class.php');
	require('./lib/absence.lib.php');
	
	
	if(!empty($conf->global->RH_ABSENCE_USE_WORKING_PLANNING)){
		header('location:planningUser.php?mode=auto&jsonp=1');
	} 
	
	
	$langs->load('absence@absence');
	
	list($langjs,$dummy) =explode('_', $langs->defaultlang);
	llxHeader('', $langs->trans('AbsencesPresencesCalendar'), '', '', 0,0,
		array('/fullcalendar/lib/moment/min/moment.min.js', '/fullcalendar/lib/fullcalendar/dist/fullcalendar.js','/fullcalendar/lib/fullcalendar/dist/lang/'.$langjs.'.js')
		,array('/fullcalendar/lib/fullcalendar/dist/fullcalendar.min.css','/fullcalendar/css/fullcalendar.css')
	);
		
	$ATMdb=new TPDOdb;
	global $user, $conf;
	$absence=new TRH_absence;
	if(isset($_REQUEST['id'])){
		$absence->load($ATMdb, $_REQUEST['id']);
	}
	else{
		$absence->load($ATMdb, $user->id); // ?
	}
	
	$idGroupe= isset($_REQUEST['groupe']) ? $_REQUEST['groupe'] : 0;
	
	$idCalendar= isset($_REQUEST['rowid']) ? $_REQUEST['rowid'] : '';
	
	$typeAbsence= isset($_REQUEST['typeAbsence']) ? $_REQUEST['typeAbsence'] : 'Tous';
	
	$form=new TFormCore($_SERVER['PHP_SELF'],'formAgenda','GET');
	echo $form->hidden('action', 'afficher');
	echo $form->hidden('id',$absence->getId());
	
	$TabGroupe=array();
	$TabGroupe[0] = 'Tous';
	//récupération du tableau groupe
	//LISTE DE GROUPES
	$sqlReq="SELECT rowid, nom FROM ".MAIN_DB_PREFIX."usergroup ORDER BY nom";
	$ATMdb->Execute($sqlReq);
	while($ATMdb->Get_line()) {
		$TabGroupe[$ATMdb->Get_field('rowid')] = $ATMdb->Get_field('nom');
	}
		
	//on récupère tous les types d'absences existants
	$TTypeAbsence=array();
	$TTypeAbsence['Tous']='Tous';
	$sql="SELECT typeAbsence, libelleAbsence  FROM `".MAIN_DB_PREFIX."rh_type_absence` ";
	$ATMdb->Execute($sql);
	while($ATMdb->Get_line()) {
		$TTypeAbsence[$ATMdb->Get_field('typeAbsence')]=$ATMdb->Get_field('libelleAbsence');
	}
	
	//on récupère le tableau des users suivant le groupe
	$TabUser=array();
	$TabUser[0]='Tous';
	if($idGroupe==0){
		$sql="SELECT rowid,lastname, firstname FROM ".MAIN_DB_PREFIX."user WHERE rowid=".$user->id;
		$ATMdb->Execute($sql);
		if($ATMdb->Get_line()) {
			$TabUser[$ATMdb->Get_field('rowid')]=ucwords(strtolower($ATMdb->Get_field('lastname'))).' '.$ATMdb->Get_field('firstname');
		}
		$sql="SELECT u.rowid,u.lastname, u.firstname FROM ".MAIN_DB_PREFIX."user as u WHERE u.statut=1";
	}else{
		$sql="SELECT u.rowid,u.lastname, u.firstname FROM ".MAIN_DB_PREFIX."user as u,
		".MAIN_DB_PREFIX."usergroup_user as g 
		WHERE g.fk_user=u.rowid AND u.statut=1 AND g.fk_usergroup=".$idGroupe;
	}
	$sql.=" ORDER BY lastname";
	
	$ATMdb->Execute($sql);
	
	
	while($ATMdb->Get_line()) {
		$TabUser[$ATMdb->Get_field('rowid')]=ucwords($ATMdb->Get_field('lastname')).' '.ucfirst(strtolower($ATMdb->Get_field('firstname')));
	}

	$idUser=GETPOST('idUtilisateur','int');
	
	$TBS=new TTemplateTBS();
	print $TBS->render('./tpl/calendrier.tpl.php'
		,array()
		,array(
			'absence'=>array(
				'groupe' 			=> $langs->trans('Group')
				,'utilisateur'  	=> $langs->trans('User')
				,'type' 			=> $langs->trans('AbsenceType')
				,'idUser' 			=>$idUser
				,'idGroupe'			=>$idGroupe
				,'typeAbsence'		=>$typeAbsence
				,'TGroupe'			=>$form->combo('', 'groupe', $TabGroupe,  $idGroupe)
				//,'TUser'=>$user->rights->absence->myactions->voirToutesAbsences?$form->combo('', 'rowid', $absence->TUser,  $absence->TUser):$form->combo('', 'rowid',$TabUser,  $TabUser)
				,'TUser'			=>$form->combo('', 'idUtilisateur', $TabUser,  $idUser)
				,'TTypeAbsence'		=>$form->combo('', 'typeAbsence', $TTypeAbsence,  $typeAbsence)
				,'droits'			=>!empty($user->rights->absence->myactions->voirToutesAbsences) ? 1 : 0
				,'btValider'		=>$form->btsubmit($langs->trans('Submit'), 'valider')
				//,'idAfficher'=>$_REQUEST['rowid']? $_REQUEST['rowid']:0
				,'confirm_delete' 	=> $langs->trans('ConfirmDeleteEvent')
				,'confirm' 			=> $langs->trans('Confirm')
				,'date_debut'		=> $form->calendrier('', 'date_debut', $absence->date_debut, 12)
				,'date_fin'			=> $form->calendrier('', 'date_fin', $absence->date_fin, 12)
				,'loading' 			=> $langs->trans('Loading')
				,'err_load_data' 	=> $langs->trans('ErrImpossibleLoadData')
				,'new_event' 		=> $langs->trans('NewEvent')
				,'today' 			=> $langs->trans('Today')
				,'day' 				=> $langs->trans('Day')
				,'week' 			=> $langs->trans('Week')
				,'month' 			=> $langs->trans('Month')
				,'refresh' 			=> $langs->trans('Refresh')
			)
			,'view'=>array(
				'head'=>dol_get_fiche_head(absencePrepareHead($absence, 'absence')  , 'calendrier', $langs->trans('Absence'))
				,'head3'=>dol_get_fiche_head(absencePrepareHead($absence, 'index')  , 'calendrier', $langs->trans('Absence'))
				,'titreCalendar'=>load_fiche_titre($langs->trans('AbsencesPresencesDiary'),'', 'title.png', 0, '')
				,'agendaEnabled'=>0
			)
		)
	);

	$defaultDay = date('d');
?>
<style style="text/css">
a.fc-day-grid-event,a.fc-time-grid-event  {
	color:#000;
	font-weight:normal;
}
</style>
<script type="text/javascript">

$.fn.serializeObject = function()
{
	var o = {};
	var a = this.serializeArray();
	$.each(a, function() {
		if (o[this.name] !== undefined) {
			if (!o[this.name].push) {
				o[this.name] = [o[this.name]];
			}
			o[this.name].push(this.value || '');
		} else {
			o[this.name] = this.value || '';
		}
	});
	return o;
};

$(document).ready(function() {

var year = '<?php echo date('Y') ?>';
var month = '<?php echo date('m') ?>';
var defaultDate = year+'-'+month+'-<?php echo $defaultDay ?>';
var defaultView='month';

$('#fullcalendar').fullCalendar({
	        header:{
	        	left:   'title',
			    center: 'agendaDay,agendaWeek,month',
			    right:  'prev,next today'
	        }
	        ,defaultDate:defaultDate
	        ,lang: 'fr'
	        ,weekNumbers:true
			,defaultView:'month'
			,viewRender: function( view, element ) {
				console.log('viewRender called');
				
				var formData = $('form[name=formAgenda]').serializeObject();
				formData.start=view.start.format('YYYY-MM-DD HH:mm:ss');
				formData.end=view.end.format('YYYY-MM-DD HH:mm:ss');
				
				$.ajax({
					url: '<?php echo dol_buildpath('/absence/script/absenceCalendarDataFeed.php',1); ?>'
					,dataType: 'json'
					,data: formData
				}).fail(function(jqXHR, textStatus, errorThrown) {
					console.log('Error: jqXHR, textStatus, errorThrown => ', jqXHR, textStatus, errorThrown);
				}).done(function(TEvent, textStatus, jqXHR) {
					console.log('viewRender Done => TEvent = ', TEvent);

					view.calendar.removeEvents();
					view.calendar.addEventSource(TEvent);
				});
			}
			,eventLimit : <?php echo !empty($conf->global->AGENDA_MAX_EVENTS_DAY_VIEW) ? $conf->global->AGENDA_MAX_EVENTS_DAY_VIEW : 3; ?>
			<?php
				if(!empty($conf->global->FULLCALENDAR_HIDE_DAYS)) {

					?>
					,hiddenDays: [ <?php echo $conf->global->FULLCALENDAR_HIDE_DAYS ?> ]
					<?php

				}
			?>
			,eventRender:function( event, element, view ) {

				var note = "";
				<?php

				if($conf->global->FULLCALENDAR_USE_HUGE_WHITE_BORDER) {
					echo 'element.css({
						"border":""
						,"border-radius":"0"
						,"border":"1px solid #fff"
						,"border-left":"2px solid #fff"
					});';

				}

				?>
				if(event.note) note+=event.note;

				if(event.fk_soc>0){
					 element.append('<div>'+event.societe+'</div>');
					 note += '<div>'+event.societe+'</div>';
				}
				if(event.fk_contact>0){
					 element.append('<div>'+event.contact+'</div>');
					 note += '<div>'+event.contact+'</div>';
				}
				<?php
				if(!empty($conf->global->FULLCALENDAR_SHOW_AFFECTED_USER)) {

					?>
					if(event.fk_user>0){
						 element.append('<div>'+event.user+'</div>');
						 note += '<div>'+event.user+'</div>';
					}
					<?php

				}

				if(!empty($conf->global->FULLCALENDAR_SHOW_PROJECT)) {

					?>
					if(event.fk_project>0){
						 element.append('<div>'+event.project+'</div>');
						 note = '<div>'+event.project+'</div>'+note;
					}
					<?php
				}

				?>
				if(event.more)  {
					 element.append('<div>'+event.more+'</div>');
					 note = note+'<div>'+event.more+'</div>';
				}

				element.prepend('<div style="float:right;">'+event.statut+'</div>');

				if ($.tipTip) {
					element.tipTip({
						maxWidth: "600px", edgeOffset: 10, delay: 50, fadeIn: 50, fadeOut: 50
						,content : '<strong>'+event.title+'</strong><br />'+ note
					});

					element.find(".classfortooltip").tipTip({maxWidth: "600px", edgeOffset: 10, delay: 50, fadeIn: 50, fadeOut: 50});
					element.find(".classforcustomtooltip").tipTip({maxWidth: "600px", edgeOffset: 10, delay: 50, fadeIn: 50, fadeOut: 5000});
				} else {
					element.tooltip({
						maxWidth: "600px", edgeOffset: 10, delay: 50, fadeIn: 50, fadeOut: 50
						,content : '<strong>'+event.title+'</strong><br />'+ note
					});
					
					element.find(".classfortooltip").tooltip({maxWidth: "600px", edgeOffset: 10, delay: 50, fadeIn: 50, fadeOut: 50});
					element.find(".classforcustomtooltip").tooltip({maxWidth: "600px", edgeOffset: 10, delay: 50, fadeIn: 50, fadeOut: 5000});
				}
				
			 }
	    });   
       
       
});
	    
</script>    
<?php

	llxFooter();

