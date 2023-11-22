<?php

require 'config.php';
require_once DOL_DOCUMENT_ROOT.'/comm/propal/class/propal.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/propal.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/images.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
if (! empty($conf->projet->enabled)) {
	require_once DOL_DOCUMENT_ROOT . '/projet/class/project.class.php';
}
dol_include_once('/absence/class/absence.class.php');
dol_include_once('/absence/lib/absence.lib.php');

$langs->load('compta');
$langs->load('other');
$langs->load('companies');

$action		= GETPOST('action','alpha');
$confirm	= GETPOST('confirm','alpha');
$id			= GETPOST('id','int');
$ref		= GETPOST('ref','alpha');
$permissiontoadd = 1;
// Security check
$socid='';
if (! empty($user->societe_id))
{
	$socid = $user->societe_id;
}
//$result = restrictedArea($user, 'propal', $id);

// Get parameters
$sortfield = GETPOST("sortfield",'alpha');
$sortorder = GETPOST("sortorder",'alpha');
$page = GETPOST("page",'int');
if (empty($page) || $page == -1) { $page = 0; }     // If $page is not defined, or '' or -1
$offset = $conf->liste_limit * $page;
$pageprev = $page - 1;
$pagenext = $page + 1;
if (! $sortorder) $sortorder="ASC";
if (! $sortfield) $sortfield="name";

$PDOdb = new TPDOdb;
$object = new TRH_Absence;
$object->loadTypeAbsencePerTypeUser($PDOdb);
$object->load($PDOdb, $id);
//$object = new Propal($db);
//$object->fetch($id,$ref);


/*
 * Actions
 */

if ($object->id > 0)
{
    $upload_dir = $conf->absence->dir_output.'/'.dol_sanitizeFileName($object->id);
    include_once DOL_DOCUMENT_ROOT . '/core/actions_linkedfiles.inc.php';
}


/*
 * View
 */

llxHeader('',$langs->trans('Documents'));

$form = new Form($db);

if ($object->id > 0)
{
	$upload_dir = $conf->absence->dir_output.'/'.dol_sanitizeFileName($object->id);
//var_dump($upload_dir);exit;
	$head = absencePrepareHead($object);
	dol_fiche_head($head, 'document', $langs->trans('Absence'), -1, 'document');

	// Construit liste des fichiers
	$filearray=dol_dir_list($upload_dir,"all",0,'','(\.meta|_preview.*\.png)$',$sortfield,(strtolower($sortorder)=='desc'?SORT_DESC:SORT_ASC),1);
	$totalsize=0;
	foreach($filearray as $key => $file)
	{
		$totalsize+=$file['size'];
	}

	print '<div class="fichecenter">';

	print '<table class="border" width="100%">';

	// Files infos
	print '<tr><td class="titlefield">'.$langs->trans("NbOfAttachedFiles").'</td><td>'.count($filearray).'</td></tr>';
	print '<tr><td>'.$langs->trans("TotalSizeOfAttachedFiles").'</td><td>'.$totalsize.' '.$langs->trans("bytes").'</td></tr>';

	print "</table>\n";

	print '</div>';


	dol_fiche_end();

	$modulepart = 'absence';
//    var_dump($user->rights->absence);exit;
	$permission = 1;
	$permtoedit = 1;
	$param = '&id=' . $object->id;
	$relativepathwithnofile =  $object->id . '/' ;
	include_once DOL_DOCUMENT_ROOT . '/core/tpl/document_actions_post_headers.tpl.php';
}
else
{
	print $langs->trans("ErrorUnknown");
}

llxFooter();
$db->close();
