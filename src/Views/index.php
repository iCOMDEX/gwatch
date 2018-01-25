<?php 
#######################################################################
## The disclosed code and program is subject to copyright, 
## patent and other intellectual property protections.  
## This publication of the code does not represent a license 
## or permission to use it or any part of it, and is not a 
## grant of permission to modify or make derivative works of 
## the code or program.  If you would like to make 
## modifications or use it for commercial or non-commercial uses, 
## please email Dr. Stephen J. O'Brien at lgdchief@gmail.com with your 
## request, affiliation, intended use, and contact information, 
## and Dr. O'Brien will contact you.
#######################################################################

error_reporting(1); 
require_once( 'bits/connect.php'); 
//require_once( '../includes/functions.php'); 
//require_once( 'panel/include/config.php'); 
?>
<html>
    
    <head>
        <title>GWATCH - Genome Wide Association Tracks Chromosome Highway</title>

        <meta name="description" content="GWATCH is a web based genome browser designed to automate analysis, visualization and data release of Whole Genome Sequence and GWAS variant" />
        <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
        
        <script type="text/javascript" src="http://code.jquery.com/jquery-1.7.2.min.js"></script>
        <script type="text/javascript" src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
            <script type="text/javascript" src="js/jquery.mousewheel.min.js"></script>
    <script type="text/javascript" src="js/ui.spinner.min.js"></script>
    <script type="text/javascript" src="js/json2.js"></script>
    <script type="text/javascript" src="js/glMatrix-0.9.5.min.js"></script>
    <script type="text/javascript" src="js/jquery.cookie.js"></script>
    <script type="text/javascript" src="js/vtip.js"></script>
    <script type="text/javascript" src="js/GLU.js"></script>
    <script type="text/javascript" src="scripts/addEvent.js"></script>
    <script type="text/javascript" src="scripts/main.js"></script>
    <script type="text/javascript" src="scripts/glhelpers.js"></script>
    <script type="text/javascript" src="scripts/rowCache.js"></script>
    <script type="text/javascript" src="scripts/road.js"></script>
    <script type="text/javascript" src="scripts/font.js"></script>
         <script type="text/javascript" src="js/jquery.address-1.5.min.js"></script>
    
        <link type="text/css" href="css/styles.css" rel="stylesheet">

        <script>
            (function(i, s, o, g, r, a, m) {
                i['GoogleAnalyticsObject'] = r;
                i[r] = i[r] || function() {
                    (i[r].q = i[r].q || []).push(arguments)
                }, i[r].l = 1 * new Date();
                a = s.createElement(o),
                m = s.getElementsByTagName(o)[0];
                a.async = 1;
                a.src = g;
                m.parentNode.insertBefore(a, m)
            })(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');

            ga('create', 'UA-11277812-8', 'gen-watch.org');
            ga('send', 'pageview');
        </script>
        <style>
            html, body {
                margin:0;
                padding:0;
                height:100%;
                font-family: Lucina Grande, Verdana;
                min-height:100%;
                position:relative;
                background-color: #F3F3F3;
                line-height:1.5;
                color: #2E3332;
            }
            pre {
                background-color:dddddd;
                padding:10px;
                padding-left:20px;
                margin-left:30px;
            }
            .red {
                color:red;
            }

          div.links {
                min-height:60px;
                display: table;
                width:100%;
                padding:0px;
                background: #f3f3f3;
                border-bottom:1px solid #E9E8F1;
            }            
            div#tabs {
                width:100%;
                background-color:white;
                bottom: 0;
                left: 0;
                padding:0px;
                /*padding-bottom:-80px; /* Height of the footer element */
                right: 0;
                font-family: Verdana;
                border-bottom: 1px solid #E9E8F1;
                border-top: 1px solid #E9E8F1;
            }
            p {
                text-align:justify;
                margin-left:50px;
                margin-right:50px;
            }
            .links ul {
                list-style-type: none;
                padding-left: 40px;
                display: list-item;
                margin:0px;
            }
            a {
                text-decoration:none;
                color:#2E3332;
            }
            a:hover {
                text-decoration:underline;
            }
            .links li {
                list-style-type: none;
                float:left;
                padding:20px 15px;
            }            
            div#contents {
                text-align:justify;
                padding-left:15px;
                padding-right:15px;
            }
            #contents ul {
                list-style: none;
            }
            div#option0 {
                padding-top:15%;
                text-align:center;
                vertical-align: middle;
            }
   
            .form {
                height:30px;
                font-size:20px;
                border:1px solid;
                border-radius:6px;
                margin:15px;
            }
            #first {
                height: 100px;
                width: 100px;
                position: relative;
                background: red;
            }
            #second {
                height: 100px;
                width: 100px;
                position: relative;
                background: green;
            }
            #third {
                height: 100px;
                width: 100px;
                position: relative;
                background: yellow;
            }
            #fourth {
                height: 100px;
                width: 100px;
                position: relative;
                background: cyan;
            }
             div#option4 h3 {
                background: white;
                border: 1px solid #cccccc;
                padding:8px;
                font-size:12px;
                border-radius:6px;
                margin: 10px 0px;
                position:relative;
            }
            div#option4 h3.edit {
                border-color: rgb(189, 219, 200);
                background-color: rgb(245, 250, 237);
            }
            div#option4 li.edit a, ul.edit a, ol.edit a {
                text-decoration: underline;
            }
            div#option4 li.edit a:hover, ul.edit a:hover, ol.edit a:hover {
                color: rgb(48, 149, 84);
                text-decoration: underline;
            }
            div#option4 h3.view {
                border-color: rgb(248, 224, 154);
                background-color: rgb(255, 249, 231);
            }
            div#option4 li.view a, ul.view a, ol.view a {
                text-decoration: underline;
            }
            div#option4 li.view a:hover, ul.view a:hover, ol.view a:hover {
                color: rgb(207, 167, 39);
                text-decoration: underline;
            }
            div#option4 h3.description {
                border-color: rgb(231, 162, 148);
                background-color: rgb(255, 242, 239);
            }
            div#option4 li.description a, ul.description a, ol.description a {
                text-decoration: underline;
            }
            div#option4 li.description a:hover, ul.description a:hover, ol.description a:hover {
                color: rgb(185, 14, 14);
                text-decoration: underline;
            }
            div#option4 h3.demo {
                border-color: rgb(165, 182, 231);
                background-color: rgb(245, 248, 255);
            }
            div#option4 li.demo a, ul.demo a, ol.demo a {
                text-decoration: underline;
            }
            div#option4 li.demo a:hover, ul.demo a:hover, ol.demo a:hover {
                color: rgb(34, 71, 179);
                text-decoration: underline;
            }

 
            div#option9 ul{padding: 0px;}           
            div#option4 ul li.edit {
                border-color: rgb(189, 219, 200);
                background-color: rgb(245, 250, 237);
            }
           div#option9 li.edit{
                border: 1px solid #cccccc;
                padding:8px;
                margin-top: 12px;
                font-size:12px;
                border-radius:6px;           
                border-color: rgb(189, 219, 200);
                background-color: rgb(245, 250, 237);
                font-weight: bold;
            }
            div#option9 li.edit a:hover, ul.edit a:hover, ol.edit a:hover {
                color: rgb(48, 149, 84);
                text-decoration: underline;
            }
             div#option17 li.edit{
                border: 1px solid #cccccc;
                padding:8px;
                margin-top: 12px;
                font-size:12px;
                border-radius:6px;           
                border-color: rgb(189, 219, 200);
                background-color: rgb(245, 250, 237);
                font-weight: bold;
            }
            div#option17 li.edit a:hover, ul.edit a:hover, ol.edit a:hover {
                color: rgb(48, 149, 84);
                text-decoration: underline;
            }
             div#option18 li.edit{
                border: 1px solid #cccccc;
                padding:8px;
                margin-top: 12px;
                font-size:12px;
                border-radius:6px;           
                border-color: rgb(189, 219, 200);
                background-color: rgb(245, 250, 237);
                font-weight: bold;
            }
            div#option18 li.edit a:hover, ul.edit a:hover, ol.edit a:hover {
                color: rgb(48, 149, 84);
                text-decoration: underline;
            }
             div#option20 li.edit{
                border: 1px solid #cccccc;
                padding:8px;
                margin-top: 12px;
                font-size:12px;
                border-radius:6px;           
                border-color: rgb(189, 219, 200);
                background-color: rgb(245, 250, 237);
                font-weight: bold;
            }
            div#option20 li.edit a:hover, ul.edit a:hover, ol.edit a:hover {
                color: rgb(48, 149, 84);
                text-decoration: underline;
            }
            div#option15 li.edit a:hover, ul.edit a:hover, ol.edit a:hover {
                color: rgb(48, 149, 84);
                text-decoration: underline;
            }
            div#option9 li.edit div.name_li{
                width:200px;
                margin-left:10px;
                margin-right:40px;
                float:left;
            }
            div#option9 ul.edit a{
                margin-left: 10px;
            }
           div#option9 ul#head {
                display:inline;
                margin-left: 50px;
                padding:0;
            }
  
            div#option9 ul#head li{
                display:inline;
                padding-right: 40px;
            }
           
            
 
            .form:hover {
                border-width: 1px;
                border-style: solid;
                border-color: rgb(208, 79, 53) rgb(65, 107, 236) rgb(245, 185, 15) rgb(48, 149, 84);
                border-top-left-radius: 7px;
                border-top-right-radius: 7px;
                border-bottom-right-radius: 7px;
                border-bottom-left-radius: 7px;
            }
            div#accordion {
                width:80%;
                padding-left:10%
            }
            a > img {
                border: 0;
            }
            h5{margin: 0px;}
            div#bad, div#bad-right-browser{margin-left: auto;margin-right: auto;width: 70%;}
            div#bad > a, div#bad-right-browser > a{color: rgb(65, 107, 236);}
            .orangebutton {padding:4px;  background:rgb(255, 174, 0); border-radius:4px;color: white;}
            .edit{
                border: 1px solid rgb(189, 219, 200);
                padding:8px;
                margin-top: 12px;
                font-size:12px;
                border-radius:6px;           
                background-color: rgb(245, 250, 237);
                font-weight: bold;
                margin-left: -6px !important;
            }
            
            .view {
                border: 1px solid rgb(189, 219, 200);
                padding:8px;
                margin-top: 12px;
                font-size:12px;
                border-radius:6px;                 
                font-weight: bold;
                margin-left: -6px !important;            
                border-color: rgb(248, 224, 154);
                background-color: rgb(255, 249, 231);
            }
            
            }
        </style>
        <script type="text/javascript">
            function showhide(div) {
                if ($("#" + div).is(":hidden")) {

                    $("#" + div).show("slow");

                } else {

                    $("#" + div).hide("slow");

                }

            }






            function getXmlHttp() {
                var xmlhttp;
                try {
                    xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
                } catch (e) {
                    try {
                        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
                    } catch (E) {
                        xmlhttp = false;
                    }
                }
                if (!xmlhttp && typeof XMLHttpRequest != 'undefined') {
                    xmlhttp = new XMLHttpRequest();
                }
                return xmlhttp;
            }


            function ac() {

                var activate = true;
                $("#accordion").accordion({
                    collapsible: true,
                    active: activate,
                    heightStyle: "content"
                });

            }

			 
 
            function executeElements(obj) {
                var head = document.getElementsByTagName('head')[0];
                var scripts = obj.getElementsByTagName('script');
                var inputs = obj.getElementsByTagName('input');
                var textareas = obj.getElementsByTagName('textarea');
                var selects = obj.getElementsByTagName('select');
                var getreq = '?';

                for (var i = 0; i < inputs.length; i++) {

                    var input = document.createElement('input');
                    input.type = inputs[i].type;
                    input.name = inputs[i].name;
                    if (input.type != 'radio') {
                        input.value = inputs[i].value;

                    }
                    input.checked = inputs[i].checked;

                    if (input.type == 'checkbox') {
                        if (input.checked != 0) input.value = 1
                        else input.value = 0
                    }


                    if (input.type == 'radio') {
                        if (input.checked == true) input.value = inputs[i].value;
                        else continue
                    }




                    getreq = getreq + input.name + '=' + input.value + "&"


                }
                for (var i = 0; i < textareas.length; i++) {

                    var textarea = document.createElement('textarea');
                    textarea.name = textareas[i].name;
                    textarea.value = textareas[i].value;
                    getreq = getreq + textarea.name + '=' + textarea.value + "&"

                }

                for (var i = 0; i < selects.length; i++) {


                    getreq = getreq + selects[i].name + '=' + selects[i].value + "&"

                }
                //alert(getreq)
                return getreq;
            }


            function executeScripts(obj) {
                var head = document.getElementsByTagName('head')[0];

                var scripts = obj.getElementsByTagName('script');
                for (var i = 0; i < scripts.length; i++) {

                    eval(scripts[i].innerHTML)

                    if (scripts[i].src != '') {

                        var script = document.createElement('script');
                        script.type = "text/javascript";
                        script.src = scripts[i].src;
                        head.appendChild(script);

                        scripts[i].src = '';
                    }
                }
            }

            function changeform($q, $q1) {




            }

            function vote(page) {



                var req = getXmlHttp()


                var statusElem = document.getElementById('option9')
                req.onreadystatechange = function() {

                    statusElem.style.display = 'block';

                    if (req.readyState == 4) {

                        if (req.status == 200) {

                            statusElem.innerHTML = req.responseText
                            ac()
                            executeScripts(document.getElementById('option9'))

                            outTime();

                        } else {

                            statusElem.innerHTML = req.statusText // показать статус (Not Found, ОК..)

                        }
                    }

                }


                req.open('GET',  page, true);


                req.send(null);

                //statusElem.innerHTML = 'Loading...' 
            }




            function fill(page, div) {

                //alert(div)
                var req = getXmlHttp()
                var statusElem = document.getElementById(div)
                 
                req.onreadystatechange = function() {
                    if (req.readyState == 4) {
                        if (req.status == 200) {
                            statusElem.style.display = 'block';
                            statusElem.innerHTML = req.responseText
                            ac()
                            executeScripts(document.getElementById(div))
                        } else {
                            statusElem.innerHTML = req.statusText // показать статус (Not Found, ОК..)
                        }
                    }
                }
                req.open('GET',  page, true);
                //alert('http://gen-watch.org' +  page)
                req.send(null);
                statusElem.innerHTML = "<div  style=\"width:100%;text-align:center;\">Loading...<img src='images/loader.gif'/></div>"

            } 
        </script>
        
                <script type="text/javascript">
            var tabs,
                separator = '-',
                initialTab = 'option0',
                navSelector = '.links li a',
                tabSelector = '#tabs, #contents';
            $.address.wrap(true);
            if ($.address.value() == '/') {
                $.address.history(false).value(initialTab).history(true);
 
            }
            $.address.change(function(event) {

                // Selects the current anchor
                var current = $('a[href=#' + event.value.replace(/^\//, '').replace('/', separator) + ']:first'),
                    selection = current.attr('href'),
                    href = selection.replace('/', separator),
                    parts = href.split(separator),
                    subselection;
                    $('.box').hide();
                    $(href).show();
                    if (href!='option0') {
                    if ($('#small-logo').html()==='') {
                    $('#small-logo').html('<img src="/images/img-small.php" style="padding: 0px;" alt="Genome Wide Association Tracks Chromosome Highway" title="Genome Wide Association Tracks Chromosome Highway">');
                    }
                    }
                    else {$('#small-logo').html('');}
                // Sets the page title
                $.address.title($.address.title().split(' | ')[0] + ' | ' + current.text());
                if (!tabs) {
                    // Creates the tabs
                    
                    tabs = $(tabSelector).tabs({
                        event: 'change'
                    }).css('display', 'block');
                    tabs.find(navSelector).click(function(e) {
                    	 if (typeof e.target.hash === 'undefined') {
                    	 $.address.value('option0');
                    	 }
                    	 else {
                      	$.address.value(e.target.hash.replace(/^#/, ''));
                      	
                      	}
                        e.preventDefault();
                    });
                }

                // Selects the parent tabs
                if (parts.length != 1) {
                    for (var i = 1; i < parts.length; i++) {
                        tabs.tabs('select', parts.slice(0, i).join(separator));
                    }
                }
                
                // Selects the chosen tab
                tabs.tabs('select', selection);
                // Selects the first child tab
                while (subselection = $(selection + ' ' + navSelector + ':first').attr('href')) {
                    tabs.tabs('select', subselection);
                    selection = subselection;
                }  
            });

            // Hides the tabs during initialization
        </script>
<script>
</script>
<style>
    ul#head {text-align: center; 
        margin-left: auto !important;
    margin-right: auto !important;
    display: list-item;
    width: 500px}
    ul#head li{list-style-type: none;
    float: left;
    padding: 5px 15px !important; }
    #edit_title {
    font-size: inherit !important;
    font-weight: normal;
    width: 428px;
    display: block;
    color: white;
    margin-left: auto !important;
    margin-right: auto !important;
    border-radius: 8px 8px 0px 0px !important;
    text-align: left;
    border: 1px #00a2ac solid !important;
    background: #00a2ac !important;
    margin-top: 20 !important;
    margin-bottom: 0 !important;
    padding: 8px 40px !important;

    }
    #sel_user {
    font-size: inherit !important;
    font-weight: normal;
    width: 428px;
    display: block;
    color: white;
    margin-left: auto !important;
    margin-right: auto !important;
    border-radius: 0px 0px 0px 0px !important;
    text-align: left;
    border: 1px #00a2ac solid !important;
    background: #00a2ac !important;
    margin-top: 0 !important;
    margin-bottom: 0 !important;
    padding: 8px 40px !important;

    }
    #edit_form{
    width: 500px;
    display: block;
    margin-left: auto;
    margin-right: auto;
    border-radius: 0px 0px 8px 8px;
    text-align: left;
    border: 1px #00a2ac solid;
    background: white;
    border-top: 0px;
    margin-top: 0;
    margin-bottom: 20;
    padding: 4px;

    }
    #register_head {
        border:1px solid grey;
        border-color:#97b0c5;
        border-radius:8px 8px 0px 0px;
        background: #97b0c5;
        margin: 10px;
        font-weight:   normal;
        color:white;
        margin-bottom: 0px;
        padding: 9px;
        width:400px;
        margin-left: auto;
        margin-right: auto;
        text-align: center;

    }

    #register_form{
        border:1px solid;
        border-color: #97b0c5;
        border-radius:0px 0px 8px 8px;
        padding: 9px;
        margin:10px;
        margin-top:0px;
        border-top:0px;
        background: linear-gradient(to left, dddddd;, dddddd);
        width:400px;
        margin-left: auto;
        margin-right: auto;
    }
    #reg_table {border-collapse:collapse;}
    .input{
        height: 30px;
        border: 1px solid;
        border-radius: 7px;
        margin: 15px;
        background: #f3f3f3;
        width:90%;
}
    .input:hover {
        border-width: 1px;
        border-style: solid;
        border-color: rgb(208, 79, 53) rgb(65, 107, 236) rgb(245, 185, 15) rgb(48, 149, 84);
        border-top-left-radius: 3px;
        border-top-right-radius: 3px;
        border-bottom-right-radius: 3px;
        border-bottom-left-radius: 3px;
    }
    .input:focus{
        border-radius: 7px;
}
    .form:focus{
        border-radius: 7px;
}
    #reg_text{
        background: #f3f3f3; 
        background: -moz-linear-gradient(top, #f3f3f3 0%, #D9D9D9 38%, #f3f3f3 82%, #f3f3f3 100%); 
        background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#f3f3f3), color-stop(38%,#D9D9D9), color-stop(82%,#f3f3f3), color-stop(100%,#f3f3f3)); 
        background: -webkit-linear-gradient(top, #f3f3f3 0%,#D9D9D9 38%,#f3f3f3 82%,#f3f3f3 100%); 
        background: -o-linear-gradient(top, #f3f3f3 0%,#D9D9D9 38%,#f3f3f3 82%,#f3f3f3 100%); 
        background: -ms-linear-gradient(top, #f3f3f3 0%,#D9D9D9 38%,#f3f3f3 82%,#f3f3f3 100%); 
        background: linear-gradient(to bottom, #f3f3f3 0%,#D9D9D9 38%,#f3f3f3 82%,#f3f3f3 100%); 
        filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f3f3f3', endColorstr='#f3f3f3',GradientType=0 ); 
        display: block; 
        padding: 12px 10px; 
        color: #2E3332; 
        font-weight: bold; 
        text-shadow: 1px 1px 1px #FFF; 
        border: 1px solid rgba(16, 103, 133, 0.6); 
        box-shadow: 0px 0px 3px rgba(255, 255, 255, 0.5), inset 0px 1px 4px rgba(0, 0, 0, 0.2); 
        border-radius: 3px; 
         outline:0; 
        width:90%; 
    }
     #reg_text:hover {
        border-width: 1px;
        border-style: solid;
        border-color: rgb(208, 79, 53) rgb(65, 107, 236) rgb(245, 185, 15) rgb(48, 149, 84);
        border-top-left-radius: 7px;
        border-top-right-radius: 7px;
        border-bottom-right-radius: 7px;
        border-bottom-left-radius: 7px;
    }   
    #reg_text:focus{
        border-radius: 7px;
    }
    #validate{
        display: inline-block;
    }
    .form_row{
        border-radius:8px;
        border:1px solid;
    }

    
 .input  { 
background: #f3f3f3; 
background: -moz-linear-gradient(top, #f3f3f3 0%, #D9D9D9 38%, #E5E5E5 82%, #f3f3f3 100%); 
background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#f3f3f3), color-stop(38%,#D9D9D9), color-stop(82%,#E5E5E5), color-stop(100%,#f3f3f3)); 
background: -webkit-linear-gradient(top, #f3f3f3 0%,#D9D9D9 38%,#E5E5E5 82%,#f3f3f3 100%); 
background: -o-linear-gradient(top, #f3f3f3 0%,#D9D9D9 38%,#E5E5E5 82%,#f3f3f3 100%); 
background: -ms-linear-gradient(top, #f3f3f3 0%,#D9D9D9 38%,#E5E5E5 82%,#f3f3f3 100%); 
background: linear-gradient(to bottom, #f3f3f3 0%,#D9D9D9 38%,#E5E5E5 82%,#f3f3f3 100%); 
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f3f3f3', endColorstr='#f3f3f3',GradientType=0 ); 
display: block; 
padding: 12px 10px; 
color: #999; 
font-size: 1.2em; 
font-weight: bold; 
text-shadow: 1px 1px 1px #FFF; 
border: 1px solid rgba(16, 103, 133, 0.6); 
box-shadow: 0px 0px 3px rgba(255, 255, 255, 0.5), inset 0px 1px 4px rgba(0, 0, 0, 0.2); 
border-radius: 3px; 
 outline:0; 
width:90%; 
} 


</style>
    </head>
    
    <body>
        <canvas id="canvas" style="display:none"></canvas>
          <div class = 'links'>
            <ul>
            		<li><a href="#option0"><div id ='small-logo'></div></a>
                	</li>
                    <li><a href="#option1">What GWATCH does</a>
                    </li>
                    <li><a href="#option2">Features of GWATCH</a>
                    </li>
                    <li><a href="#option3">Tutorial</a>
                    </li>                    
                    <li><a href="#option4" onclick="fill('/bits/show_datasets.php', 'option4')">Active Datasets</a></li>         
                    <?php include( '../includes/login_w_history.php'); ?>
        

            </ul>
        </div>

         <div id="tabs">
                                 <div id="bad" style="display:none;">
  <h3>Unfortunately your browser does not appear to support WebGL.</h3>
  Browsers known to work are:<br/>
  Firefox version 4.0 and above. <a href="http://www.mozilla.com/en-US/firefox/new/">Download Firefox</a><br/>
  Chrome version 12 and above. <a href="http://www.google.com/chrome/">Download Chrome</a> <br/> <br/>
    
  Microsoft has not announced any plans to support WebGL. If you must use Internet Explorer then 
  you can use <a href="http://code.google.com/chrome/chromeframe/">Chrome Frame</a> which allows a window inside IE to be rendered
  with the Chrome rendering engine.
        <br/><br/>
   Safari 5.1 also works, but WebGL is not enabled by default.
   <a href="https://discussions.apple.com/thread/3300585?start=0&tstart=0">Instructions to Enable WebGL In Safari</a>
</div>
<div id="bad-right-browser" style="display:none;">
  <h3>Unfortunately your browser does not appear to support WebGL.</h3>
  You appear to be using a supported browser, but WebGL failed to initialise.<br/>
    Check you have the latest graphics drivers for your video card installed, and you do not have WebGL
    disabled in your browser settings.
</div>
   
          <div id="contents">


                <div id="option0" class = "box">
                </div>
                <div id="option1" class = "box" style="margin-left: auto;margin-right: auto;width: 70%;" >
                    <h3>What GWATCH does</h3>

                    <p>GWATCH is a web-based genome browser designed to automate analysis, visualization and release of data from genome-wide association studies (GWAS) and whole genome sequence association studies of genetic epidemiological cohorts. For any association study, GWATCH allows cataloging and viewing of significant statistical results of association tests (p-values, odds ratios, hazard ratios and others) for single or multiple variants (SNP, indels, CNV), for single or multiple tests.
                        <p>GWAS data are collected and subjected to quality control (call rates by individual and by SNP etc.) by the researchers. Statistical association tests are designed to help detect genetic differences among study groups with alternative phenotypes or disease outcomes. Each SNP-test combination includes information on patient counts by category, p-values and a Quantitative Association Statistic (QAS, a general term for statistics explaining direction and strength of associations: odds ratio, hazard ratio, relative hazard and so on, depending on the particular statistical test). An unabridged data file listing association test, categorical patient counts, p-values and QAS for each SNP-test combination comprises the initial input to GWATCH.
                            <p>It is also possible, even desirable, that each association test conducted is entered as an analytical routine, as well as SNP genotyping and clinical category designation of study participants. Note that all identifiable genotypic, demographic or clinical data are kept hidden to protect patient privacy permanently.
                                <p>The display <a href="#option2" style="color:blue;">features of GWATCH</a> allow for numerous views of the results which are routinely considered by genetic practitioners.
                                    <p>The promise of GWATCH falls into four general applications:
                                        <ol>
                                            <li>Automates gene association search and discovery analysis.
                                                <li>Advances display of results from Manhattan plots to 2D and 3D snapshots of any gene region and dynamic chromosome highway browser.
                                                    <li>Allows real time validation/replication of candidate and discovered genes from other sources limiting Bonferroni penalties.
                                                        <li>Offers solution to privacy constraints on unabridged data sharing and release.</ol>
                </div>
                <div id="option2" class = "box" style="margin-left: auto;margin-right: auto;width: 70%;" >
                    <h3>Features of GWATCH</h3>

                    <ol>
                        <li>Unabridged Data Table*
                            <li>Manhattan Plots for each Association Test across all SNPs
                                <li>SNAPSHOTS of SNP rest results in a chromosome region of SNP chromosome coordinates, MAF**, P-value Plus QAS***
                                    <ul>
                                        <li>2D Heat Plot Snapshot illustrating P-values and QAS of any selected chromosome region
                                            <li>3D checkerboard Plot Snapshot illustrating P-values and QAS of selected chromosome region
                                                <li>LD polarized 3D Checkerboard Snapshot illustrating P-values and QAS of any selected chromosome region
                                                    <li>Dynamic Highway View by Chromosome Browser illustrating P-value and QAS</ul>
                                    <li>TRAX Reports:
                                        <ul>
                                            <li>TRAX Summary page–one page graphic summary of QAS, P-values values for a selected SNP
                                                <li>TRAX analysis report- Full graphs, curves, tables and statistics for  all association tests for one selected SNP</ul>
                                        <li>Top association hits
                                            <ul>
                                                <li>ranked -log P-value
                                                    <li>ranked QAS**
                                                        <li>ranked Density of -log P-value within a SNP genomic region</ul>
                    </ol>
                    <p>Abbreviations:
                        <br>*TABLE - Unabridged Data Table of P-values vs. QAS for all SNPs genotyped
                        <br>**MAF - minor allele frequency 
                        <br>***QAS - Quantitative Association Statistic (OR, RH, EZ2 depending on Tests)
                        <br>
                </div>
                <div id="option3" class = "box" style="text-align:center" >
                        <h3>Tutorial</h3>

<iframe width="560" height="315" src="//www.youtube.com/embed/fIeOnZ-WLzo" frameborder="0" allowfullscreen></iframe>                </div>
                <div id="option4" class = "box" >
                     
                </div>
                <div id="option5" class = "box" style="margin-left: auto;margin-right: auto;width: 70%;" >
                    <h3>GWATCH paper</h3>
                   <ul>	
						<li class="edit"><a href="https://gigascience.biomedcentral.com/articles/10.1186/2047-217X-3-18">http://www.gigasciencejournal.com/</a>
						<li class="edit"><a href="pdf/GWATCH.pdf" target="_blank">GWATCH - A Web-platform    for Automated   Gene    Association Discovery   Analysis </a> 
               		
               		
										<li class="edit"><a href="pdf/Supp Table 1  Finished Big Table 100 lines PARD3B SNP Additional file 2.xlsx">Supp Table 1  Finished Big Table 100 lines PARD3B SNP.xlsx</a> 
										<li class="edit"><a href="pdf/Supp Table 2 Tests and Pats counts in Module A Additional file 3.xlsx">Supp Table 2 Tests and Pats counts in Module A.xlsx</a> 
										<li class="edit"><a href="pdf/Supp Table 3 Tests and pats counts in Module B Additional file 4.xlsx">Supp Table 3 Tests and pats counts in Module B.xlsx</a> 
										<li class="edit"><a href="pdf/Supp Table 4 Test and Pats counts for Module C Additional file 5.xlsx">Supp Table 4 Test and Pats counts for Module C.xlsx</a> 
										<li class="edit"><a href="pdf/Supp Table 5 Tests for Groups  A,B and C-Additional file 6.xlsx">Supp Table 5 Tests for Groups  A,B and C.xlsx</a> 
										<li class="edit"><a href="pdf/Supp Table 6 541 Hits in 241 genes replicated  in A,B or C -Additional file 7.xlsx">Supp Table 6 541 Hits in 241 genes replicated  in A,B or C.xlsx</a> 
										<li class="edit"><a href="pdf/Supp Table 7 HITS with pval ,QAS & densty ranks -Additional file 8.xlsx">Supp Table 7 HITS with pval ,QAS & densty ranks.xlsx</a> 
										<li class="edit"><a href="pdf/Supp Figs1-4 & Table 8-QC  Methods- Additional file 1.docx">Supp Figs1-4 & Table 8-QC  Methods.docx</a> 
										<li class="edit"><a href="pdf/Supp Fig  5 Trax page- Additional file 9.pdf">Supp Fig  5 Trax page.pdf</a> 
										<li class="edit"><a href="pdf/Supp Fig 6 TRAX Report PARD3B-Additional file 10.pdf">Supp Fig 6 TRAX Report PARD3B.pdf</a> 
               		               		
               		
               		</ul> 
                </div>
                <div id="option9" class = "box" style="margin-left: auto;margin-right: auto;width: 70%;" >
                        

                    <?php include( '../includes/uploadhelp.php');?>
                </div>
                <div id="option7" class = "box" style="margin-left: auto;margin-right: auto;width: 70%;" >
                        <h3>Contact us for support</h3>
                    <p> <strong>Support</strong>
                        <br/>support@gen-watch.org
                        <br/>
                        <br/><strong>Nikоlаy Сherkаsov</strong>
						<br/>x@biomed.spb.ru
						<br/>
						<br/><strong>Stephen OBrien </strong>
						<br/>Chief scientific officer
						<br/>lgdchief@gmail.com
                        <br/><br/>   <strong>Theodosius Dobzhansky Center for Genome Bioinformatics</strong>
                        <br/>   <a href="http://dobzhanskycenter.bio.spbu.ru/?lang=en" target=_blank>http://dobzhanskycenter.bio.spbu.ru/?lang=en</a>
                        <br />41A, Sredniy Av.
                        <br/>St. Petersburg State University
                        <br/>St. Petersburg, Russia
                        <br/>dobzhanskylab@gmail.com
                        <br/>+7 812 363-61-03</div>
                <div id="option8" class="box" style="margin-left: auto; margin-right: auto; width: 70%; display: none;" >
                       <?php
                       
                       if($_GET['login']=='error'){
                        
                       	PrintError("Incorrect login or password");
                       	
                       }
                       
                       ?>
                    	<h3 id="edit_title">Login form</h3>
                    <form action="/bits/ath.php" method="post" name="form" id="edit_form">
                        <p>ID or Email:
                        <br><input type="TEXT" NAME="email" VALUE="" SIZE="20" class="form">
                        <p>Password:
                        <br><input type="password" NAME="password" VALUE="" SIZE="20" class="form">                         
                        <p><input type="submit"  class="form" value="Login" name="button">
                    </form>
                </div>
           	<div id="option15" class='box' style="margin-left: auto;margin-right: auto;width: 500px;display: block;">
            </div> 
            <div id="option16" class='box' style="margin-left: auto;margin-right: auto;width: 500px;display: block;"> 
            
            
            
            </div> 
            <div id="option17" class='box' style="margin-left: auto;margin-right: auto;width: 500px;display: block;">
            </div> 
            <div id="option18" class='box' style="margin-left: auto;margin-right: auto;width: 500px;display: block;">
            </div> 
            <div id="option19" class='box' style="margin-left: auto;margin-right: auto;width: 500px;display: block;">
            </div> 
            <div id="option20" class='box' sstyle="margin-left: auto;margin-right: auto;width: 500px;display: block; ">
             	&nbsp;
            </div> 
            <div id="option21" class='box' sstyle="margin-left: auto;margin-right: auto;width: 500px;display: block;">
             	&nbsp;
            </div> 
                 </div> 
        </div>
         </div>
         <div class = 'links'>
            <ul style="line-height:0;padding: 0;">
                <li><a href="#option5">GWATCH paper</a>
                </li>
                <li><a href="#option9" onclick="fill('/bits/uploadhelp.php', 'option9')">Upload Dataset</a>
                </li>
                <li><a href="#option7">Contact us for support</a>
                </li>
                <li><a href="#option21" onclick="fill('/bits/feedback.php', 'option21')">Feedback</a>
                </li>

                

            </ul>
        </div>
        <script type="text/javascript">
            $("#accordion1").accordion({
                collapsible: true,
                active: false,
                heightStyle: "content"
            });

            $("#accordion").accordion({
                collapsible: true,
                active: false,
                heightStyle: "content"
            });
        </script>
        <script>
            $(window).resize(function() {
                var w = $(window).width();
                var h = $(window).height();
                h = h - 132;
                $("#tabs").css('min-height', h);
                $("#option0").css('padding-top', h / 3);

                //  $('#option0').html('<img src="http://gwas.dobzhanskycenter.ru/img-color.php?height='+h+'&width='+w+'" stylr="vertical-align: middle;" class="img-center">')

            });
        </script>
        <script>
            $(window).load(function() {
                var w = $(window).width();
                var h = $(window).height();
                h = h - 132;
                $("#tabs").css('min-height', h);
                //  $('#option0').html('<img src="http://gwas.dobzhanskycenter.ru/img-color.php?height='+h+'&width='+w+'" stylr="vertical-align: middle;" class="img-center">')

            });
        </script>
        <script>
            var winW = 630,
                winH = 460;
            if (document.body && document.body.offsetWidth) {
                winW = document.body.offsetWidth;
                winH = document.body.offsetHeight;
            }
            if (document.compatMode == 'CSS1Compat' && document.documentElement && document.documentElement.offsetWidth) {
                winW = document.documentElement.offsetWidth;
                winH = document.documentElement.offsetHeight;
            }
            if (window.innerWidth && window.innerHeight) {
                winW = window.innerWidth;
                winH = window.innerHeight;
            }
            //   document.getElementById("tabs").style.height=winH-40;
            //document.writeln('Window width = '+winW);
            //document.writeln('Window height = '+winH);
            document.getElementById("tabs").style.minHeight = winH - 120;
            winH = winH - 132
            var div = document.getElementById('option0');
            div.style.paddingTop = winH / 3;
            //alert(div.innerHTML) 
            div.innerHTML = '<img src="/images/img-color.php?height=' + winH + '&width=' + winW + '"  class="img-center"  alt="Genome Wide Association Tracks Chromosome Highway" title="Genome Wide Association Tracks Chromosome Highway">'
       </script>


       <script>
            var canvas = get("canvas");
            var gl;
            try 
            {
                gl = canvas.getContext("experimental-webgl");
                gl.viewportWidth = canvas.width;
                gl.viewportHeight = canvas.height;
            } catch(e) { };
            if (gl)  
            {
             
            }
            else
            {   
                var userAgent = navigator.userAgent;
                if ( ( userAgent.indexOf( "Firefox/") != -1 )
                 || ( userAgent.indexOf( "Chrome/") != -1 ) )
                {
                document.getElementById("contents").style.display = "none";    
                document.getElementById("bad-right-browser").style.display = "block";
                }
                else
                {
                document.getElementById("contents").style.display = "none"; 
                document.getElementById("bad").style.display = "block";
            
                }
            } 
            delete gl;
        </script>


    </body>

</html>