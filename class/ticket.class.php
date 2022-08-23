<?php

class TRH_TicketResto extends TObjetStd {
	function __construct() {
		global $langs;

		parent::set_table(MAIN_DB_PREFIX.'rh_ticketresto');
		parent::add_champs('nbTicket','type=entier;');
		parent::add_champs('date_distribution','type=date;index');
		parent::add_champs('montant,partpatron','type=entier;');	//utilisateur concerné
		parent::add_champs('entity,fk_user','type=entier;index;');

		parent::add_champs('code_produit,code_client,pointlivraison,niveau1,niveau2,matricule,nomcouv,nomtitre,raisonsociale,cp,ville,rscarnet,cpcarnet','type=chaine;');

		parent::_init_vars();
		parent::start();

		$this->choixApplication = 'all';

		$this->TUser = array();
		$this->TGroup  = array();
		$this->TChoixApplication = array(
			'all'=> $langs->trans('AllThis')
			,'group'=> $langs->trans('ApplicationChoiceGroup')
			,'user'=> $langs->trans('ApplicationChoiceUser')
		);
	}

	function loadByUserDate(&$ATMdb, $fk_user, $date_distribution) {

		$ATMdb->Execute("SELECT rowid FROM ".$this->get_table()." WHERE fk_user=".$fk_user." AND date_distribution='".$date_distribution."'"  );
		if($obj=$ATMdb->Get_line()) {
			return $this->load($ATMdb, $obj->rowid);
		}
		else {
			return false;
		}

	}

	static function getNdfpByDateRefFromDates($fk_user, $date_debut, $date_fin, $suspicious=false)
	{
		global $db,$conf;

		$TRef = array();
		$db->query("SET SESSION sql_mode = '';");

		$sql = "SELECT n.ref, DATE_FORMAT(nd.dated,'%Y-%m-%d') as dated";
		$sql.= ' FROM '.MAIN_DB_PREFIX.'ndfp_det nd';
		$sql.= ' INNER JOIN '.MAIN_DB_PREFIX.'ndfp n ON (nd.fk_ndfp=n.rowid)';
		$sql.= ' LEFT JOIN '.MAIN_DB_PREFIX.'ndfp_det_link_user ndl ON (nd.rowid=ndl.fk_ndfpdet)';
		$sql.= ' WHERE 1';
		if($suspicious) {
			$sql .= " AND nd.datec >= '".$date_debut."' AND nd.datec <= '".$date_fin."'";
		} else {
			$sql .= " AND nd.dated <= '".$date_fin."' AND nd.datef >= '".$date_debut."'";
		}

		$sql.= ' AND (n.fk_user = '.$fk_user.' OR ndl.fk_user = '.$fk_user.')';

		$sql.= ' AND nd.fk_exp IN (
			SELECT const.value
				FROM ' . MAIN_DB_PREFIX . 'const const
					INNER JOIN ' . MAIN_DB_PREFIX . 'entity entity ON const.entity = entity.rowid AND entity.active = 1
				WHERE name = "RH_NDF_TICKET_RESTO"
		)';
		$sql.= ' GROUP BY nd.dated ';
//		echo $sql."<br><br><br><br><br>";

		$resql = $db->query($sql);
		if ($resql)
		{
			while ($obj = $db->fetch_object($resql))
			{
				$desc = $obj->ref.' ('.$obj->dated.')';
				$TRef[$obj->dated][$obj->ref] = $desc;
			}
		}
		else
		{
			dol_print_error($db);
		}

		return $TRef;
	}

	static function isNDFforDay(&$ATMdb, $date, $fk_user, $withSuspicisous=false) {
		global $conf;
		/* Note repas */
		$sql = "SELECT n.ref, DATE_FORMAT(nd.dated,'%d/%m/%Y') as dated
		FROM ".MAIN_DB_PREFIX."ndfp_det nd LEFT JOIN ".MAIN_DB_PREFIX."ndfp n ON (nd.fk_ndfp=n.rowid)
		WHERE n.fk_user=".$fk_user." AND nd.fk_exp IN (".$conf->global->RH_NDF_TICKET_RESTO.") ";

		if($withSuspicisous) {
			$sql .= " AND ((nd.dated<='".$date."' AND nd.datef>='".$date."') OR (nd.datec LIKE '".$date."%') ) ";
		}
		else{
			$sql .= " AND nd.dated<='".$date."' AND nd.datef>='".$date."'";
		}
		$sql.=" GROUP BY n.ref,nd.dated ";

		$ATMdb->Execute($sql);
		$Tab=array();

		while($obj = $ATMdb->Get_line()) {
			$line = $obj->ref.' ('.$obj->dated.')';
			if(!in_array($line,$Tab)) $Tab[]=$line;
		}


		/*Note invité*/
		$sql = "SELECT n.ref, DATE_FORMAT(nd.dated,'%d/%m/%Y') as dated
		FROM ".MAIN_DB_PREFIX."ndfp_det nd
			INNER JOIN ".MAIN_DB_PREFIX."ndfp n ON (nd.fk_ndfp=n.rowid)
			INNER JOIN ".MAIN_DB_PREFIX."ndfp_det_link_user ndl ON (nd.rowid=ndl.fk_ndfpdet)
		WHERE ndl.fk_user=".$fk_user." AND nd.fk_exp IN (".$conf->global->RH_NDF_TICKET_RESTO.") ";

		if($withSuspicisous) {
			$sql .= " AND ((nd.dated<='".$date."' AND nd.datef>='".$date."') OR (nd.datec LIKE '".$date."%') ) ";
		}
		else{
			$sql .= " AND nd.dated<='".$date."' AND nd.datef>='".$date."'";
		}

		$sql.=" GROUP BY n.ref,nd.dated ";

		$ATMdb->Execute($sql);

		while($obj = $ATMdb->Get_line()) {
			$line = $obj->ref.' ('.$obj->dated.')';
                        if(!in_array($line,$Tab))$Tab[]=$line;
		}

		return $Tab;
	}

	static function getTUserIdWithTicketOption()
	{
		global $db;

		$TUserId = array();

		$sql = 'SELECT fk_object FROM '.MAIN_DB_PREFIX.'user_extrafields WHERE ticketresto_ok = 1';
		$resql = $db->query($sql);
		if ($resql)
		{
			while ($row = $db->fetch_array($resql))
			{
				$TUserId[$row['fk_object']] = $row['fk_object'];
			}
		}
		else
		{
			dol_print_error($db);
		}

		return $TUserId;
	}

	static function getTicketFor(&$ATMdb, $date_debut, $date_fin, $idGroup=0, $fk_user=0)
	{
		global $conf;

		$Tab=array();

		$planningFilters = array();
		if (! empty($conf->global->ABSENCE_TICKETSRESTO_COUNT_ABSENCE_AVALIDER))
		{
			$planningFilters = array('etat' => array('Avalider', 'Validee'),
                                     'extrafields' => array(
                                            'ue.ticketresto_ok' => 1
                                        )
                                    );
		}

		$TAbsence = TRH_Absence::getPlanning($ATMdb, $idGroup, $fk_user, $date_debut, $date_fin, $planningFilters);

		$TUserToScan = self::getTUserIdWithTicketOption();

		if (empty($TAbsence)) $TAbsence = array();
		foreach($TAbsence as $fk_user=>$TAbs) {

			$presence = $ndf = $ndf_with_suspicious = 0;
			$TRefSuspisious = array();

			if (isset($TUserToScan[$fk_user]))
			{
				$TRefNdfp = self::getNdfpByDateRefFromDates($fk_user, $date_debut, $date_fin);
				$TRefNdfpSuspicious = self::getNdfpByDateRefFromDates($fk_user, $date_debut, $date_fin, true);

				foreach($TAbs as $date=>$row)
				{
					$presence += $row['presence_jour_entier'];

					if(	$row['presence_jour_entier'] )
					{
						if (!empty($TRefNdfp[$date])) $ndf++;
					}
				}

				if (!empty($TRefNdfpSuspicious))
				{
					foreach ($TRefNdfpSuspicious as $d => $T)
					{
						$suspicious_found = false;
						foreach ($T as $ref_ndfp => $str)
						{
							if (!isset($TRefNdfp[$d][$ref_ndfp]))
							{
								$suspicious_found = true;
								$TRefSuspisious[] = $str;
							}
						}

						if ($suspicious_found) $ndf_with_suspicious++;
					}
				}
			}

			$Tab[$fk_user]=array(
				'presence'=>$presence
				,'ndf'=>$ndf
				,'ndf_suspicious'=>$ndf_with_suspicious
				, 'TRefSuspisious'=>$TRefSuspisious
			);
		}


		return $Tab;

	}
	static function getHistory(&$ATMdb, $fk_user) {
		$Tab=array();

		$TId = $ATMdb->ExecuteAsArray("SELECT rowid FROM ".MAIN_DB_PREFIX.'rh_ticketresto WHERE fk_user='.$fk_user." ORDER BY date_distribution DESC");

		foreach($TId as $row) {

			$t=new TRH_TicketResto;
			$t->load($ATMdb, $row->rowid);

			$Tab[] = $t;
		}


		return $Tab;
	}
}
