<?php require_once 'private/layout/header.php'; ?>

<?php $demo = $application->get_demo($request->get['demo_id']); ?>

<section class="primary-font-color">
    
    <h1 style="font-size: 32px;"><?= $request->translate('customize_demo_header_label'); ?></h1>


    <form action="be-customize-demo.php" method="post">

        <input type="hidden" name="demo_id" value="<?= $request->get['demo_id']; ?>">

        <div class="form-item">
            <label for="name"><?= $request->translate('customize_demo_name_label'); ?></label>
            <input type="text" name="name" id="name" value="<?= $session->get_old_input('name')?$session->get_old_input('name'):$demo['name']; ?>">
            
            <?php if($session->get_error_message('name')): ?>
            <span class="error"><?= $session->get_error_message('name'); ?></span>
            <?php endif; ?>

        </div>
        <div class="form-item" style="margin-top: 8px;">
            <label for="schema"><?= $request->translate('customize_demo_schema_label'); ?></label>
            <textarea name="schema" id="schema" style="height: 600px;"><?= $session->get_old_input('schema')?$session->get_old_input('schema'):$demo['schema0']; ?></textarea>
            
            <?php if($session->get_error_message('schema')): ?>
            <span class="error"><?= $session->get_error_message('schema'); ?></span>
            <?php endif; ?>

        </div>

        <div class="form-item" style="margin-top: 8px; overflow: auto;">
            <button type="submit" style="height: 36px; font-size: 16px; float: right;"><?= $request->translate('customize_demo_button_submit'); ?></button>
        </div>
    </form>

</section>

<?php require_once 'private/layout/footer.php'; ?>