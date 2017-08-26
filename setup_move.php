<?php

include ("class.setup.php");

$from_path = '';
if (isset($_GET['path']))  $from_path   = $_GET['path'];
if($from_path == '')
    $from_path = $_SERVER['DOCUMENT_ROOT'];

$setupClass = new lcoreSetup($from_path);
$dir_info = $setupClass->getDirectories($from_path);
$config_info = $setupClass->parse_etc($from_path);

?>


<!DOCTYPE html>
<head>
    <title>Paxido setup Wizard</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="//netdna.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">
    <script type="text/javascript" src="//code.jquery.com/jquery-1.10.2.min.js"></script>
    <script type="text/javascript" src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.js"></script>

<!--    <link type="text/css" href="css/bootstrap.css" rel="stylesheet">-->
<!--    <link type="text/css" href="css/main.css" rel="stylesheet">-->
<!--    <link type="text/css" href="css/jquery.steps.css" rel="stylesheet">-->
<!--    <link type="text/css" href="css/jquery-confirm.css" rel="stylesheet">-->
<!--    <script type="application/javascript" src="js/jquery-1.9.1.min.js"></script>-->
    <script type="application/javascript" src="js/date.js"></script>
<!--    <script type="application/javascript" src="js/jquery-confirm.js"></script>-->
    <script type="application/javascript" src="setup.js"></script>

    <style>
        /*
    Common
*/

        .wizard,
        .tabcontrol
        {
            display: block;
            width: 100%;
            overflow: hidden;
            margin-top: 20px;
        }

        .wizard a,
        .tabcontrol a
        {
            outline: 0;
        }

        .wizard ul,
        .tabcontrol ul
        {
            list-style: none !important;
            padding: 0;
            margin: 0;
        }

        .wizard ul > li,
        .tabcontrol ul > li
        {
            display: block;
            padding: 0;
        }

        /* Accessibility */
        .wizard > .steps .current-info,
        .tabcontrol > .steps .current-info
        {
            position: absolute;
            left: -999em;
        }

        .wizard > .content > .title,
        .tabcontrol > .content > .title
        {
            position: absolute;
            left: -999em;
        }



        /*
            Wizard
        */

        .wizard > .steps
        {
            position: relative;
            display: block;
            width: 100%;
        }

        .wizard.vertical > .steps
        {
            display: inline;
            float: left;
            width: 30%;
        }

        .wizard > .steps .number
        {
            font-size: 1.429em;
        }

        .wizard > .steps > ul > li
        {
            width: 20%;
        }

        .wizard > .steps > ul > li,
        .wizard > .actions > ul > li
        {
            float: left;
        }

        .wizard.vertical > .steps > ul > li
        {
            float: none;
            width: 100%;
        }

        .wizard > .steps a,
        .wizard > .steps a:hover,
        .wizard > .steps a:active
        {
            display: block;
            width: auto;
            margin: 0 0.5em 0.5em;
            padding: 1em 1em;
            text-decoration: none;

            -webkit-border-radius: 5px;
            -moz-border-radius: 5px;
            border-radius: 5px;
        }

        .wizard > .steps .disabled a,
        .wizard > .steps .disabled a:hover,
        .wizard > .steps .disabled a:active
        {
            background: #dcdcdc;
            border: 1px solid #9e9e9e;
            color: #aaa;
            cursor: default;
        }

        .wizard > .steps .current a,
        .wizard > .steps .current a:hover,
        .wizard > .steps .current a:active
        {
            background: #2184be;
            color: #fff;
            cursor: default;
        }

        .wizard > .steps .done a,
        .wizard > .steps .done a:hover,
        .wizard > .steps .done a:active
        {
            background: #9dc8e2;
            color: #fff;
        }

        .wizard > .steps .error a,
        .wizard > .steps .error a:hover,
        .wizard > .steps .error a:active
        {
            background: #ff3111;
            color: #fff;
        }

        .wizard > .content
        {
            background: #dcdcdc;
            display: block;
            margin: 0.5em;
            min-height: 30em;
            overflow: hidden;
            position: relative;
            width: auto;

            -webkit-border-radius: 5px;
            -moz-border-radius: 5px;
            border-radius: 5px;
        }

        .wizard.vertical > .content
        {
            display: inline;
            float: left;
            margin: 0 2.5% 0.5em 2.5%;
            width: 65%;
        }

        .wizard > .content > .body
        {
            float: left;
            position: absolute;
            width: 100%;
            height: 95%;
            padding: 2.5%;
        }

        .wizard > .content > .body ul
        {
            list-style: disc !important;
        }

        .wizard > .content > .body ul > li
        {
            display: list-item;
        }

        .wizard > .content > .body > iframe
        {
            border: 0 none;
            width: 100%;
            height: 100%;
        }

        .wizard > .content > .body input
        {
            display: block;
            border: 1px solid #ccc;
        }

        .wizard > .content > .body input[type="checkbox"]
        {
            display: inline-block;
        }

        .wizard > .content > .body input.error
        {
            background: rgb(251, 227, 228);
            border: 1px solid #fbc2c4;
            color: #8a1f11;
        }

        .wizard > .content > .body label
        {
            display: inline-block;
            margin-bottom: 0.5em;
        }

        .wizard > .content > .body label.error
        {
            color: #8a1f11;
            display: inline-block;
            margin-left: 1.5em;
        }

        .wizard > .actions
        {
            position: relative;
            display: block;
            text-align: right;
            /*width: 100%;*/
        }

        .wizard.vertical > .actions
        {
            display: inline;
            float: right;
            margin: 0 2.5%;
            width: 95%;
        }

        .wizard > .actions > ul
        {
            display: inline-block;
            text-align: right;
        }

        .wizard > .actions > ul > li
        {
            margin: 0 0.5em;
        }

        .wizard.vertical > .actions > ul > li
        {
            margin: 0 0 0 1em;
        }

        .wizard > .actions a,
        .wizard > .actions a:hover,
        .wizard > .actions a:active
        {
            background: #2184be;
            color: #fff;
            display: block;
            padding: 0.5em 1em;
            text-decoration: none;

            -webkit-border-radius: 5px;
            -moz-border-radius: 5px;
            border-radius: 5px;
        }

        .wizard > .actions .disabled a,
        .wizard > .actions .disabled a:hover,
        .wizard > .actions .disabled a:active
        {
            background: #dcdcdc;
            border: 1px solid #9e9e9e;
            color: #aaa;
        }

        .wizard > .loading
        {
        }

        .wizard > .loading .spinner
        {
        }



        /*
            Tabcontrol
        */

        .tabcontrol > .steps
        {
            position: relative;
            display: block;
            width: 100%;
        }

        .tabcontrol > .steps > ul
        {
            position: relative;
            margin: 6px 0 0 0;
            top: 1px;
            z-index: 1;
        }

        .tabcontrol > .steps > ul > li
        {
            float: left;
            margin: 5px 2px 0 0;
            padding: 1px;

            -webkit-border-top-left-radius: 5px;
            -webkit-border-top-right-radius: 5px;
            -moz-border-radius-topleft: 5px;
            -moz-border-radius-topright: 5px;
            border-top-left-radius: 5px;
            border-top-right-radius: 5px;
        }

        .tabcontrol > .steps > ul > li:hover
        {
            background: #edecec;
            border: 1px solid #bbb;
            padding: 0;
        }

        .tabcontrol > .steps > ul > li.current
        {
            background: #fff;
            border: 1px solid #bbb;
            border-bottom: 0 none;
            padding: 0 0 1px 0;
            margin-top: 0;
        }

        .tabcontrol > .steps > ul > li > a
        {
            color: #5f5f5f;
            display: inline-block;
            border: 0 none;
            margin: 0;
            padding: 10px 30px;
            text-decoration: none;
        }

        .tabcontrol > .steps > ul > li > a:hover
        {
            text-decoration: none;
        }

        .tabcontrol > .steps > ul > li.current > a
        {
            padding: 15px 30px 10px 30px;
        }

        .tabcontrol > .content
        {
            position: relative;
            display: inline-block;
            width: 100%;
            height: 35em;
            overflow: hidden;
            border-top: 1px solid #bbb;
            padding-top: 20px;
        }

        .tabcontrol > .content > .body
        {
            float: left;
            position: absolute;
            width: 95%;
            height: 95%;
            padding: 2.5%;
        }

        .tabcontrol > .content > .body ul
        {
            list-style: disc !important;
        }

        .tabcontrol > .content > .body ul > li
        {
            display: list-item;
        }
    </style>
    <style>
        html,
        button,
        input,
        select,
        textarea {
            color: #222;
        }

        body {
            font-size: 1.5em;
            line-height: 1.4;
        }
        ::-moz-selection {
            background: #b3d4fc;
            text-shadow: none;
        }

        ::selection {
            background: #b3d4fc;
            text-shadow: none;
        }

        hr {
            display: block;
            height: 1px;
            border: 0;
            border-top: 1px solid #ccc;
            margin: 1em 0;
            padding: 0;
        }
        img {
            vertical-align: middle;
        }

        fieldset {
            border: 0;
            margin: 0;
            padding: 0;
        }
        textarea {
            resize: vertical;
        }

        .alignCenter {
            text-align: center;
        }

        .center {
            margin: auto;
            width: 80%;
            margin-top: 20px;
        }

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
            background-color:#aaaaaa;
            font-weight:bold;
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

        #result {
            text-align: left;
            padding: 10px 50px;
            border: 1px solid #000;
            height: 13em;
            margin: 20px 50px;
            background-color: white;
            line-height: 30px;
        }

        .progress {
            padding: 10px 50px;
            border: 1px solid #999;
            border-radius: 5px;
            height: 13em;
            margin: 20px 50px;
            background-color: white;
            line-height: 30px;
        }

        .moving_back {
            margin-top: 50px;
            width: 80%;
            margin-left: 10%;
            padding: 50px;
            background-color: #dcdcdc;
            border-radius: 10px;
        }
        .ir {
            background-color: transparent;
            border: 0;
            overflow: hidden;
            /* IE 6/7 fallback */
            *text-indent: -9999px;
        }

        .ir:before {
            content: "";
            display: block;
            width: 0;
            height: 150%;
        }
        .hidden {
            display: none !important;
            visibility: hidden;
        }
        .visuallyhidden {
            border: 0;
            clip: rect(0 0 0 0);
            height: 1px;
            margin: -1px;
            overflow: hidden;
            padding: 0;
            position: absolute;
            width: 1px;
        }

        .visuallyhidden.focusable:active,
        .visuallyhidden.focusable:focus {
            clip: auto;
            height: auto;
            margin: 0;
            overflow: visible;
            position: static;
            width: auto;
        }
        .invisible {
            visibility: hidden;
        }
        .clearfix:before,
        .clearfix:after {
            content: " "; /* 1 */
            display: table; /* 2 */
        }

        .clearfix:after {
            clear: both;
        }
        .clearfix {
            *zoom: 1;
        }
        @media only screen and (min-width: 35em) {
            /* Style adjustments for viewports that meet the condition */
        }

        @media print,
        (-o-min-device-pixel-ratio: 5/4),
        (-webkit-min-device-pixel-ratio: 1.25),
        (min-resolution: 120dpi) {
            /* Style adjustments for high resolution devices */
        }
        @media print {
            * {
                background: transparent !important;
                color: #000 !important; /* Black prints faster: h5bp.com/s */
                box-shadow: none !important;
                text-shadow: none !important;
            }

            a,
            a:visited {
                text-decoration: underline;
            }

            a[href]:after {
                content: " (" attr(href) ")";
            }

            abbr[title]:after {
                content: " (" attr(title) ")";
            }
            .ir a:after,
            a[href^="javascript:"]:after,
            a[href^="#"]:after {
                content: "";
            }

            pre,
            blockquote {
                border: 1px solid #999;
                page-break-inside: avoid;
            }

            thead {
                display: table-header-group; /* h5bp.com/t */
            }

            tr,
            img {
                page-break-inside: avoid;
            }

            img {
                max-width: 100% !important;
            }

            @page {
                margin: 0.5cm;
            }

            p,
            h2,
            h3 {
                orphans: 3;
                widows: 3;
            }

            h2,
            h3 {
                page-break-after: avoid;
            }
        }

    </style>
</head>

<body>
<div class="content center" style="">
    <div style="margin-top: 50px;"></div>
    <h1 class="alignCenter t-shadow">Paxido Move Wizard</h1>

    <div id="wizard" class="wizard clearfix">
        <div class="content clearfix" style="background-color: #fff;">
            <section id="wizard-p-0" class="body current">
                <table class="table table-bordered">
                    <tbody>
                    <tr>
                        <td style='text-align: right;color: #051851; width: 40%;background-color: transparent;'><b>Source Dir:</b></td>
                        <td style='text-align: left;background-color: transparent;'><b><?php echo $from_path; ?></b></td>
                    </tr>
                    <tr>
                        <td style='text-align: right;color: #051851;background-color: transparent;'><b>Destination Dir:</b></td>
                        <td style="background-color: transparent;">
                            <select class="form-control" id="to_dir" style="width:70%">
                                <option>my.paxido.com</option>
                                <option>gse.paxido.com</option>
                                <option>sv.paxido.com</option>
                                <option>ibm.paxido.com</option>
                                <option>ibm-ch.paxido.com</option>
                                <option>events.paxido.com</option>
                                <option>toshiba.paxido.com</option>
                                <option>lenovo.paxido.com</option>
                                <option>hds.paxido.com</option>
                                <option>bwi.paxido.com</option>
                                <option>huawei.paxido.com</option>
                                <option>philips.paxido.com</option>
                            </select>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <section id="wizard-p-4" class="body">
                    <div class="alignCenter" style="padding-top: 30px">
                        <h2 id="p-title" class="title"></h2>
                        <img id="img_processing" src="css/processing.gif" style="margin-top: 20px; width: 100px; margin: auto; display: none;" />
                    </div>
                </section>
            </section>
        </div>

        <div class="actions clearfix" style="margin: 15px 10px;">
            <button id="back2" class="btn btn-warning" style="width: 100px; float: left;" onclick="location.href='index.php';">Back</button>
            <button id="move" class="btn btn-success" style="width: 100px;float:right;" onclick="move_click('<?php echo urlencode($from_path); ?>');">Move</button>
        </div>
    </div>
</body>
</html>
