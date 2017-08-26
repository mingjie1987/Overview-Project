<?php


$action = 'noAction';
$title = '';
$name = '';
$date = '';
$pronum = '';
$wildcard = '';
$r_host = '';
$r_user = '';
$r_pwd = '';
$r_db = '';
$from = '';
$to = '';
$dir = '';

if (isset($_POST['action']))    $action     = $_POST['action'];
if (isset($_POST['host']))      $r_host     = $_POST['host'];
if (isset($_POST['user']))      $r_user     = $_POST['user'];
if (isset($_POST['pwd']))       $r_pwd      = $_POST['pwd'];
if (isset($_POST['db']))        $r_db       = $_POST['db'];
if (isset($_POST['title']))     $title      = $_POST['title'];
if (isset($_POST['name']))      $name       = $_POST['name'];
if (isset($_POST['date']))      $date       = $_POST['date'];
if (isset($_POST['pronum']))    $pronum     = $_POST['pronum'];
if (isset($_POST['wildcard']))  $wildcard   = $_POST['wildcard'];
if (isset($_POST['from']))      $from       = urldecode($_POST['from']) ;
if (isset($_POST['to']))        $to         = $_POST['to'];
if (isset($_POST['root']))      $dir        = $_POST['root'];

if($action != "noAction"){
    $lSetup = new lcoreSetup();
    if($action == 'start_install'){
        $lSetup->start_install($from, $title, $name, $date, $pronum, $wildcard, $r_host, $r_user, $r_pwd, $r_db);
    }
    if($action == 'connection_check'){
        $lSetup->connection_check($r_host, $r_user, $r_pwd, $r_db);
    }
    if($action == 'start_move'){
        $lSetup->start_move($from, $to, $dir);
    }
}

define("MYSQL_LOCAL_PASSWORD","");

class lcoreSetup {

    private $FROM_PATH = '';
    private $TO_PATH = '';
    private $TITLE = '';
    private $NAME = '';
    private $DATE = '';
    private $PRONUM = '';
    private $WILDCARD = '';
    private $REMOTE_HOST = '';
    private $REMOTE_USER = '';
    private $REMOTE_PWD = '';
    private $REMOTE_DB = '';
    private $LOCAL_HOST = '';
    private $LOCAL_USER = '';
    private $LOCAL_PWD = '';
    private $LOCAL_DB = '';
    private $RCONN = null;
    private $LCONN = null;
    private $OS_CHECK = 'window';

    /*********************************************************
     * lcoreSetup constructor.
     * @param $path
     */
    function __construct(){

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $this->OS_CHECK = 'windows';
        } else {
            $this->OS_CHECK = 'linux';
        }
    }

    /*********************************************************
     * @param $host
     * @param $user
     * @param $pwd
     * @param $db
     */
    function connection_check($host, $user, $pwd, $db){
        $connected = true;
        $link = new mysqli($host, $user, $pwd);
        if ( $link->connect_error )
            $connected = false;

        $link->close();
        header("Content-type: text/plain;charset=utf-8");
        $s = array();
        $s['success'] = $connected;
        echo json_encode($s);
    }

    /*********************************************************
     * @param $p
     * @return array
     */
    function getDirectories($root)
    {
        //$root = $this->FROM_PATH;
        $fns = array();
        $invisibleFileNames = array(".", "..", ".svn", ".idea");
        if ($handle = opendir($root))
        {
            while (false !== ($file = readdir($handle)))
            {
                if(!in_array($file, $invisibleFileNames)){
                    if (is_dir($root . '/'.$file) && $file != '.' && $file != '..') $fns[] = $file;
                }
            }
            closedir($handle);
        }
        sort($fns);
        return $fns;
    }

    /**********************************************************
     * description: get all files from set directory.
     * @param $rootDir
     * @param $flag =>   $flag==true: file   $flag==false: folder
     * @param array $allData
     * @return array
     */
    public function getAllfilesFromDir($rootDir, $flag = true, $allData=array()) {
        // set filenames invisible if you want
        $invisibleFileNames = array(".", "..", ".svn", ".idea");
        // run through content of root directory
        $dirContent = scandir($rootDir);
        foreach($dirContent as $key => $content) {
            // filter all files not accessible
            $path = $rootDir.'/'.$content;
            if(!in_array($content, $invisibleFileNames)) {
                // if content is file & readable, add to array
                if(is_file($path) && is_readable($path)) {
                    // save file name with path
                    if($flag == true){
                        $allData[] = $path;
                    }
                    // if content is a directory and readable, add path and name
                }elseif(is_dir($path) && is_readable($path)) {
                    // recursive callback to open new directory
                    if($flag == false){
                        $allData[] = $path;
                    }
                    $allData = $this->getAllfilesFromDir($path, $flag, $allData);
                }
            }
        }
        return $allData;
    }

    /***********************************************************
     * @param $root
     * @return array
     */
    public function parse_etc($root)
    {
        //$root = $this->FROM_PATH;
        $config = array();

        $cfg_file =  $root."/etc/em.conf";

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
            echo "config-file not found....<br>";
            exit(2);
        }
        return $config;
    }

    /***************************************************************
     * @return bool
     */
    function CreateDirectoryStructure() {

        if( file_exists($this->TO_PATH) ){
            $this->remove_dir($this->TO_PATH);
        }

        $ret = mkdir($this->TO_PATH, 0777, true);
        if($ret == false) return $ret;

        $dirs = $this->getAllfilesFromDir($this->FROM_PATH, false);
        foreach($dirs AS $dir)
        {
            if($dir != "")
            {
                $tmp_dir = str_replace($this->FROM_PATH, $this->TO_PATH, $dir);
                $ret = mkdir($tmp_dir, 0777, true);
                if($ret == false) break;

            }
        }
        return $ret;
    }

    /*************************************************************
     * @param $dir
     */
    function remove_dir($dir) {

        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir."/".$object) == "dir")
                        $this->remove_dir($dir."/".$object);
                    else unlink   ($dir."/".$object);
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }

    /************************************************************
     * @return bool
     */
    function CopyAllFiles(){
        $ret = true;
        $files = $this->getAllfilesFromDir($this->FROM_PATH);
        foreach($files AS $file)
        {
            if($file != "")
            {
                $source = $file;
                $dest = str_replace($this->FROM_PATH, $this->TO_PATH, $source);
                $ret = copy($source, $dest);
                if($ret == false) break;
            }
        }
        return $ret;
    }

    /*********************************************************
     * @param $host
     * @param $user
     * @param $pwd
     * @return mysqli|null
     */
    function CreateDBConnection($host, $user, $pwd, $db = ''){
        $conn = new mysqli($host, $user, $pwd, $db);
        if ($conn->connect_error) {
            return null;
        }
        return $conn;
    }

    /************************************************************
     * @return bool
     */
    function CreateDatabases(){
        try{
            $local_dbs  = array($this->LOCAL_DB);
            $remote_dbs = array($this->REMOTE_DB);
            if($this->LOCAL_HOST != $this->REMOTE_HOST){
                array_push($local_dbs,  "em_depman", "em_translations");
                array_push($remote_dbs, "em_depman", "em_translations");
            }

            // create connection
            if($this->LCONN == null)
                $this->LCONN = $this->CreateDBConnection($this->LOCAL_HOST, $this->LOCAL_USER, $this->LOCAL_PWD);

            if($this->RCONN == null)
                $this->RCONN = $this->CreateDBConnection($this->REMOTE_HOST, $this->REMOTE_USER, $this->REMOTE_PWD);

            // Create database to remote
            foreach ($remote_dbs as $database){
                $sql = "DROP DATABASE ".$database;
                $this->RCONN->query($sql);

                $sql = "CREATE DATABASE ".$database;
                $this->RCONN->query($sql);
            }

            // Copy database
            for($i = 0; $i < count($remote_dbs); $i++){
                $this->CopyLocalDB_To_RemoteDB($this->LCONN, $this->RCONN, $local_dbs[$i], $remote_dbs[$i]);
            }

            // create functions
            $this->CreateFunctions();

            // close database
            $this->LCONN->close();  $this->LCONN = null;
            $this->RCONN->close();  $this->RCONN = null;

            return true;
            
        } catch (Exception $e){
            $this->LCONN->close();  $this->LCONN = null;
            $this->RCONN->close();  $this->RCONN = null;
            return false;
        }
            
    }

    /***************************************************************
     * @param $lconn
     * @param $rconn
     * @param $local_db
     * @param $remote_db
     */
    function CopyLocalDB_To_RemoteDB($lconn, $rconn, $local_db, $remote_db){
        $sql = "SHOW TABLES FROM ".$local_db;
        $result = $lconn->query($sql);
        if($result){
            while($row = $result->fetch_assoc())
            {
                //Drop table if exist
                $idx = "Tables_in_".strtolower ($local_db);
                $sql = "DROP TABLE IF EXISTS " . $remote_db . "." . $row[$idx];
                $rconn->query($sql);

                //Create new table
                $sql = "CREATE TABLE " . $remote_db . "." . $row[$idx] . " LIKE " . $local_db. "." . $row[$idx];
                $rconn->query($sql);

                //Insert data
                $sql = "INSERT INTO " . $remote_db . "." . $row[$idx] . " SELECT * FROM " . $local_db . "." . $row[$idx];
                $rconn->query($sql);
            }
            //mysqli_free_result($result);
        }
    }

    /****************************************************************************
     * @return bool
     */
    function UpdateRemoteDatabase(){
        try{
            if($this->RCONN == null)
                $this->RCONN = $this->CreateDBConnection($this->REMOTE_HOST, $this->REMOTE_USER, $this->REMOTE_PWD, $this->REMOTE_DB);

            // update temp db    1. sitetitle
            $sql = "SELECT * FROM em_mgmt WHERE opt_key='sitetitle';";
            $result = $this->RCONN->query($sql);
            if ($result->num_rows > 0)
                $sql = "UPDATE em_mgmt SET opt_value='".$this->TITLE."' WHERE opt_key='sitetitle';";
            else
                $sql = "INSERT INTO em_mgmt(opt_key, opt_value) VALUES('sitetitle', '".$this->TITLE."');";
            $this->RCONN->query($sql);

            // update temp db    2. date of expiration
            $sql = "SELECT * FROM em_mgmt WHERE opt_key='expaire_date';";
            $result = $this->RCONN->query($sql);
            if ($result->num_rows > 0)
                $sql = "UPDATE em_mgmt SET opt_value='".$this->DATE."' WHERE opt_key='expaire_date';";
            else
                $sql = "INSERT INTO em_mgmt(opt_key, opt_value) VALUES('expaire_date', '".$this->DATE."');";
            $this->RCONN->query($sql);

            // update temp db    3. product number
            $sql = "SELECT * FROM em_mgmt WHERE opt_key='reflex_auftragsnummer';";
            $result = $this->RCONN->query($sql);
            if ($result->num_rows > 0)
                $sql = "UPDATE em_mgmt SET opt_value='".$this->PRONUM."' WHERE opt_key='reflex_auftragsnummer';";
            else
                $sql = "INSERT INTO em_mgmt(opt_key, opt_value) VALUES('reflex_auftragsnummer', '".$this->PRONUM."');";
            $this->RCONN->query($sql);

            $this->RCONN->close();  $this->RCONN = null;
            return true;

        } catch (Exception $e){
            $this->RCONN->close();  $this->RCONN = null;
            return false;
        }
    }

    /****************************************************************************
     * @return bool
     */
    function CreateUserAndUpdatePrivilege($new_user, $new_pwd){
        try{
            if($new_user != ''){
                $conn = $this->CreateDBConnection($this->REMOTE_HOST, $this->REMOTE_USER, $this->REMOTE_PWD);

                // drop user
                $sql = "DROP USER '".$new_user."'@'".$this->REMOTE_HOST."';";
                $conn->query($sql);

                // create new user
                $sql = "CREATE USER '".$new_user."'@'".$this->REMOTE_HOST."' IDENTIFIED BY '".$new_pwd."';";
                $conn->query($sql);

                // update ALL PRIVILEGES
                $sql = "GRANT ALL PRIVILEGES ON ".$this->REMOTE_DB.".* TO '".$new_user."'@'".$this->REMOTE_HOST."' IDENTIFIED BY '".$new_pwd."' WITH GRANT OPTION;";
                $conn->query($sql);

                // em_depman
                $sql = "GRANT SELECT, INSERT, UPDATE ON em_depman.* TO '".$new_user."'@'".$this->REMOTE_HOST."' IDENTIFIED BY '".$new_pwd."' WITH GRANT OPTION;";
                $conn->query($sql);

                // em_translations
                $sql = "GRANT SELECT, INSERT, UPDATE ON em_translations.* TO '".$new_user."'@'".$this->REMOTE_HOST."' IDENTIFIED BY '".$new_pwd."' WITH GRANT OPTION;";
                $conn->query($sql);

                $conn->close();
            }
            return true;

        } catch (Exception $e){
            $conn->close();
            return false;
        }
    }

    /*********************************************************************
     *
     */
    function CreateFunctions(){
        $conn = $this->CreateDBConnection($this->REMOTE_HOST, $this->REMOTE_USER, $this->REMOTE_PWD, $this->REMOTE_DB);

        // getLevel4ParentId
        $sql = "CREATE DEFINER=`".$this->REMOTE_USER."`@`".$this->REMOTE_HOST."` FUNCTION `getLevel4ParentId`(`id_in` INT) RETURNS INT(11)\r";
        $sql.= "BEGIN\r";
        $sql.= "DECLARE levelnr INT;\r";
        $sql.= "SET levelnr = 0;\r";
        $sql.= "SELECT COUNT(*)  INTO levelnr\r";
        $sql.= "FROM  `nsc_tree` AS v,  `nsc_tree` AS s\r";
        $sql.= "WHERE s.`lft` BETWEEN v.`lft` AND v.`rgt`\r";
        $sql.= "AND s.`layout_id` = v.`layout_id`\r";
        $sql.= "AND s.`id` =id_in\r";
        $sql.= "GROUP BY s.`id`;\r";
        $sql.= "IF levelnr IS NULL THEN\r";
        $sql.= "SET levelnr = 0;\r";
        $sql.= "END IF;\r";
        $sql.= "RETURN levelnr;\r";
        $sql.= "END;";
        $conn->query($sql);

        // getLevelById
        $sql = "CREATE DEFINER=`".$this->REMOTE_USER."`@`".$this->REMOTE_HOST."` FUNCTION `getLevelById`(`id_in` INT) RETURNS INT(11)\r";
        $sql.= "BEGIN\r";
        $sql.= "DECLARE levelnr INT;\r";
        $sql.= "SET levelnr = 0;\r";
        $sql.= "SELECT COUNT(*)  INTO levelnr\r";
        $sql.= "FROM  `nsc_tree` AS v,  `nsc_tree` AS s\r";
        $sql.= "WHERE s.`lft` BETWEEN v.`lft` AND v.`rgt`\r";
        $sql.= "AND s.`layout_id` = v.`layout_id` \r";
        $sql.= "AND s.`id` =id_in \r";
        $sql.= "GROUP BY s.`id`;\r";
        $sql.= "IF levelnr IS NULL THEN\r";
        $sql.= "SET levelnr = 0;\r";
        $sql.= "END IF;\r";
        $sql.= "RETURN levelnr;\r";
        $sql.= "END;";
        $conn->query($sql);

        // getParentId
        $sql = "CREATE DEFINER=`".$this->REMOTE_USER."`@`".$this->REMOTE_HOST."` FUNCTION `getParentId`(`id_in` INT) RETURNS VARCHAR(255) CHARSET latin1\r";
        $sql.= "BEGIN\r";
        $sql.= "DECLARE parent_id INT; \r";
        $sql.= "DECLARE levelnr   INT; \r";
        $sql.= "DECLARE lft_out INT; \r";
        $sql.= "DECLARE rgt_out   INT;\r";
        $sql.= "DECLARE layout_id_out INT ; \r";
        $sql.= "SET parent_id = 0 ; \r";
        $sql.= "SET levelnr   = 0 ; \r";
        $sql.= "SET lft_out = 0 ; \r";
        $sql.= "SET rgt_out = 0 ; \r";
        $sql.= "SET layout_id_out = 0 ; \r";
        $sql.= "SELECT getLevel4ParentId(id),lft,rgt,layout_id  \r";
        $sql.= "INTO levelnr,lft_out,rgt_out ,layout_id_out  \r";
        $sql.= "FROM `nsc_tree`  \r";
        $sql.= "WHERE `id` = id_in; \r";
        $sql.= "SELECT `id` \r";
        $sql.= "INTO parent_id \r";
        $sql.= "FROM `nsc_tree` \r";
        $sql.= "WHERE getLevel4ParentId(id) = (levelnr-1) \r";
        $sql.= "AND `lft` < lft_out \r";
        $sql.= "AND `rgt` > lft_out \r";
        $sql.= "AND `layout_id` = layout_id_out; \r";
        $sql.= "IF parent_id IS NULL THEN \r";
        $sql.= "SET parent_id = 0; \r";
        $sql.= "END IF; \r";
        $sql.= "RETURN parent_id;\r";
        $sql.= "END;";
        $conn->query($sql);

        $conn->close();
    }
    /************************************************************
     * @return bool
     */
    function RewritePaxidoConfig($new_user, $new_pwd)
    {
        $user = ($new_user == '') ? $this->REMOTE_USER : $new_user;
        $pwd  = ($new_user == '') ? $this->REMOTE_PWD  : $new_pwd;
        try{
            // update /etc/em.conf file
            $cur_date = date("M/d/Y");
            $en_file = $this->TO_PATH."/etc/em.conf";
            $file=fopen($en_file, "w");

            $new_config ="------------------------------------------------------------------------\n";
            $new_config.="---------------------( Date:    $cur_date    )--------------------------\n";
            $new_config.="------------------------------------------------------------------------\n";
            $new_config.="sql-host=".$this->REMOTE_HOST."\n";
            $new_config.="sql-user=".$user."\n";
            $new_config.="sql-passwd=".$pwd."\n";
            $new_config.="sql-database=".$this->REMOTE_DB."\n";
            $new_config.="sql_database_privatfelder=".$this->REMOTE_DB."_privatfelder\n";
            $new_config.="sql_db_proj=emuebersicht\n";
            $new_config.="sql_proj_user=projects\n";
            $new_config.="sql_proj_pw=sunshine\n";
            $new_config.="project_id=@@@project_id@@@";

            fwrite($file,$new_config);
            fclose($file);

            // update /lcore/.env file
            $en_file = $this->TO_PATH."/lcore/.env";
            $file=fopen($en_file, "w");

            $new_config ="APP_NAME=Laravel\n";
            $new_config.="APP_ENV=local\n";
            $new_config.="APP_KEY=base64:A3FJq9Bjg7AAwJ8L/dWO9a7TcfzPo951Ox25gxBCUAc=\n";
            $new_config.="APP_DEBUG=true\n";
            $new_config.="APP_LOG_LEVEL=debug\n";
            $new_config.="APP_URL=https://to.do\n";
            $new_config.="\n";
            $new_config.="DB_CONNECTION=mysql\n";
            $new_config.="DB_HOST=".$this->REMOTE_HOST."\n";
            $new_config.="DB_PORT=3306\n";
            $new_config.="DB_DATABASE=".$this->REMOTE_DB."\n";
            $new_config.="DB_USERNAME=".$user."\n";
            $new_config.="DB_PASSWORD=".$pwd."\n";
            $new_config.="\n";
            $new_config.="BROADCAST_DRIVER=log\n";
            $new_config.="CACHE_DRIVER=file\n";
            $new_config.="SESSION_DRIVER=file\n";
            $new_config.="QUEUE_DRIVER=sync\n";
            $new_config.="\n";
            $new_config.="REDIS_HOST=127.0.0.1\n";
            $new_config.="REDIS_PASSWORD=null\n";
            $new_config.="REDIS_PORT=6379\n";
            $new_config.="\n";
            $new_config.="MAIL_DRIVER=smtp\n";
            $new_config.="MAIL_HOST=smtp.mailtrap.io\n";
            $new_config.="MAIL_PORT=2525\n";
            $new_config.="MAIL_USERNAME=null\n";
            $new_config.="MAIL_PASSWORD=null\n";
            $new_config.="MAIL_ENCRYPTION=null\n";
            $new_config.="\n";
            $new_config.="PUSHER_APP_ID=\n";
            $new_config.="PUSHER_APP_KEY=\n";
            $new_config.="PUSHER_APP_SECRET=\n";

            fwrite($file,$new_config);
            fclose($file);
            
            return true;
        } catch (Exception $e){
            return false;
            
        }
    }


    /*************************************************************
     * 
     */
    public function start_install($from, $title, $name, $date, $pronum, $wildcard, $host, $user, $pwd, $db){
        $this->FROM_PATH = $from;
        $config = $this->parse_etc($from);

        $this->LOCAL_HOST   = $config['sql_host'];
        $this->LOCAL_USER   = $config['sql_user'];
        $this->LOCAL_PWD    = $config['sql_passwd'];
        $this->LOCAL_DB     = $config['sql_database'];
        $this->TITLE        = $title;
        $this->NAME         = $name;
        $this->DATE         = $date;
        $this->PRONUM       = $pronum;
        $this->WILDCARD     = $wildcard;
        $this->REMOTE_HOST  = $host;
        $this->REMOTE_USER  = $user;
        $this->REMOTE_PWD   = $pwd;
        $this->REMOTE_DB    = $db;
        $this->TO_PATH = ($name == '') ? dirname($_SERVER['DOCUMENT_ROOT'])."/".$wildcard
                                       : dirname($_SERVER['DOCUMENT_ROOT'])."/".$wildcard."/".$name;

        $new_user = "user_".$name;
        $new_pwd = "";
        $success = true;
        $content = '';

        //====================================================
        $ret = $this->step_site_copy();
        if($ret == true){
            $content.= "<b>- Complete site copying.</b><br />&nbsp;&nbsp;&nbsp;Copying Direcotory: ".$this->TO_PATH."<br />";
        } else {
            $content.= "<b>- In copying dite, it occured error.</b><br />";
            $success = false;
        }
            

        //====================================================
        $ret = $this->step_db_copy($new_user, $new_pwd);
        if($ret == true){
            $content.= "<b>- Complete duplication of database.</b><br />&nbsp;&nbsp;&nbsp;DB Name: ".$this->REMOTE_DB."<br />";
        } else {
            $content.= "<b>- In duplicating db, it occured error.</b><br />";
            $success = false;
        }

        //====================================================
        $ret = $this->step_config_update($new_user, $new_pwd);
        if($ret == true) {
            $content.= "<b>- Complete configration.</b><br />&nbsp;&nbsp;&nbsp;File: /etc/em.conf , /lcore/.env <br />";
        } else {
            $content.= "<b>- In changing config, it occured error.</b><br />";
            $success = false;
        }

        header("Content-type: text/plain;charset=utf-8");
        $s = array();
        $s['success'] = $success;
        $s['content'] = $content;
        echo json_encode($s);
    }

    /*************************************************************
     * @return bool
     */
    function step_site_copy(){

        $ret = $this->CreateDirectoryStructure();
        if(!$ret) return false;

        $ret = $this->CopyAllFiles();
        if(!$ret) return false;

        return true;
    }

    /************************************************************
     * @return bool
     */
    function step_db_copy($n_user, $n_pwd){

        if($this->LOCAL_HOST == $this->REMOTE_HOST && $this->LOCAL_DB == $this->REMOTE_DB)
            return true;

        $ret = $this->CreateDatabases();
        if(!$ret) return false;

        $ret = $this->UpdateRemoteDatabase();
        if(!$ret) return false;

        $ret = $this->CreateUserAndUpdatePrivilege($n_user, $n_pwd);
        if(!$ret) return false;

        return true;
    }

    /***************************************************************
     * @return bool
     */
    function step_config_update($n_user, $n_pwd){
        $ret = $this->RewritePaxidoConfig($n_user, $n_pwd);
        if(!$ret) return false;

        return true;
    }


    /***************************************************************
     * @param $from
     * @param $to
     * @param $dir
     */
    public function start_move($from, $to, $dir)
    {
        $this->FROM_PATH = $from;
        $this->TO_PATH = ($dir == '') ? dirname($_SERVER['DOCUMENT_ROOT']) . "/" . $to
                                      : dirname($_SERVER['DOCUMENT_ROOT']) . "/" . $to . "/" . $dir;
        $success = true;

        $ret = $this->step_site_copy();
        if ($ret == true) {
            $this->remove_dir($this->FROM_PATH);
        } else {
            $success = false;
        }

        header("Content-type: text/plain;charset=utf-8");
        $s = array();
        $s['success'] = $success;
        echo json_encode($s);
    }
}