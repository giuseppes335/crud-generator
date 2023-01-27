<?php

require_once 'component.php';

class Header extends Component {

    function __construct($request, $session, $application, $lang, $menu, $title) {

        parent::__construct($request, $session, $application);

        $this->lang = $lang;

        $this->menu = $menu;

        $this->title = $title;

    }

    function reset() {

    }

    function bootstrap() {

        $this->menu->bootstrap();

    }

    function get() {

        $application_host = $this->application->host;

        $request_demo_id = $this->request->demo_id;

        $path = $this->application->path;

        ob_start();

        $this->menu->get();

        $menu = ob_get_contents();

        ob_end_clean();

        echo <<<EOT
        <!-- custom content -->
        <!doctype html>
        <html lang="$this->lang">
        <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>$this->title</title>
        <style>
        html, body {
            height: 100%;
        }
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            
        }
        body * {
            box-sizing: border-box;
        }
        .header, .footer {
            height: 50px;
            background-color: #3f51b5;
        }
        .logo {
            text-decoration: none;
            color: #fff;
            line-height: 50px;
            margin-left: 60px;
        }
        .logo:hover {
            font-weight: bold;
        }
        .sidebar {
            background-color: #c3deeb;
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
            display: block;
            width: 100%;
            padding: 8px 32px;
        }
        .sidebar li:hover {
            background-color: #ffeb3bd6;
        }
        .content {
            position: relative;
            padding: 16px;
        }
        .menu {
            list-style: none;
        }
        .menu a {
            color: #fff;
            text-decoration: none;
            line-height: 35px;
        }
        .menu a:hover {
            font-weight: bold;
        }
        .menu-index {
            background-color: #ffeb3bd6;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: inline-block;
            text-align: center;
            line-height: 35px;
            font-size: 20px;
        }
        .selected {
            font-weight: bold;
            background-color: #ffeb3bd6;
        }
        .table-container  {
            padding: 32px;
            box-shadow: 4px 4px #c3deeb;
            border: 1px solid #4c4c4c;
            border-radius: 4px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table th, .table td {
            text-align: left;
            border-bottom: 1px solid #4c4c4c;
            height: 50px;
            line-height: 50px;
            vertical-align: middle;
            word-break: break-all;
        }
        /*
        .table tbody tr:nth-child(even) {
            background: #fff;
        }
        */
        .table tbody {
            background: #d3d3d3;
        }
        
        .table td span {
            display: none;
        }
        .table-action-section {
            text-align: center!important;
        }
        .mobile {
            display: none;
        }
        @media screen and (max-width: 767px) {
            .table thead {
                display: none;
            }
            .table td {
                display: flex;
                flex-direction: column;
                justify-content: center;
                line-height: normal;
            }
            .table td span {
                display: block;
                font-size: 12px;
                font-weight: bold;
            }
            .table-action-section {
                display: block!important;
                text-align: left!important;
            }
            .mobile {
                display: block;
            }
        }
        .button-a {
            background-color: #3f51b5;
            color: #fff;
            border: 1px solid #3f51b5;
            display: inline-block;
            height: 40px;
            line-height: 40px;
            padding: 0 20px;
            text-decoration: none;
            font-weight: bold;
        }
        .button-a:hover {
            background-color: #607d8b;
            color: #fff;
            border: 1px solid #607d8b;
            cursor: pointer;
        }
        .button-a:active {
            background-color: #3f51b5;
            color: #fff;
            border: 1px solid #3f51b5;
        }
        .button {
            background-color: #3f51b5;
            color: #fff;
            border: 1px solid #3f51b5;
            height: 40px;
            line-height: 40px;
            padding: 0 20px;
            font-size: 16px;
            font-weight: bold;
        }
        .button:hover {
            background-color: #607d8b;
            color: #fff;
            border: 1px solid #607d8b;
            cursor: pointer;
        }
        .icon {
            width: 24px; 
            position: relative; 
            top: 5px;
        }
        .field {
            max-width: 600px;
        }
        .field label, .field input[type=text], .field select {
            display: block;
            width: 100%;
        }
        .field input[type=text], .field select {
            height: 40px;
            line-height: 40px;
            font-size: 16px;
            margin-bottom: 8px;
        }
        .field textarea {
            height: 300px;
            font-size: 16px;
            margin-bottom: 8px;
        } 
        .field select[multiple] {
            height: 300px;
        } 
        .filter-form {
            max-width: 100%;
            margin-top: 4px;
        }
        .filter-form div {
            min-height: 40px;
            margin-bottom: 2px;
        }
        .filter-form button {
            vertical-align: middle;
        }
        .filter-form select, .filter-form input[type=text] {
            margin: 0;
            display: inline-block;
            width: 300px;
            height: 40px;
            line-height: 40px;
            font-size: 16px;
            vertical-align: middle;
        }
        .filters-labels {
            margin-bottom: 2px;
        }
        .filters-labels span {
            display: inline-block;
            background-color: #96cde6;
            padding: 4px;
            border-radius: 4px;
            color: #fff;
            margin-right: 4px;
        }
        .actions {
            margin: 0;
            display: inline-block;
            height: 40px;
            line-height: 40px;
            font-size: 16px;
        }
        .circle {
            background-color: #ffeb3bd6;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            text-align: center;
            line-height: 35px;
            display: inline-block!important;
            text-decoration: none;
        }
        #overlay {
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0,0,0,0.1);
            z-index: 4000;
        }
        #popup{
            width: 600px;
            padding: 32px;
            background-color: #fff;
            box-shadow: 4px 4px #c3deeb;
            border: 1px solid #4c4c4c;
            border-radius: 4px;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%,-50%);
        }
        @media screen and (max-width: 767px) {
            #popup{
                width: 100%;
            }
        }
        #popup-content {
            width: 100%;
            height: 100%;
        }
        .lookup {
            display: none;
            position: absolute;
            border: 1px solid #4c4c4c;
            background-color: #fff;
            width: 100%;
        }
        .lookup div {
            padding: 4px;
        }
        .lookup div:hover, .lookup div:focus {
            cursor: pointer;
            background-color: #c3deeb;
            color: #fff;
        }
        .close-button {
            cursor: pointer;
        }
        .close-button img {
            width: 24px;
        }
        #toggle-button {
            height: 50px;
            cursor: pointer;
        }
        #close-button {
            cursor: pointer;
        }
        .link {
            color: #000;
        }
        .variante {
            line-height: 16px;
            vertical-align: middle;
        }
        .variante ul {
            margin: 0;
        }
        .disabled {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0,0,0,0.2);
            z-index: 2000;
            cursor: pointer;
        }
        .loader {
            width: 0px;
            height: 0px;
        }
        .scrollable {
            display: flex;
        }
        .scrollable-buttons {
            width: 24px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .arrows {
            padding: 0;
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
        .block {
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0,0,0,0.1);
            z-index: 5000;
        }
        </style>
        </head>
        <body style="display: flex; flex-direction: column">
        <header class="header" style="height: 50px; display: flex; align-items: center; justify-content: space-between;">
        <a class="logo" href="">$this->title</a>
        <a id="toggle-button" class="invert" style="margin-right: 16px;" onclick="toggleSidebar()"><img src="$application_host/img/menu_FILL0_wght700_GRAD0_opsz48.png" alt=""></a>
        </header>
        <main style="flex-grow: 1;">
        <div style="position: relative; height: 100%;">
        <div id="sidebar" class="sidebar">
        <div style="text-align: right;">
            <a id="close-button" class="invert" onclick="closeSidebar()"><img src="$application_host/img/close_FILL0_wght700_GRAD0_opsz48.png" alt=""></a>
        </div>
        $menu
        </div>
        <div id="content" class="content">
        <!-- end custom content -->
        EOT;
        
    }

    function post() {

    }
    
}