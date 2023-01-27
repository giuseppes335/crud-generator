<?php require_once 'private/layout/header.php'; ?>

<?php $demos = $application->get_demos($session->get_session_id()); ?>

<section class="primary-font-color">

    <?php if ($demos) : ?>

    <h1 style="font-size: 32px;"><?= $demos[0]['name']; ?></h1>
    <p><?= $request->translate('demos_created_at_label'); ?>: <?= $demos[0]['created_at']; ?></p>
    <a class="button-link" href="personalizza-demo.php?demo_id=<?= $demos[0]['id']; ?>"><?= $request->translate('demos_update_demo_label'); ?></a>
    <a class="button-link" href="be-download-demo.php?demo_id=<?= $demos[0]['id']; ?>"><?= $request->translate('demos_download_demo_label'); ?></a>
    <a class="button-link" href="preview.php?demo_id=<?= $demos[0]['id']; ?>&reset&debug"><?= $request->translate('demos_start_demo_label'); ?></a>
    <a class="button-link" href="preview.php?demo_id=<?= $demos[0]['id']; ?>&reset" onclick="event.preventDefault(); if(confirm('<?= $request->translate('demos_confirm_text'); ?>')) window.location.href = this.href">Crea e prova</a>

    <?php else : ?>

    <h1>Scegli un'applicazione</h1>
    <a class="button-link" href="scegli-un-applicazione.php"><?= $request->translate('templates_header_label'); ?></a>
    

    <?php endif; ?>

</section>


<?php require_once 'private/layout/footer.php'; ?>