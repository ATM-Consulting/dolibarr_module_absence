
			[onshow;block=begin;when [view.mode]=='edit']
           		[absenceCourante.titreNvDemande;strconv=no;protect=no]
			[onshow;block=end]
			 [onshow;block=begin;when [view.mode]!='edit']
           		[absenceCourante.titreRecapAbsence;strconv=no;protect=no]
			[onshow;block=end]


		<div id="fiche-abs">
			[view.form_start;strconv=no]

				<table class="border" width="100%">
				<tr>
					<td>[translate.User;strconv=no;protect=no]</td>
					<td>[absenceCourante.userAbsence;strconv=no;protect=no]</td>
				</tr>
				<tr>
					<td>[translate.AbsenceType;strconv=no;protect=no]</td>
					<td>[absenceCourante.comboType;strconv=no;protect=no]</td>
				</tr>
				<tr class="date">
					<td>[translate.StartDate;strconv=no;protect=no]</td>
			 		<td>[absenceCourante.date_debut;strconv=no;protect=no]  &nbsp; &nbsp;[absenceCourante.ddMoment;strconv=no;protect=no]  &nbsp; &nbsp;[absenceCourante.hourStartMorning;strconv=no;protect=no]  &nbsp; &nbsp;[absenceCourante.hourEndMorning;strconv=no;protect=no]</td>
				</tr>
				<tr class="date">
					<td>[translate.EndDate;strconv=no;protect=no]</td>
			 		<td>[absenceCourante.date_fin;strconv=no;protect=no]  &nbsp; &nbsp;[absenceCourante.dfMoment;strconv=no;protect=no]  &nbsp; &nbsp;[absenceCourante.hourStartAfternoon;strconv=no;protect=no]  &nbsp; &nbsp;[absenceCourante.hourEndAfternoon;strconv=no;protect=no]</td>
				</tr>
				<tr class="singleDate" style="display: none">
					<td>[translate.date;strconv=no;protect=no]</td>
					<td>[absenceCourante.date_single;strconv=no;protect=no]</td>
				</tr>
				<tr class="singleDate" style="display: none">
					<td>[translate.DurationInHours;strconv=no;protect=no]</td>
					<td>[absenceCourante.dureeSingle;strconv=no;protect=no]</td>
				</tr>
				[onshow;block=begin;when [view.mode]!='edit']
					<tr>
						<td>[translate.DurationInDays;strconv=no;protect=no]</td>
						<td>[absenceCourante.duree;strconv=no;protect=no]</td>
					</tr>
					<tr>
						<td>[translate.DurationInHours;strconv=no;protect=no]</td>
						<td>[absenceCourante.dureeHeure;strconv=no;protect=no]</td>
					</tr>
					<tr>
						<td>[translate.CountedDurationInHours;strconv=no;protect=no]</td>
						<td>[absenceCourante.dureeHeurePaie;strconv=no;protect=no]</td>
					</tr>
					<tr>
						<td>[translate.State;strconv=no;protect=no]</td>
						<td>[absenceCourante.libelleEtat;strconv=no;protect=no]</td>
					</tr>
					<tr>
						<td>[translate.Warning;strconv=no;protect=no]</td>
						<td>[absenceCourante.avertissement;strconv=no;protect=no]</td>
					</tr>
					<tr>
						<td>[translate.ValidationLevel;strconv=no;protect=no]</td>
						<td>[absenceCourante.niveauValidation;strconv=no;protect=no]</td>
					</tr>
					<tr>
						<td>[translate.ValidatorComment;strconv=no;protect=no]</td>
						<td>[absenceCourante.commentaireValideur;strconv=no;protect=no]</td>
					</tr>
					[onshow;block=end]
					<tr>
						<td>[translate.Comment;strconv=no;protect=no]</td>
						<td>[absenceCourante.commentaire;strconv=no;protect=no]</td>
					</tr>
					[onshow;block=begin;when [TNextValideur.#]+-0]
					[onshow;block=begin;when [absenceCourante.time_validation]==0 ]
					<tr class="next_valideurs">
						<td width="25%">[langs.transnoentities(NextValideur)]</td>
						<td><p>[TNextValideur;block=p][TNextValideur.getNomUrl(1);strconv=no]</p></td>
					</tr>
					[onshow;block=end]
					[onshow;block=end]
					<tr>
						<td>[translate.CreatedThe;strconv=no;protect=no]</td>
						<td>[absenceCourante.dt_cre;strconv=no;protect=no]</td>
					</tr>
                    [onshow;block=begin;when [view.mode]=='edit']
					<tr>
						<td>[translate.Documents;strconv=no;protect=no]</td>
						<td>[absenceCourante.documents;strconv=no;protect=no]</td>
					</tr>
                    [onshow;block=end]
					[onshow;block=begin;when [absenceCourante.time_validation]+-0 ]
					<tr>
						<td>[translate.ValidatedThe;strconv=no;protect=no]</td>
						<td>[absenceCourante.date_validation;strconv=no;protect=no] [translate.AbsenceBy;strconv=no;protect=no] [absenceCourante.userValidation]</td>
					</tr>
					[onshow;block=end]

			</table>



   		 <br/>
     	[absenceCourante.titreJourRestant;strconv=no;protect=no]
            <table class="border" id="compteur-user"  width="100%">
                <tr>
                    <td>[translate.HolidaysPaidNMoinsUn;strconv=no;protect=no]</td>
                    <td id="reste">[congesPrec.reste]</td>
                </tr>
                <tr>
                    <td>[translate.HolidaysPaidN;strconv=no;protect=no]</td>
                    <td id="resteN">[congesCourant.reste]</td>
                </tr>
				<tr>
					<td>[translate.CumulatedDayOff]</td>
					<td id="cumule">[rttCourant.cumuleReste]</td>
				</tr>
				<tr>
					<td>[translate.langs.transnoentities(CumulatedDayOffTakenNextYear)]</td>
					<td id="cumuleN1">[rttCourant.cumuleN1]</td>
				</tr>
				<tr>
					<td>[translate.NonCumulatedDayOff]</td>
					<td id="noncumule">[rttCourant.nonCumuleReste]</td>
				</tr>
				<tr>
					<td>[translate.langs.transnoentities(NonCumulatedDayOffTakenNextYear)]</td>
					<td id="noncumuleN1">[rttCourant.nonCumulePrisN1]</td>
				</tr>
				<tr>
					<td>[translate.acquisRecuperation;strconv=no;protect=no]</td>
					<td id="recup">[congesCourant.recup]</td>
				</tr>

				[onshow;block=begin;when [other.dontSendMail]==1]
				<tr>
					<td>[translate.dontSendMail;strconv=no;protect=no]</td>
					<td id="dont_send_mail">[other.dontSendMail_CB;strconv=no;protect=no]</td>
				</tr>
				[onshow;block=end]
				[onshow;block=begin;when [other.autoValidatedAbsence]==1]
				<tr>
					<td>[translate.langs.transnoentities(autoValidatedAbsence)]</td>
					<td id="autoValidatedAbsence"><input type="checkbox" name="autoValidatedAbsence" id="autoValidatedAbsence" value="1" [other.autoValidatedAbsenceChecked;strconv=no] /></td>
				</tr>
				[onshow;block=end]



			</table>
		<div class="tabsAction" >
		[onshow;block=begin;when [absenceCourante.etat]!='Refusee']
		[onshow;block=begin;when [absenceCourante.etat]!='Validee']

				[onshow;block=begin;when [view.mode]=='edit']

					<input type="submit" value="[translate.Register;strconv=no;protect=no]" name="save" class="button" />
				[onshow;block=end]


				[onshow;block=begin;when [view.mode]!='edit']
					[onshow;block=begin;when [userCourant.valideurConges]=='1']

						<a class="butAction" id="action-update"  onclick="if (window.confirm('[translate.ConfirmAcceptAbsenceRequest;strconv=no]')){actionValidAbsence('accept')};">[translate.Accept;strconv=no;protect=no]</a>
						<span class="butActionDelete" id="action-delete"  onclick="refuseAbsence()">[translate.Refuse;strconv=no;protect=no]</span>
<!--						<a style='width:30%' class="butAction" id="action-update"  onclick="if (window.confirm('[translate.ConfirmSendToSuperiorAbsenceRequest;strconv=no]')){actionValidAbsence('sendToSuperior')};">[translate.SendToSuperiorValidator;strconv=no;protect=no]</a>	-->

					[onshow;block=end]
				[onshow;block=end]
		[onshow;block=end]
		[onshow;block=end]

			[view.form_end;strconv=no]

		[onshow;block=begin;when [view.mode]!='edit']
				[onshow;block=begin;when [absenceCourante.droitSupprimer]==1]
						<span class="butActionDelete" id="action-delete"  onclick="if (window.confirm('[translate.ConfirmDeleteAbsenceRequest;strconv=no;protect=no]')){document.location.href='?action=delete&id=[absenceCourante.id]'};">[translate.Delete;strconv=no;protect=no]</span>
				[onshow;block=end]
		[onshow;block=end]
		</div>
		<div style="clear:both;"></div>


		[listUserAlreadyAccepted.titre;strconv=no;protect=no]
		<table class="liste formdoc noborder">
			<tr class="liste_titre">
				<td><b>Date d'acceptation</b></td>
				<td><b>Acceptée par</b></td>
			</tr>
            [TUserAccepted.html;strconv=no;protect=no]
		</table>
		<br />
	</div>




		<div>
		[absenceCourante.titreDerAbsence;strconv=no;protect=no]
		<table  class="liste formdoc noborder" style="width:100%">
				<tr class="liste_titre">
					<td><b>[absenceCourante.lib_date_debut;strconv=no;protect=no]</b></td>
					<td><b>[absenceCourante.lib_date_fin;strconv=no;protect=no]</b></td>
					<td><b>[absenceCourante.lib_type_absence;strconv=no;protect=no]</b></td>
					<td><b>[absenceCourante.lib_duree_decompte;strconv=no;protect=no]</b></td>
					<!-- <td><b>[absenceCourante.lib_conges_dispo_avant;strconv=no;protect=no]</b></td> -->
					<td><b>[absenceCourante.lib_etat;strconv=no;protect=no]</b></td>
				</tr>
				<tbody id="TRecapAbs">

				</tbody>
		</table>

		<div id="user-planning-dialog">
			<div class="content">
			</div>
		</div>

		<div id="user-planning">

		</div>

		</div>
		<br>

		<script type="text/javascript" id="workflowScript">
			function refuseAbsence() {

				var caseDontSendMail = $("#dontSendMail");

				if(caseDontSendMail.is(':checked')){
					var dontSendMail = '&dontSendMail=1'
				};

				if (commentaireValideur = window.prompt('[translate.ConfirmRefuseAbsenceRequest;strconv=no]')){
					
					var link = '?action=refuse&id=[absenceCourante.id]&commentaireValideur='+commentaireValideur+(typeof dontSendMail !== 'undefined' ? dontSendMail : '');
					document.location.href=link;

				};

			}



			function comparerDates(){

					dpChangeDay("date_debut","[view.dateFormat;strconv=no]");
					dpChangeDay("date_fin","[view.dateFormat;strconv=no]");

					jd = $("#date_debutday").val();
					md = $("#date_debutmonth").val();
					ad = $("#date_debutyear").val();
					jf = $("#date_finday").val();
					mf = $("#date_finmonth").val();
					af = $("#date_finyear").val();

					var dFin = new Date(af, mf-1, jf, 0,0,0,0,0);
					var dDeb = new Date(ad, md-1, jd, 0,0,0,0,0);

					if(dDeb>dFin) {
						dFin = dDeb;

						$("#date_debut").val( formatDate( dDeb,"[view.dateFormat;strconv=no]" ) ) ;
	 					$("#date_fin").val( formatDate( dFin,"[view.dateFormat;strconv=no]" ) ) ;


						dpChangeDay("date_debut","[view.dateFormat;strconv=no]");
						dpChangeDay("date_fin","[view.dateFormat;strconv=no]");
					}



			}
			function loadRecapCompteur() {
					if($('#fk_user').length>0) fk_user = $('#fk_user').val();
					else  fk_user = $('#userRecapCompteur').val() ;
					console.log("loadRecapCompteur "+fk_user);
					if(fk_user<=0) return false;

					$('#reste,#cumule,#noncumule,#recup').html('...');

					$.ajax({
						url: 'script/chargerCompteurDemandeAbsence.php?user='+fk_user
						,dataType:'json'
					}).done(function(liste) {

						$('#reste').html(liste.reste);
                        $('#resteN').html(liste.resteN);

						if(liste.reste<0)$('#reste').css({'color':'red', 'font-weight':'bold'});
						else $('#reste').css({'color':'black', 'font-weight':'normal'});

						$('#cumule').html(liste.annuelCumule);
						$('#noncumule').html(liste.annuelNonCumule);
						$('#cumuleN1').html(liste.annuelN1Cumule);
						$('#noncumuleN1').html(liste.annuelN1NonCumule);

						$('#recup').html(liste.acquisRecuperation);

						$('#mensuel').html(liste.mensuel); //TODO n'existe pas ?

						$('#link-to-counter').html(liste.link);

					});


			}

			function loadRecapAbsence() {
					if($('#fk_user').length>0) fk_user = $('#fk_user').val();
					else  fk_user = $('#userRecapCompteur').val() ;

					if(fk_user<=0) return false;

					$.ajax({
						url: 'script/chargerRecapAbsenceUser.php?idUser='+fk_user
						,dataType:'json'
					}).done(function(liste) {
							$('#TRecapAbs').html('');


							for (var i=0; i<liste.length; i++){
								var texte = "<tr>"
									+"<td>"+liste[i].date_debut+"</td>"
									+"<td>"+liste[i].date_fin+"</td>"
									+"<td>"+liste[i].libelle+"</td>"
									+"<td>"+liste[i].duree+"</td>"
									/*+"<td>"+liste[i].congesAvant+"</td>"*/
									+"<td>"+liste[i].libelleEtat+"</td>"
									+"</tr>";
								$('#TRecapAbs').html($('#TRecapAbs').html()+texte);
							}


					});

                    $.ajax({
                        url: "planningUser.php"
                        ,async: true
                        ,crossDomain: true
                        ,data: {
                            action_search:1
                            ,fk_user : fk_user
                            ,'no-link':1
                        }

                    }).done(function(response) {
					    $('#user-planning').html($(response).find("#plannings"));
                        $('#user-planning tr.footer').remove();
                        if ($.tipTip) $(".classfortooltip").tipTip({maxWidth: "600px", edgeOffset: 10, delay: 50, fadeIn: 50, fadeOut: 50});
						else $(".classfortooltip").tooltip({maxWidth: "600px", edgeOffset: 10, delay: 50, fadeIn: 50, fadeOut: 50});
    			    });

			}
			//	script vérifiant que la date de début ne dépasse pas celle de fin
			$(document).ready( function(){
				$("#dfMoment").val('apresmidi');
				/*$("#date_debut").change(comparerDates);
				$("#date_fin").change(comparerDates);*/

				$("#date_debut").attr('onchange', $("#date_debut").attr('onchange')+" ; comparerDates();" );
				$("#date_fin").attr('onchange', $("#date_fin").attr('onchange')+" ; comparerDates();" );

				$("#ddMoment").on('change',comparerDates);
				$("#dfMoment").on('change',comparerDates);

				$("#type").change();
				$("#fk_user").change();

				loadRecapCompteur();
				loadRecapAbsence()
			});

		$('#fk_user').on('change',function(){
				loadRecapCompteur();
				loadRecapAbsence()
		});

		$("#type").on('change', function() {
		   var TUnsecable = [ [absenceCourante.unsecableIds;protect=no;strconv=no] ];
		   var TPresenceHour = [ [absenceCourante.presenceHourIds;protect=no;strconv=no] ];
		   var TPresenceDays = [ [absenceCourante.presenceDayIds;protect=no;strconv=no] ];

		   $("#ddMoment,#dfMoment").prop("disabled",false);
		   $(".date").show();
		   $(".singleDate").hide();
		   $(".periodPresenceHour").hide();
		   $("#dureeSingle").attr('required', false);

		   for(x in TUnsecable) {

		       if($(this).val() == TUnsecable[x]) {
		              $("#ddMoment,#dfMoment").prop("disabled",true);
		       }

		   }

		   for(x in TPresenceHour) {

		       if($(this).val() == TPresenceHour[x]) {
		              $(".date").hide();
		              $(".singleDate").show();
		              $("#dureeSingle").attr('required', true);

			   }

		   }

			for(x in TPresenceDays) {

				if($(this).val() == TPresenceDays[x]) {
					$(".periodPresenceHour").show();
				}

			}

		});

		function actionValidAbsence(type) {

			var link = '';
			var caseDontSendMail = $("#dontSendMail");

			if(type == 'sendToSuperior') {
				link = '?action=niveausuperieur&id=[absenceCourante.id]&validation=ok';
			} else {
				link = '?action=accept&id=[absenceCourante.id]';
			}

			if(caseDontSendMail.is(':checked')){
				link += '&dontSendMail=1'
			};

			document.location.href=link;

		}

		</script>




