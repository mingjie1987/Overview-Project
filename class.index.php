<?php

$root_dir = $_SERVER['DOCUMENT_ROOT'];

$config = array();
$paxido_dir_full = array();
$paxido_dir_names = getAllCopiedDirectories(dirname($root_dir), $paxido_dir_full);

function checkOS(){
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        return "windows";
    } else {
        return "linux";
    }
}

function getAllCopiedDirectories($parent_dir, &$full_path)
{
    $fns = array();
    $EnableDirNames = array(
        "my.paxido.com",
        "gse.paxido.com",
        "sv.paxido.com",
        "ibm.paxido.com",
        "ibm-ch.paxido.com",
        "events.paxido.com",
        "toshiba.paxido.com",
        "lenovo.paxido.com",
        "hds.paxido.com",
        "bwi.paxido.com",
        "huawei.paxido.com",
        "philips.paxido.com");
    if ($handle = opendir($parent_dir))
    {
        while (false !== ($file = readdir($handle)))
        {
            if(in_array($file, $EnableDirNames)){
                $dir_name = $parent_dir . '/'.$file;
                if (is_dir($dir_name) && $file != '.' && $file != '..'){
                    $fns[] = $file;
                    $full_path[] = $dir_name;
                }
            }
        }
        closedir($handle);
    }
    sort($fns);
    return $fns;
}

function getDirectories($parent_dir, &$full_path)
{
    $fns = array();
    $invisibleFileNames = array(".", "..", ".svn", ".idea");

    if ($handle = opendir($parent_dir))
    {
        while (false !== ($file = readdir($handle)))
        {
            if(!in_array($file, $invisibleFileNames)){
                $dir_name = $parent_dir . '/'.$file;
                if (is_dir($dir_name) && $file != '.' && $file != '..'){
                    $fns[] = $file;
                    $full_path[] = $dir_name;
                }
            }
        }
        closedir($handle);
    }
    sort($fns);
    return $fns;
}

function getSubDirCount($path){
    $tmp_full = array();
    $tmp_dirs = getDirectories($path, $tmp_full);
    return count($tmp_dirs);
}

function parse_emconf($path)
{
    $config = array();

    $cfg_file =  $path."/etc/em.conf";

    if (file_exists($cfg_file))
    {
        $INT_i = 0;
        $INT_fd = fopen ($cfg_file, "r");
        while (!feof ($INT_fd))
        {
            $INT_buffer = fgets($INT_fd, 4096);
            $INT_i++;
            $INT_inhalt[$INT_i]= $INT_buffer;
            $INT_inhalt[$INT_i]=preg_replace("[\s]","",$INT_inhalt[$INT_i]);
        }
        fclose ($INT_fd);

        for ($INT_n=1;$INT_n<=$INT_i;$INT_n++)
        {
            $INT_frages=substr($INT_inhalt[$INT_n],0,1);
            $INT_frages=Ord($INT_frages);
            if ( ($INT_frages <> '0') and ($INT_frages <> '35') )
            {
                $INT_vari_name=substr($INT_inhalt[$INT_n],0,strpos($INT_inhalt[$INT_n],"="));
                $INT_vari_name=preg_replace("/\-/","_",$INT_vari_name);
                $INT_wert=substr($INT_inhalt[$INT_n],(strpos($INT_inhalt[$INT_n],"=")+1));
                $$INT_vari_name=$INT_wert;
                if (trim($INT_vari_name) != '') $config[$INT_vari_name] = $INT_wert;
            }
        }
    }
    else
    {
        return false;
        //echo "config-file not found....<br>";
        //exit(2);
    }
    return $config;
}

function get_mgmt_data($config){
    $rows = array();
    $host = $config['sql_host'];
    $user = $config['sql_user'];
    $pass = $config['sql_passwd'];
    $db = $config['sql_database'];
    $conn = CreateDBConnection($host, $user, $pass, $db);
    if($conn == null) return $rows;

    $sql = "Select * From em_mgmt;";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while($sub = $result->fetch_assoc()) {
            $rows[$sub['opt_key']] = $sub['opt_value'];
        }
    }
    $conn->close();

    return $rows;
}

function CreateDBConnection($host, $user, $pwd, $db = ''){
    try{
        $conn = new mysqli($host, $user, $pwd);
        if ($conn->connect_error) return null;

        $sql = "SHOW DATABASES LIKE '" . $db . "'";
        $result = $conn->query($sql);
        if ($result->num_rows == 0) return null;

        return new mysqli($host, $user, $pwd, $db);

    } catch (Exception $e){
        return null;
    }
}

function get_state_style($config){
    $bgcolor = '35FF23';
    $host = $config['sql_host'];
    $user = $config['sql_user'];
    $pass = $config['sql_passwd'];
    $db = $config['sql_database'];
    $conn = CreateDBConnection($host, $user, $pass, $db);
    if($conn == null) return $bgcolor;

    $color_ev_archive = 'FF0000';
    $color_ev_close   = 'FFC105';
    $color_ev_noclose = 'ff00ff';
    $color_ev_open    = '35FF23';
    $sql = 'select IFNULL(opt_value,0) from em_mgmt where opt_key = "ev_close_date"';
    $result = $conn->query($sql);
    $cldate = $result->fetch_row();
    $cldate = $cldate[0];

    $sql = 'select IFNULL(opt_value,0) from em_mgmt where opt_key = "ev_archive_date"';
    $result = $conn->query($sql);
    $adate = $result->fetch_row();
    $archdate = $adate[0];


    $now = time();
    $critical_period = 172800; // 4 days
    if ( $archdate != '' && $archdate != '0' && ($archdate < $now) )
    {
        $bgcolor = $color_ev_archive;
    }
    elseif(stristr($cldate,'-') || $cldate == '0') // OLD DATABASE VALUES
    {
        $cldate =  strtotime($cldate);
        $timedelta = $cldate - $now;

        if($cldate < $now) $bgcolor = $color_ev_noclose;
        elseif($timedelta < $critical_period) $bgcolor ='E3FF0F';
        else $bgcolor = $color_ev_open;
    }
    else // new unix time stamp
    {
        $timedelta = $cldate - $now;
        if($cldate < $now) $bgcolor = $color_ev_close;
        elseif($timedelta < $critical_period) $bgcolor ='E3FF0F';
        else $bgcolor = $color_ev_open;
    }
    return $bgcolor;
}

function get_details_data($config){
    $content = "";
    $host = $config['sql_host'];
    $user = $config['sql_user'];
    $pass = $config['sql_passwd'];
    $db = $config['sql_database'];
    $conn = CreateDBConnection($host, $user, $pass, $db);
    if($conn == null) return $content;

    $content = "<table class='table table-bordered'>";
    $content.= "<tr><td class='head' colspan='2'>User</td></tr>";
    $content.= "</table>";

    return $content;

}

function get_reflex_rechnungen($pid)
{
    $rechnungen = array();
    $conn = CreateDBConnection("reflex.paul-gmbh.com",'reflex','start');
    if($conn == null) return $rechnungen;

    $conn->select_db('myreflex_test');
    $sql = 'select RECHNR from rechko where ZUAUFTRNR='. $pid .';';
    $res = $conn->query($sql);
    if($res)
    {
        while ($rechnr = $res->fetch_assoc())
        {
            $rechnungen[] = $rechnr['RECHNR'];
        }
    }
    return $rechnungen;
}

?>