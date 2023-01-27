<?php require_once 'private/layout/header.php'; ?>


<section class="primary-font-color">
    
    <h1 style="font-size: 32px;"><?= $request->translate('templates_header_label'); ?></h1>

    <div style="margin-top: 16px;">
        <?php foreach($application->get_templates() as $template) : ?>
        <div class="template">
            <h2><?= $template['name']; ?></h2>
            <div class="template-image-container">
                <img src="<?= $configuration->host?>/img/<?= $template['image_name']; ?>" alt="">
            </div>
            <p><?= $template['description']; ?></p>
            <a class="button-link" href="be-start-demo.php?template_id=<?= $template['id']; ?>"><?= $request->translate('templates_start_demo_label'); ?></a>
        </div>
        <?php endforeach; ?>
    </div>

</section>


<?php require_once 'private/layout/footer.php'; ?>