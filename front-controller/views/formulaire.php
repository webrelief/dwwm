<?php if (isset($message)) {?>
    <div class="alert alert-success">
        <?php echo $message;?>
    </div>
<?php } ?>

<form action="" method="post">
    <div>
        Element : <input type="text" name="element" value="<?php echo $input_value ?? ''; ?>">
    </div>
    <div>
        <button type="submit">Valider</button>
    </div>
</form>