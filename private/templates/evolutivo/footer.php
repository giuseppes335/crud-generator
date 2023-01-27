<?php

require_once 'relation.php';

class Footer extends Relation {

    function __construct(Array $params) {

        parent::__construct($params);

    }

    function get() {

        echo <<<EOT
        <script>
        function addListener(id, name, url, destination_id) {
            let el = document.getElementById(id);
            el.addEventListener("change", function() {
                url = url_with_new_param(url, name, this.value);
                ajax(url, destination_id);
            });
        }
        let disableds = document.getElementsByClassName('disabled');
        if (disableds) {
            for(i = 0; i < disableds.length; i++) {
                let disabled = disableds[i];
                let selected = disabled.dataset.selected;
                const trs = disabled.parentNode.querySelectorAll(':scope tr');
                for(j = 0; j < trs.length;j++) {
                    let tr = trs[j];
                    if (tr.dataset.id === selected) {
                        tr.style.backgroundColor = '#ffeb3bd6';
                    }
                }
            }
        }
        function SetCaretAtEnd(elem) {
            var elemLen = elem.value.length;
            // For IE Only
            if (document.selection) {
                // Set focus
                elem.focus();
                // Use IE Ranges
                var oSel = document.selection.createRange();
                // Reset position to 0 & then set at end
                oSel.moveStart('character', -elemLen);
                oSel.moveStart('character', elemLen);
                oSel.moveEnd('character', 0);
                oSel.select();
            }
            else if (elem.selectionStart || elem.selectionStart == '0') {
                // Firefox/Chrome
                elem.selectionStart = elemLen;
                elem.selectionEnd = elemLen;
                elem.focus();
            } // if
        }
        function ajax(ajax_call, id) {
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
              if (this.readyState == 4 && this.status == 200) {
                document.getElementById(id).innerHTML = this.responseText;
              }
            };
            xhttp.open("GET", ajax_call, true);
            xhttp.send();
        }
        function popup_on(ajax_call) {
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
              if (this.readyState == 4 && this.status == 200) {
                document.getElementById("popup-content").innerHTML = this.responseText;
                document.getElementById("overlay").style.visibility = "visible";
              }
            };
            xhttp.open("GET", ajax_call, true);
            xhttp.send();
            
        }
        function popup_off() {
            document.getElementById("overlay").style.visibility = "hidden";
        }
        function url_with_new_param(url, field, value) {
            let url0 = new URL(url);
            url0.searchParams.set(field, value);
            return url0.href;
        }
        function url_exists_param(url, field) {
            let url0 = new URL(url);
            let i = 0;
            for (const [key, value] of url0.searchParams.entries()) {

                if (key == field) {
                    i++;
                }
                
            }
            if (i > 0) {
                return true;
            } else {
                return false;
            }
        }
        function remove_url_param(url, field) {
            let url0 = new URL(url);
            url0.searchParams.delete(field);
            return url0.href;
        }
        function toggle_content_position(x) {
            let sidebar = document.getElementById('sidebar');
            let content = document.getElementById('content');
            let close_button = document.getElementById('close-button');
            let toggle_button = document.getElementById('toggle-button');
            if (x.matches) {
                sidebar.style.left = '-280px';
                //sidebar.style.zIndex = -3000;
                content.style.paddingLeft = '16px';
                //close_button.style.display = 'inline-block';
                toggle_button.style.display = 'inline-block';
            } else {
                sidebar.style.left = '0px';
                sidebar.style.zIndex = 3000;
                content.style.paddingLeft = '296px';
                close_button.style.display = 'none';
                toggle_button.style.display = 'none';
            }
        } 
        var x = window.matchMedia("(max-width: 767.98px)")
        toggle_content_position(x)
        x.addListener(toggle_content_position)
        function toggleSidebar() {
            let sidebar = document.getElementById('sidebar');
            if (sidebar.style.left === '0px') {
                sidebar.style.left = '-280px';
                //sidebar.style.zIndex = -3000;
            } else {
                sidebar.style.left = '0px';
                sidebar.style.zIndex = 3000;
            }
        }
        function closeSidebar() {
            let sidebar = document.getElementById('sidebar');
            sidebar.style.left = '-280px';
            //sidebar.style.zIndex = -3000;
        }
        document.addEventListener('transitionend', () => {
            let sidebar = document.getElementById('sidebar');
            let close_button = document.getElementById('close-button');
            if (sidebar.style.left === '-280px') {
                sidebar.style.zIndex = -3000;
                close_button.style.display = 'inline-block';
            }
        });
        </script>
EOT;
        
    }

    function post() {

    }

    function put() {

    }

    function delete() {
        
    }
    
}