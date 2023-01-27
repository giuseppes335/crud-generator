<?php 

session_start([
    'cookie_lifetime' => 0
]);

$scripts = [];

require_once 'private/include/configuration.php';
require_once 'private/include/request.php';
require_once 'private/include/session.php';
require_once 'private/include/application.php';

$configuration = new Configuration();
$session = new Session();
$application = new Application($configuration);
$request = new Request($_GET, $_POST, $session, $application, $configuration);

?>

<!doctype html>
<html lang="it">
<head>

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Crud generator</title>
<meta description="Crud generator">
<style>

    @font-face {
        font-family: Roboto;
        src: url(css/fonts/roboto/Roboto-Regular.ttf);
    }

    html, body {
        height: 100%;
    }

    body {
        margin: 0;
        font-family: Roboto; 
        font-size: 18px;
    }

    body * {
        box-sizing: border-box;
    }

    .default-padding {
        padding: 16px;
    }

    .primary-bg-color {
        /*background-color: #454c8e;*/
        background-color: #fab63e;
    }

    .primary-font-color {
        color: #4c4c4c;
    }

    .secondary-font-color {
        color: #fff;
    }

    #section-1-img {

        background-image: url("/img/bg-versioni-demo.png");
        background-size: contain;
        background-repeat: no-repeat;

    }

    .index-section {
        max-width: 768px;
        margin: 50px auto;
    }
    @media screen and (max-width: 767px) {
        .index-section {
            margin: 4px;
        }
    }

    .form-section {
        max-width: 400px;
        margin: 0 auto;
    }

    .editor-section {
        max-width: 800px;
        margin: 0 auto;
    }



    .demo-field-container {
        padding: 16px;
        box-shadow: 4px 4px #fab63e52;
        border: 1px solid #4c4c4c;
        margin-top: 16px;
    }

    /* Forms */
    .form-item {
        width: 100%;
    }

    .form-item label, .form-item input[type=text], .form-item input[type=email], .form-item textarea, .form-item select {
        display: block;
        width: 100%;
    }

    .form-item input[type=text], .form-item input[type=email], .form-item select {
        height: 36px;
        line-height: 36px;
        font-size: 16px;
    }

    .form-item textarea {
        height: 150px;
        font-size: 16px;
    }

    .error {
        color: #cc0000;
    }

    .action {
        height: 36px;
        line-height: 36px;
        font-size: 16px;
        outline: none;
        background-color: #fab63e;
        color: #fff;
        border: none;
        font-weight: bold;
        margin-left: 40px;
    }

    .action:hover {
        cursor: pointer;
    }


    /* Buttons */
    button {
        padding: 0 32px;
        background-color: #4c4c4c;
        border: none;
        color: #fff;
        cursor: pointer;
    }

    button:hover {
        background-color: #292929;
    }

    button:active {
        background-color: #000;
    }

    .button-link {
        background-color: #fab63e;
        color: #fff;
        border: 1px solid #fab63e;
        display: inline-block;
        height: 40px;
        line-height: 40px;
        padding: 0 20px;
        text-decoration: none;
    }

    .button-link:hover {
        background-color: #eb9e16;
        color: #fff;
        border: 1px solid #eb9e16;
    }

    .button-link:active {
        background-color: #fab63e;
        color: #fff;
        border: 1px solid #fab63e;
    }  
    
    


    .button-link-step {
        background-color: #3f51b5;
        color: #fff;
        border: 1px solid #3f51b5;
        text-decoration: none;
        border-radius: 50%;
        width: 150px;
        height: 150px;
        font-weight: bold;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .button-link-step:hover {
        background-color: #fab63e52;
        color: #fff;
        border: 1px solid #fab63e52;
    }

    .button-link-step:active {
        background-color: #3f51b5;
        color: #fff;
        border: 1px solid #3f51b5;
    } 

    #toggle-button {
        height: 50px;
        cursor: pointer;
    }

    #close-button {
        cursor: pointer;
    }

    /* Tables */
    table  {
        width: 100%;
        padding: 32px;
        box-shadow: 4px 4px #fab63e52;
        border: 1px solid #4c4c4c;
        border-radius: 4px;
    }

    th, td {
        text-align: left;
        border-bottom: 1px solid #4c4c4c;
        height: 50px;
        line-height: 50px;
        vertical-align: middle;
    }

    .table-action-section {
        text-align: center;
    }

    .mobile {
        display: none;
    }

    @media screen and (max-width: 767px) {
        thead {
            display: none;
        }
        td {
            display: block;
            line-height: normal;
        }
        td span {
            display: block;
            font-size: 12px;
            font-weight: bold;
        }
        .table-action-section {
            text-align: left;
        }
        .mobile {
            display: block;
        }
    }

    /* Pre */

    pre {
        font-size: 16px;
        margin: 20px 0;
    }

    /* Layout */

    .one-column-mobile {
        padding: 100px 200px;
    }

    .two-column-mobile {
        width: 100%;
        padding: 100px 100px;
        display: flex;
    }

    .two-column-mobile-child {
        width: 100%; 
        padding: 0 100px; 
    }

    @media screen and (max-width: 1023px) {
        .one-column-mobile {
            padding: 16px 16px;
        }

        .two-column-mobile {
            padding: 16px 16px;
            flex-direction: column-reverse  ;
        }

        .two-column-mobile-child {
            width: 100%; 
            padding: 0; 
        }
    }

    .logo {
        text-decoration: none;
        color: #fff;
        line-height: 50px;
        margin-left: 200px;
        font-weight: bold;
    }

    @media screen and (max-width: 1023px) {
        .logo {
            margin-left: 16px;
        }
    }

    .sidebar {
        background-color: #4c4c4c;
        transition: left 0.5s;
        position: absolute; 
        left: 0;
        width: 280px; 
        height: 100%; 
    }

    .sidebar ul {
        list-style: none;
        padding: 0;
        width: 100%;
    }

    .sidebar li {
        list-style: none;
        display: inline-block;
        width: 100%;
        
    }

    .sidebar a {
        display: inline-block;
        width: 100%;
        padding: 8px 32px;
    }

    .sidebar li:hover {
        background-color: #fab63e52;
    }

    .content {
        position: relative;
        padding: 16px;
    }

    .bg-image {
        background-image: url("img/bg-versioni-demo.png");
        background-size: cover;
        color: #fff;
        position: relative;
    }

    .overlay {
        position: absolute;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0,0,0,0.5);
        z-index: 2;
        cursor: pointer;
    }

    .promotion {
        display: flex;
        justify-content: center;
        background-color: #fab63e;
        color: #fff;
        border-radius: 8px;
        padding: 32px;
    }

    .rounded {
        background-color: #fab63e;
        color: #fff;
        width: 75px;
        height: 75px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        font-size: 64px;
    }

    .template {
        max-width: 33%;
        background-color: #fab63e52;
        border-radius: 8px;
        padding: 16px;
    }

    .template-image-container {
        overflow: hidden;
        height: 100px;
    }

    .template-image-container img {
        width: 100%;
    }

    .stepper {
        padding: 0;
        margin: 0;

    }

    .stepper li {
        display: inline-flex;
        width: 32%;
        justify-content: center;
    }

    @media screen and (max-width: 1023px) {
        .stepper li {
            display: flex;
            width: 100%;
            margin-bottom: 16px;
        }
    }

    @media screen and (max-width: 1023px) {
        .template {
            max-width: 50%;
        }
    }

    @media screen and (max-width: 767px) {
        .template {
            max-width: 100%;
        }
    }

    /* Link */
    a {
        color: #fff;
        text-decoration: none;
    }
    .bold-on-hover:hover {
        font-weight: bold;
    }


    /*Filter styles*/
    .saturate { filter: saturate(3); }
    .grayscale { filter: grayscale(100%); }
    .contrast { filter: contrast(160%); }
    .brightness { filter: brightness(0.25); }
    .blur { filter: blur(3px); }
    .invert { filter: invert(100%); }
    .sepia { filter: sepia(100%); }
    .huerotate { filter: hue-rotate(180deg); }
    .rss.opacity { filter: opacity(50%); }



    .software-category {
        color: #4c4c4c ;
        font-weight: bold;
        font-style: italic;
        text-decoration: underline;
        line-height: 40px;
    }

    .arrow {
       width: 40px;
       height: 40px; 
    }

    .bg-pattern {
        background-color: #4c4c4c;
    }

</style>

</head>
<body style="display: flex; flex-direction: column">

<header class="primary-bg-color secondary-font-color" style="height: 50px; display: flex; align-items: center; justify-content: space-between;">
    <a class="logo" href="<?= $configuration->host; ?>">Crud generator</a> 
    <?php if($request->is_tool_panel()): ?>
    <a id="toggle-button" class="invert" style="margin-right: 16px;" onclick="toggleSidebar()"><img src="<?= $configuration->host; ?>/img/menu_FILL0_wght700_GRAD0_opsz48.png" alt=""></a>
    <?php endif; ?>
</header>

<main style="flex-grow: 1;">
    <?php if($request->is_tool_panel()): ?>
    <?php $demos = $application->get_demos($session->get_session_id()); ?>
    <div style="position: relative; height: 100%;">
        <div id="sidebar" class="sidebar">
            <div style="display: flex; flex-direction: column;">
                <div style="text-align: right;">
                    <a id="close-button" class="invert" onclick="closeSidebar()"><img src="<?= $configuration->host; ?>/img/close_FILL0_wght700_GRAD0_opsz48.png" alt=""></a>
                </div>
                <ul style="color: #fff;">
                    <li><a href="scegli-un-applicazione.php">Scegli un'applicazione</a></li>
                    <?php if ($demos): ?>
                    <li><a href="<?= $configuration->host; ?>/demo.php""><?= $demos[0]['name']; ?></a></li>
                    <?php endif; ?>
                    <li style="padding: 8px 32px;"><hr style="border: none; border-bottom: 1px solid #fff;"></li>
                    <li><a href="<?= $configuration->host; ?>">Esci</a></li>
                </ul>
            </div>
        </div>
        <div id="content" class="content">
            
    <?php endif; ?>