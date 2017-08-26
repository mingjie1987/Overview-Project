<?php

include ("class.index.php");

$tab_index = -1;
if( isset($_GET['shinkenCheck']) AND isset($_GET['host']) AND isset($_GET['user']) AND isset($_GET['pwd']) AND isset($_GET['db']) AND isset($_GET['tab_idx']) )
{
    $tab_index = $_GET['tab_idx'];
    $conn = CreateDBConnection($_GET['host'], $_GET['user'], $_GET['pwd'], $_GET['db']);
    if($conn != null){
        $sql="UPDATE `em_mgmt` SET `opt_value` = '".$_GET['shinkenCheck']."' WHERE `opt_key` LIKE 'shinken_check' LIMIT 1 ;";
        $conn->query($sql);
    }
}

?>


<!DOCTYPE html>
<head>
    <title>Paxido setup Information</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link href="//netdna.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">
    <script type="text/javascript" src="//code.jquery.com/jquery-1.10.2.min.js"></script>
    <script type="text/javascript" src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.js"></script>
<!--    <link type="text/css" href="/css/setup.info.css" rel="stylesheet">-->
<!--    <link type="text/css" href="/css/jquery-confirm.css" rel="stylesheet">-->
    <script type="text/javascript" src="setup.js"></script>
    <script type="text/javascript" src="js/date.js"></script>
<!--    <script type="text/javascript" src="js/jquery-confirm.js"></script>-->
    <style>
        .panel.with-nav-tabs .panel-heading{
            padding: 5px 5px 0 5px;
        }
        .panel.with-nav-tabs .nav-tabs{
            border-bottom: none;
        }
        .panel.with-nav-tabs .nav-justified{
            margin-bottom: -1px;
        }
        /********************************************************************/
        /*** PANEL DEFAULT ***/
        .with-nav-tabs.panel-default .nav-tabs > li > a,
        .with-nav-tabs.panel-default .nav-tabs > li > a:hover,
        .with-nav-tabs.panel-default .nav-tabs > li > a:focus {
            color: #777;
        }
        .with-nav-tabs.panel-default .nav-tabs > .open > a,
        .with-nav-tabs.panel-default .nav-tabs > .open > a:hover,
        .with-nav-tabs.panel-default .nav-tabs > .open > a:focus,
        .with-nav-tabs.panel-default .nav-tabs > li > a:hover,
        .with-nav-tabs.panel-default .nav-tabs > li > a:focus {
            color: #777;
            background-color: #ddd;
            border-color: transparent;
        }
        .with-nav-tabs.panel-default .nav-tabs > li.active > a,
        .with-nav-tabs.panel-default .nav-tabs > li.active > a:hover,
        .with-nav-tabs.panel-default .nav-tabs > li.active > a:focus {
            color: #555;
            background-color: #fff;
            border-color: #ddd;
            border-bottom-color: transparent;
        }
        .with-nav-tabs.panel-default .nav-tabs > li.dropdown .dropdown-menu {
            background-color: #f5f5f5;
            border-color: #ddd;
        }
        .with-nav-tabs.panel-default .nav-tabs > li.dropdown .dropdown-menu > li > a {
            color: #777;
        }
        .with-nav-tabs.panel-default .nav-tabs > li.dropdown .dropdown-menu > li > a:hover,
        .with-nav-tabs.panel-default .nav-tabs > li.dropdown .dropdown-menu > li > a:focus {
            background-color: #ddd;
        }
        .with-nav-tabs.panel-default .nav-tabs > li.dropdown .dropdown-menu > .active > a,
        .with-nav-tabs.panel-default .nav-tabs > li.dropdown .dropdown-menu > .active > a:hover,
        .with-nav-tabs.panel-default .nav-tabs > li.dropdown .dropdown-menu > .active > a:focus {
            color: #fff;
            background-color: #555;
        }
        /********************************************************************/
        /*** PANEL PRIMARY ***/
        .with-nav-tabs.panel-primary .nav-tabs > li > a,
        .with-nav-tabs.panel-primary .nav-tabs > li > a:hover,
        .with-nav-tabs.panel-primary .nav-tabs > li > a:focus {
            color: #fff;
        }
        .with-nav-tabs.panel-primary .nav-tabs > .open > a,
        .with-nav-tabs.panel-primary .nav-tabs > .open > a:hover,
        .with-nav-tabs.panel-primary .nav-tabs > .open > a:focus,
        .with-nav-tabs.panel-primary .nav-tabs > li > a:hover,
        .with-nav-tabs.panel-primary .nav-tabs > li > a:focus {
            color: #fff;
            background-color: #3071a9;
            border-color: transparent;
        }
        .with-nav-tabs.panel-primary .nav-tabs > li.active > a,
        .with-nav-tabs.panel-primary .nav-tabs > li.active > a:hover,
        .with-nav-tabs.panel-primary .nav-tabs > li.active > a:focus {
            color: #428bca;
            background-color: #fff;
            border-color: #428bca;
            border-bottom-color: transparent;
        }
        .with-nav-tabs.panel-primary .nav-tabs > li.dropdown .dropdown-menu {
            background-color: #428bca;
            border-color: #3071a9;
        }
        .with-nav-tabs.panel-primary .nav-tabs > li.dropdown .dropdown-menu > li > a {
            color: #fff;
        }
        .with-nav-tabs.panel-primary .nav-tabs > li.dropdown .dropdown-menu > li > a:hover,
        .with-nav-tabs.panel-primary .nav-tabs > li.dropdown .dropdown-menu > li > a:focus {
            background-color: #3071a9;
        }
        .with-nav-tabs.panel-primary .nav-tabs > li.dropdown .dropdown-menu > .active > a,
        .with-nav-tabs.panel-primary .nav-tabs > li.dropdown .dropdown-menu > .active > a:hover,
        .with-nav-tabs.panel-primary .nav-tabs > li.dropdown .dropdown-menu > .active > a:focus {
            background-color: #4a9fe9;
        }
        /********************************************************************/
        /*** PANEL SUCCESS ***/
        .with-nav-tabs.panel-success .nav-tabs > li > a,
        .with-nav-tabs.panel-success .nav-tabs > li > a:hover,
        .with-nav-tabs.panel-success .nav-tabs > li > a:focus {
            color: #3c763d;
        }
        .with-nav-tabs.panel-success .nav-tabs > .open > a,
        .with-nav-tabs.panel-success .nav-tabs > .open > a:hover,
        .with-nav-tabs.panel-success .nav-tabs > .open > a:focus,
        .with-nav-tabs.panel-success .nav-tabs > li > a:hover,
        .with-nav-tabs.panel-success .nav-tabs > li > a:focus {
            color: #3c763d;
            background-color: #d6e9c6;
            border-color: transparent;
        }
        .with-nav-tabs.panel-success .nav-tabs > li.active > a,
        .with-nav-tabs.panel-success .nav-tabs > li.active > a:hover,
        .with-nav-tabs.panel-success .nav-tabs > li.active > a:focus {
            color: #3c763d;
            background-color: #fff;
            border-color: #d6e9c6;
            border-bottom-color: transparent;
        }
        .with-nav-tabs.panel-success .nav-tabs > li.dropdown .dropdown-menu {
            background-color: #dff0d8;
            border-color: #d6e9c6;
        }
        .with-nav-tabs.panel-success .nav-tabs > li.dropdown .dropdown-menu > li > a {
            color: #3c763d;
        }
        .with-nav-tabs.panel-success .nav-tabs > li.dropdown .dropdown-menu > li > a:hover,
        .with-nav-tabs.panel-success .nav-tabs > li.dropdown .dropdown-menu > li > a:focus {
            background-color: #d6e9c6;
        }
        .with-nav-tabs.panel-success .nav-tabs > li.dropdown .dropdown-menu > .active > a,
        .with-nav-tabs.panel-success .nav-tabs > li.dropdown .dropdown-menu > .active > a:hover,
        .with-nav-tabs.panel-success .nav-tabs > li.dropdown .dropdown-menu > .active > a:focus {
            color: #fff;
            background-color: #3c763d;
        }
        /********************************************************************/
        /*** PANEL INFO ***/
        .with-nav-tabs.panel-info .nav-tabs > li > a,
        .with-nav-tabs.panel-info .nav-tabs > li > a:hover,
        .with-nav-tabs.panel-info .nav-tabs > li > a:focus {
            color: #31708f;
        }
        .with-nav-tabs.panel-info .nav-tabs > .open > a,
        .with-nav-tabs.panel-info .nav-tabs > .open > a:hover,
        .with-nav-tabs.panel-info .nav-tabs > .open > a:focus,
        .with-nav-tabs.panel-info .nav-tabs > li > a:hover,
        .with-nav-tabs.panel-info .nav-tabs > li > a:focus {
            color: #31708f;
            background-color: #bce8f1;
            border-color: transparent;
        }
        .with-nav-tabs.panel-info .nav-tabs > li.active > a,
        .with-nav-tabs.panel-info .nav-tabs > li.active > a:hover,
        .with-nav-tabs.panel-info .nav-tabs > li.active > a:focus {
            color: #31708f;
            background-color: #fff;
            border-color: #bce8f1;
            border-bottom-color: transparent;
        }
        .with-nav-tabs.panel-info .nav-tabs > li.dropdown .dropdown-menu {
            background-color: #d9edf7;
            border-color: #bce8f1;
        }
        .with-nav-tabs.panel-info .nav-tabs > li.dropdown .dropdown-menu > li > a {
            color: #31708f;
        }
        .with-nav-tabs.panel-info .nav-tabs > li.dropdown .dropdown-menu > li > a:hover,
        .with-nav-tabs.panel-info .nav-tabs > li.dropdown .dropdown-menu > li > a:focus {
            background-color: #bce8f1;
        }
        .with-nav-tabs.panel-info .nav-tabs > li.dropdown .dropdown-menu > .active > a,
        .with-nav-tabs.panel-info .nav-tabs > li.dropdown .dropdown-menu > .active > a:hover,
        .with-nav-tabs.panel-info .nav-tabs > li.dropdown .dropdown-menu > .active > a:focus {
            color: #fff;
            background-color: #31708f;
        }
        /********************************************************************/
        /*** PANEL WARNING ***/
        .with-nav-tabs.panel-warning .nav-tabs > li > a,
        .with-nav-tabs.panel-warning .nav-tabs > li > a:hover,
        .with-nav-tabs.panel-warning .nav-tabs > li > a:focus {
            color: #8a6d3b;
        }
        .with-nav-tabs.panel-warning .nav-tabs > .open > a,
        .with-nav-tabs.panel-warning .nav-tabs > .open > a:hover,
        .with-nav-tabs.panel-warning .nav-tabs > .open > a:focus,
        .with-nav-tabs.panel-warning .nav-tabs > li > a:hover,
        .with-nav-tabs.panel-warning .nav-tabs > li > a:focus {
            color: #8a6d3b;
            background-color: #faebcc;
            border-color: transparent;
        }
        .with-nav-tabs.panel-warning .nav-tabs > li.active > a,
        .with-nav-tabs.panel-warning .nav-tabs > li.active > a:hover,
        .with-nav-tabs.panel-warning .nav-tabs > li.active > a:focus {
            color: #8a6d3b;
            background-color: #fff;
            border-color: #faebcc;
            border-bottom-color: transparent;
        }
        .with-nav-tabs.panel-warning .nav-tabs > li.dropdown .dropdown-menu {
            background-color: #fcf8e3;
            border-color: #faebcc;
        }
        .with-nav-tabs.panel-warning .nav-tabs > li.dropdown .dropdown-menu > li > a {
            color: #8a6d3b;
        }
        .with-nav-tabs.panel-warning .nav-tabs > li.dropdown .dropdown-menu > li > a:hover,
        .with-nav-tabs.panel-warning .nav-tabs > li.dropdown .dropdown-menu > li > a:focus {
            background-color: #faebcc;
        }
        .with-nav-tabs.panel-warning .nav-tabs > li.dropdown .dropdown-menu > .active > a,
        .with-nav-tabs.panel-warning .nav-tabs > li.dropdown .dropdown-menu > .active > a:hover,
        .with-nav-tabs.panel-warning .nav-tabs > li.dropdown .dropdown-menu > .active > a:focus {
            color: #fff;
            background-color: #8a6d3b;
        }
        /********************************************************************/
        /*** PANEL DANGER ***/
        .with-nav-tabs.panel-danger .nav-tabs > li > a,
        .with-nav-tabs.panel-danger .nav-tabs > li > a:hover,
        .with-nav-tabs.panel-danger .nav-tabs > li > a:focus {
            color: #a94442;
        }
        .with-nav-tabs.panel-danger .nav-tabs > .open > a,
        .with-nav-tabs.panel-danger .nav-tabs > .open > a:hover,
        .with-nav-tabs.panel-danger .nav-tabs > .open > a:focus,
        .with-nav-tabs.panel-danger .nav-tabs > li > a:hover,
        .with-nav-tabs.panel-danger .nav-tabs > li > a:focus {
            color: #a94442;
            background-color: #ebccd1;
            border-color: transparent;
        }
        .with-nav-tabs.panel-danger .nav-tabs > li.active > a,
        .with-nav-tabs.panel-danger .nav-tabs > li.active > a:hover,
        .with-nav-tabs.panel-danger .nav-tabs > li.active > a:focus {
            color: #a94442;
            background-color: #fff;
            border-color: #ebccd1;
            border-bottom-color: transparent;
        }
        .with-nav-tabs.panel-danger .nav-tabs > li.dropdown .dropdown-menu {
            background-color: #f2dede; /* bg color */
            border-color: #ebccd1; /* border color */
        }
        .with-nav-tabs.panel-danger .nav-tabs > li.dropdown .dropdown-menu > li > a {
            color: #a94442; /* normal text color */
        }
        .with-nav-tabs.panel-danger .nav-tabs > li.dropdown .dropdown-menu > li > a:hover,
        .with-nav-tabs.panel-danger .nav-tabs > li.dropdown .dropdown-menu > li > a:focus {
            background-color: #ebccd1; /* hover bg color */
        }
        .with-nav-tabs.panel-danger .nav-tabs > li.dropdown .dropdown-menu > .active > a,
        .with-nav-tabs.panel-danger .nav-tabs > li.dropdown .dropdown-menu > .active > a:hover,
        .with-nav-tabs.panel-danger .nav-tabs > li.dropdown .dropdown-menu > .active > a:focus {
            color: #fff; /* active text color */
            background-color: #a94442; /* active bg color */
        }

        /*****************************************************/
        /*  page style  */
        table {
            width:100%;
            border-collapse:collapse;
            border:none;
        }
        .details {
            border-collapse:collapse;
            border:none;
        }
        .details td
        {
            background:#ffffff;
            padding:5px;
            border:none;
        }
        td {
            background:#ffffff;
            border:1px solid black;
            padding:5px;
        }

        .head {
            background-color:#bedaf1;
            font-weight:bold;
        }

        .table,
        .table-border, thead, tbody, tr,
        .table-bordered > thead > tr > th,
        .table-bordered > tbody > tr > th,
        .table-bordered > tfoot > tr > th,
        .table-bordered > thead > tr > td,
        .table-bordered > tbody > tr > td,
        .table-bordered > tfoot > tr > td {
            border: 1px solid #848484;
        }

        .width10 {width: 10%;}
        .width20 {width: 20%;}
        .width30 {width: 30%;}
        .width40 {width: 40%;}
        .width50 {width: 50%;}
        .width60 {width: 60%;}
        .width70 {width: 70%;}
        .width80 {width: 80%;}
        .width90 {width: 90%;}
        .width100 {width: 100%;}

        .t-shadow {
            text-shadow: 2px 2px 3px #ada8a8;
        }


        /*****************/
        .navbar-nav {
            width: 100%;
            text-align: center;
        }
        .navbar-nav > li {
            float: none;
            display: inline-block;
        }
    </style>
</head>

<body>
    <div class="content center" style="">

        <div style="margin-top: 50px;"></div>
        <h1 style="text-align: center; text-shadow: 2px 2px 3px #ada8a8;">Paxido Setup Information</h1>
        <div style="margin-top: 50px;"></div>


        <div class="col-md-offset-1 col-md-10">
            <div class="panel with-nav-tabs panel-primary">
                <div class="panel-heading">
                    <ul class="nav nav-tabs">
                        <?php
                        foreach ($paxido_dir_names as $idx=>$dir) {
                            //echo $dir;
                            if( getSubDirCount($paxido_dir_full[$idx]) == 0 ) continue;
                            if ($tab_index != -1){
                                $active = ($idx == $tab_index) ? "active" : "";
                            } else
                                $active = ($idx == 0) ? "active" : "";
                            $tab_id = "paxido-tab-id-".$idx;
                            echo "<li class='$active'>";
                            echo "<a data-toggle='tab' href='#$tab_id'>$dir</a></li>";
                        }
                        ?>
                    </ul>
                </div>

                <div class="panel-body">
                    <div class="tab-content" style="margin-top: 20px;">
                        <?php
                        foreach ($paxido_dir_names as $idx => $dir) {
                            //echo $paxido_dir_full[$idx];
                            if( getSubDirCount($paxido_dir_full[$idx]) == 0 ) continue;
                            if ($tab_index != -1){
                                $class = ($idx == $tab_index) ? "tab-pane fade in active" : "tab-pane fade";
                            } else
                                $class = ($idx == 0) ? "tab-pane fade in active" : "tab-pane fade";
                            $tab_id = "paxido-tab-id-".$idx;

                            $sub_full = array();
                            $sub_dirs = getDirectories($paxido_dir_full[$idx], $sub_full);

                            echo "<div id='$tab_id' class='$class'>";
                            echo "<table class='table table-bordered'>";
                            echo "<thead>";
                            echo "<tr>";
                            echo "<td class='head' style='width:20px;'></td>";
                            echo "<td class='head width10'>Auftrag</td>";
                            echo "<td class='head width10'>Auftraggeber</td>";
                            echo "<td class='head width15'>Veranstaltung</td>";
                            echo "<td class='head width10'>P</td>";
                            echo "<td class='head width10'>M</td>";
                            echo "<td class='head width10'>SQL DB</td>";
                            echo "<td class='head width10'>Aktivitäten</td>";
                            echo "<td class='head width10'>Details</td>";
                            echo "<td class='head width15'>operations-menü</td>";
                            echo "</tr>";
                            echo "</thead>";


                            echo "<tbody>";
                            foreach ($sub_dirs as $key => $sub){
                                $no = $key + 1;
                                $config = parse_emconf($sub_full[$key]);
                                if($config == false) continue;
                                $db_name = $config['sql_database'];
                                $state_color = get_state_style($config);
                                $em_mgmt = get_mgmt_data($config);
                                $reflex_auftragsnummer = '';
                                $ansprechpartner       = '';
                                $sitetitle = '';
                                $base_url = '';
                                $shinken_check = '';
                                if (isset($em_mgmt['reflex_auftragsnummer']))
                                {
                                    $reflex_auftragsnummer =$em_mgmt['reflex_auftragsnummer'];
                                }
                                if (isset($em_mgmt['ansprechpartner']))
                                {
                                    $ansprechpartner =$em_mgmt['ansprechpartner'];
                                }
                                if (isset($em_mgmt['sitetitle']))
                                {
                                    $sitetitle =$em_mgmt['sitetitle'];
                                }
                                if (isset($em_mgmt['basis_url']))
                                {
                                    $base_url =$em_mgmt['basis_url'];
                                }
                                if (isset($em_mgmt['shinken_check']))
                                {
                                    $shinken_check =$em_mgmt['shinken_check'];
                                }

                                echo "<tr>";
                                echo "<td>$no</td>";
                                echo "<td style='background:#". $state_color .";'>$reflex_auftragsnummer</td>";
                                echo "<td>$ansprechpartner</td>";
                                echo "<td>$sitetitle</td>";
                                echo "<td><a target='_blank' href='".$base_url."/'>Anmeldeseite</a></td>";
                                echo "<td><a target='_blank' href='".$base_url."/verwaltung'>Verwaltung</a></td>";
                                echo "<td><a target='_blank' href='http://internalweb.paul-gmbh.com/dbadmin-internal/sql.php?db=". $db_name ."&server=2&table=dbfields&pos=0'>". $db_name ."</a></td>";
                                echo "<td>";
                                if (isset($reflex_auftragsnummer) && trim($reflex_auftragsnummer) != '' && trim($reflex_auftragsnummer) != '--' && trim($reflex_auftragsnummer) != '000000')
                                    echo '<a target="_blank" href="http://internalweb.paul-gmbh.com/aktivitaeten/?at='. $reflex_auftragsnummer .'">Activities</a>';
                                echo "</td>";
                                echo "<td><a href='javascript:show_details(\"details_". $idx . "_" . $key . "\");'>details</a></td>";
                                echo "<td>";
                                echo '<a href="setup_copy.php?path='.$sub_full[$key].'" style="color: #5d0055;text-decoration: underline;">copy</a>&emsp;';
                                echo '<a href="setup_move.php?path='.$sub_full[$key].'" style="color: #00245d;text-decoration: underline;">move</a>&emsp;';
                                if($shinken_check == 0) {
                                    echo '<a href="?shinkenCheck=1&host='.$config['sql_host'].'&user='.$config['sql_user'].
                                        '&pwd='.$config['sql_passwd'].'&db='.$config['sql_database'].
                                        '&tab_idx='.$idx.'" style="color: #005d0c;text-decoration: underline;"><i>Inaktiv</i></a>';
                                } else if($shinken_check==1) {
                                    echo '<a href="?shinkenCheck=0&host='.$config['sql_host'].'&user='.$config['sql_user'].
                                        '&pwd='.$config['sql_passwd'].'&db='.$config['sql_database'].
                                        '&tab_idx='.$idx.'" style="color: #005d0c;text-decoration: underline;">Aktiv</a>';
                                }
                                echo "</td>";
                                echo "</tr>";



                                /*********************************************************/
                                $conn = CreateDBConnection($config['sql_host'], $config['sql_user'], $config['sql_passwd'], $config['sql_database']);
                                if( $conn != null ){
                                    echo '<tr style="border:none;">';
                                    echo '<td valign="top" colspan="10" style="padding:0px;width:100%;border:none;">';
                                    echo '<div style="display:none;" id="details_'. $idx . '_' . $key .'">';
                                    echo '<table class="table table-bordered" style="margin: 0">';
                                    echo '<tr>';

                                    /*******************************************************/
                                    echo '<td style="width:25%; padding: 0;">';
                                    echo '<table style="border: 0;">';
                                    echo '<tr><td colspan="2" style="text-align: center; background-color: #ebffe0; border:0"><i><b>Benutzer</b></i></td></tr>';
                                    echo '<tr><td style="background-color: #ebffe0; border:1">Username</td><td style="background-color: #ebffe0; border:0">Passwort</td></tr>';
                                    $sql = 'select * from user';
                                    $res = $conn->query($sql);
                                    while ($usr = $res->fetch_assoc())
                                    {
                                        echo '<tr><td style="border:0; background-color: #ebffe0;">'. $usr['loginname'] .'</td>';
                                        echo '<td style="border:0; background-color: #ebffe0;">'. $usr['passwort'] .'</td></tr>';
                                    }
                                    echo '</table>';
                                    echo '</td>';

                                    /*******************************************************/
                                    echo '<td style="width:25%; padding: 0;">';
                                    echo '<table style="border: 0">';
                                    echo '<tr><td style="text-align: center; background-color: #e0fffc; border:0"><i><b>Einträge in Personen-DB</b></i></td></tr>';
                                    echo '<tr><td style="background-color: #e0fffc;  border:0">Anzahl</td></tr>';
                                    $sql = 'select count(*) from personen';
                                    $res = $conn->query($sql);
                                    $num = $res->fetch_row();
                                    $num = $num[0];
                                    echo '<tr><td style="border:0; background-color: #e0fffc;">'. $num .'</td></tr>';
                                    echo '</table>';
                                    echo '</td>';

                                    /*******************************************************/
                                    echo '<td style="width:25%; padding: 0;">';
                                    echo '<table style="border: 0">';
                                    echo '<tr><td style="text-align: center; background-color: #ffe0e0; border:0"><i><b>Rechnungen</b></i></td></tr>';
                                    echo '<tr><td style="background-color: #ffe0e0; border:0">Rechnungsnummer</td></tr>';
                                    /*$rechnungen = get_reflex_rechnungen($reflex_auftragsnummer);
                                    foreach ($rechnungen as $rech){
                                        echo '<tr><td style="border:0; background-color: #ffe0e0">'. $rech .'</td></tr>';
                                    }*/
                                    echo '</table>';
                                    echo '</td>';

                                    /*******************************************************/
                                    echo '<td style="width:25%; padding: 0;">';
                                    echo '<table style="border: 0">';
                                    echo '<tr><td colspan="2" style="text-align: center; background-color: #f7e0ff; border:0"><i><b>Locations</b></i></td></tr>';
                                    echo '<tr><td style="background-color: #f7e0ff; border:0">Titel</td><td style="background-color: #f7e0ff; border:1">Anzeigen bis</td></tr>';
                                    $sql = 'select * from locations';
                                    $res = $conn->query($sql);
                                    while ($loc = $res->fetch_assoc())
                                    {
                                        $ftime = "";//($loc['show_till_date'] == '' || $loc['show_till_date'] == null) ? '' : strftime('%d.%m.%Y', $loc['show_till_date']);
                                        echo '<tr><td style="border: 0;background-color: #f7e0ff;">'. utf8_encode($loc['title']) .'</td><td style="border: 0;background-color: #f7e0ff;">'. $ftime .'</td></tr>';
                                    }
                                    echo '</table>';
                                    echo '</td>';

                                    echo '</tr>';
                                    echo '</table>';
                                    echo '</div>';
                                    echo '</td>';
                                    echo '</tr>';

                                    $conn->close();
                                }
                            }
                            echo "</tbody>";
                            echo "</table>";
                            echo "</div>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>

    </div>
</body>
</html>
