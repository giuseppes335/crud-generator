<?php require_once 'private/layout/header.php'; ?>

<section class="primary-font-color">
    
    <h1 style="font-size: 32px;">Crea elementi</h1>

    <form action="be-crea-elementi.php" method="post">

        <div class="form-item" style="margin-top: 8px;">
            <label for="schema">Schema</label>
            <textarea name="schema" id="schema" style="height: 600px;"></textarea>
        </div>

        <div class="form-item" style="margin-top: 8px; overflow: auto;">
            <button type="submit" style="height: 36px; font-size: 16px; float: right;">Crea elemento</button>
        </div>
    </form>

</section>

<?php require_once 'private/layout/footer.php'; ?>