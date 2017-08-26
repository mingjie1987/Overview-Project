
var tab_index;

/********************************************************/
$(function ()
{
    tab_index = 0;
    $("#previous").prop('disabled', true);
    $("#wizard-t-0").addClass("first current");
    //
    /*$("#ins-title").val("MyTitle");
    $("#ins-name").val("paxido_test");*/
    $("#exp-date").val(formatDate(new Date(), 'dd.MM.yyyy'));
    //
    /*$("#db_host").val("192.168.77.4");
    $("#db_user").val("root");
    $("#db_pwd").val("fjk0394FDS$$_1");*/
    $("#db_host").val("localhost");
    $("#db_user").val("root");
    $("#db_pwd").val("");
});

/********************************************************/
function check_site_prop(){
    var val = $("#ins-title").val().trim();
    if(val == '')
        return "'Title' filed is required.";

    val = $("#ins-name").val().trim();
    if(val == '')
        return "'Name' filed is required.";

    val = $("#exp-date").val().trim();
    if(isDate(val, 'dd.MM.yyyy') == false)
        return "'Date' filed is required or input error.";

    //val = $("#wildcard").val().trim();
    val = $( "#wildcard option:selected" ).text()
    if(val == '')
        return "'Wildcard' filed is required.";

    return "";
}

/********************************************************/
function check_db_prop(){
    var val1 = $("#db_host").val().trim();
    if(val1 == '')
        return "'Host' filed is required.";

    var val2 = $("#db_user").val().trim();
    if(val2 == '')
        return "'User' filed is required.";

    var val4 = $("#db_name").val().trim();
    if(val4 == '')
        return "'DB Name' filed is required.";

    return "";
}

/********************************************************/
function connection_check(){
    var checked = true;
    var val1 = $("#db_host").val().trim();
    var val2 = $("#db_user").val().trim();
    var val3 = $("#db_pwd").val().trim();
    var val4 = $("#db_name").val().trim();

    var data = {
        action: 'connection_check',
        host: val1,
        user: val2,
        pwd: val3,
        db: val4
    }
    $.ajax({
        type: "POST",
        url: 'class.setup.php',
        data: data,
        dataType : 'json',
        async: false,
        success:function(data) {
            console.log(data);
            checked = data.success;
        },
        error: function(data)
        {
            console.log(data);
            checked = false;
        }
    });
    return checked;
}


/********************************************************/
function Previous_Click() {
    if(tab_index == 0) return;

    $("#next").prop('disabled', false);
    $("#next").text((tab_index == 4) ? "Copy" : "Next");
    $("#wizard-p-" + tab_index).css("display", "none");
    $("#wizard-t-" + tab_index).removeClass("current");
    $("#wizard-t-" + tab_index).addClass("disabled");
    $("#result").html("");

    tab_index--;
    $("#wizard-p-" + tab_index).css("display", "block");

    if(tab_index == 0){
        $("#previous").prop('disabled', true);
    }
}


/********************************************************/
function Next_Click(path) {
    if(tab_index == 4) return;
    if(tab_index == 2){
        var checked = check_site_prop();
        if(checked != ""){
            $.alert({
                title: 'Warning',
                icon: 'fa fa-warning',
                type: 'blue',
                content: checked + '<hr>',
                buttons: {
                    okay: {
                        text: 'Ok',
                        btnClass: 'btn-blue'
                    }
                }
            });
            return;
        }
    }
    if(tab_index == 3){
        var checked = check_db_prop();
        if(checked != ""){
            $.alert({
                title: 'Warning',
                icon: 'fa fa-warning',
                type: 'blue',
                content: checked + '<hr>',
                buttons: {
                    okay: {
                        text: 'Ok',
                        btnClass: 'btn-blue'
                    }
                }
            });
            return;
        }

        checked = connection_check();
        if(checked == false){
            $.alert({
                title: 'Warning',
                icon: 'fa fa-warning',
                type: 'blue',
                content: "It can't connect to inputed host.\nPlease confirm inputed values." + '<hr>',
                buttons: {
                    okay: {
                        text: 'Ok',
                        btnClass: 'btn-blue'
                    }
                }
            });
            return;
        }
    }

    $("#previous").prop('disabled', false);
    $("#wizard-p-" + tab_index).css("display", "none");

    tab_index++;
    $("#wizard-p-" + tab_index).css("display", "block");
    $("#wizard-t-" + tab_index).removeClass("disabled");
    $("#wizard-t-" + tab_index).addClass("current");

    if(tab_index == 3){
        $("#next").text("Copy");
        $("#db_name").val($("#ins-name").val());
    } else if(tab_index == 4){
        $("#previous").prop('disabled', true);
        $("#next").prop('disabled', true);
        $("#back1").prop('disabled', true);

        start_install(path);
    }
}

/********************************************************/
function start_install(path){

    $("#img_processing").css("display","block");
    $("#p-title").text("Copying... Please wait.");
    $("#result").html("copying...");

    var val1 = $("#ins-title").val().trim();
    var val2 = $("#ins-name").val().trim();
    var val3 = $("#exp-date").val().trim();
    var val4 = $("#project-num").val().trim();
    var val5 = $( "#wildcard option:selected" ).text();

    var val6 = $("#db_host").val().trim();
    var val7 = $("#db_user").val().trim();
    var val8 = $("#db_pwd").val().trim();
    var val9 = $("#db_name").val().trim();

    setTimeout(function(){
        var data = {
            action: 'start_install',
            from: path,
            title: val1,
            name: val2,
            date: val3,
            pronum: val4,
            wildcard: val5,
            host: val6,
            user: val7,
            pwd: val8,
            db: val9
        }
        $.ajax({
            type: "POST",
            url: 'class.setup.php',
            data: data,
            dataType : 'json',
            success:function(data) {
                console.log(data);
                location.href = '/index.php';
            },
            error: function(data)
            {
                console.log(data);
                process_completion("Faild setup. Please try again.", data.content);
            }
        });
    }, 1000);

}

/********************************************************/
function process_completion(msg, content){
    $("#img_processing").css("display","none");
    $("#p-title").text(msg);
    $("#result").html(content);
    $("#previous").prop('disabled', false);
    $("#back1").prop('disabled', false);
}



/*******************************************************/
/*******************************************************/
/*******************************************************/
/*******************************************************/
/*******************************************************/
/*******************************************************/
/*******************************************************/
function show_details(id) {
    var table_id = "#" + id;
    var displayed = $(table_id).css("display");
    if(displayed == "none")
        $(table_id).css("display", "block");
    else
        $(table_id).css("display", "none");
}

/********************************************************/
function move_click(from){
    var to = $( "#to_dir option:selected" ).text();
    var root = get_last_dir(decodeURIComponent(from));

    if(root == "") return;
    if(from.indexOf(to) > -1){
        $.alert({
            title: 'Warning',
            icon: 'fa fa-warning',
            type: 'blue',
            content: "Domain directory is the same.<br>So it can not move to destination. <br/>Please check domain directory." + '<hr>',
            buttons: {
                okay: {
                    text: 'Ok',
                    btnClass: 'btn-blue'
                }
            }
        });
        return;
    }

    $("#move").prop('disabled', true);
    $("#back2").prop('disabled', true);

    $("#img_processing").css("display","block");
    $("#p-title").text("Moving... Please wait.");

    var data = {
        action: 'start_move',
        from: from,
        to: to,
        root: root
    }
    $.ajax({
        type: "POST",
        url: 'class.setup.php',
        data: data,
        dataType : 'json',
        success:function(data) {
            location.href = '/index.php';
        },
        error: function(data)
        {
            console.log(data);
            moving_completion("Faild Moving. Please try again.");
        }
    });
}

/********************************************************/
function get_last_dir(from) {
    var last_dir = "";
    var paths = from.split('/');
    var size = paths.length;

    for(var i = size - 1; i >= 0; i--){
        last_dir = paths[i].trim();
        if(last_dir != "") break;
    }
    return last_dir;
}

/********************************************************/
function moving_completion(msg){
    $("#img_processing").css("display","none");
    $("#p-title").text(msg);
    $("#previous").prop('disabled', false);
    $("#back1").prop('disabled', false);
}