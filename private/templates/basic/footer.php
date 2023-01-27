<?php

require_once 'component.php';

class Footer extends Component {

    function __construct($request, $session, $application) {

        parent::__construct($request, $session, $application);

    }

    function reset() {
        
    }

    function bootstrap() {

    }

    function get() {

        echo <<<EOT
        <!-- custom content -->
        </div>
        </div>
        </main>
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
                let table = disabled.nextElementSibling.nextElementSibling.nextElementSibling;
                let id = disabled.getAttribute('id');
                console.log(id);
                const log = table.querySelector(':scope .enabled-' + id);
                log.style.backgroundColor = '#ffeb3bd6';
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
        </body>
        </html>
        <!-- end custom content -->
        EOT;
        
    }

    function post() {
  
    }
    
}