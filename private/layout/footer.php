    <?php if($request->is_tool_panel()): ?>
        </div>
    </div>
    <?php endif; ?>
</main>

<footer class="default-padding primary-bg-color secondary-font-color" style="text-align: center; font-size: 14px;">
    Crud generator
</footer>

<script>
    function select_demo(selected_demo) {
        let demos = document.getElementsByClassName('demo');
        for(let i = 0; i < demos.length; i++) {
            let demo = demos[i];
            if (selected_demo === demo.getAttribute('id')) {
                demo.style.display = 'block';
            } else {
                demo.style.display = 'none';
            }
        }
    }
    <?php if($request->is_tool_panel()): ?>
    function toggle_content_position(x) {
        let sidebar = document.getElementById('sidebar');
        let content = document.getElementById('content');
        let close_button = document.getElementById('close-button');
        let toggle_button = document.getElementById('toggle-button');
        if (x.matches) {
            sidebar.style.left = '-280px';
            //sidebar.style.zIndex = -1000;
            content.style.paddingLeft = '16px';
            //close_button.style.display = 'inline-block';
            toggle_button.style.display = 'inline-block';
        } else {
            sidebar.style.left = '0px';
            sidebar.style.zIndex = 1000;
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
            //sidebar.style.zIndex = -1000;
        } else {
            sidebar.style.left = '0px';
            sidebar.style.zIndex = 1000;
        }
    }
    function closeSidebar() {
        let sidebar = document.getElementById('sidebar');
        sidebar.style.left = '-280px';
        //sidebar.style.zIndex = -1000;
    }
    document.addEventListener('transitionend', () => {
        let sidebar = document.getElementById('sidebar');
        let close_button = document.getElementById('close-button');
        if (sidebar.style.left === '-280px') {
            sidebar.style.zIndex = -1000;
            close_button.style.display = 'inline-block';
        }
    });
    <?php endif; ?>
</script>

</body>
</html>

<?php 

$session->unset_error_messages();
$session->unset_old_inputs(); 

?>