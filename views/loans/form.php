<?php if (!empty($errors)) : ?>
    <div class="alert alert-danger" role="alert">
        <?php foreach ($errors as $error) : ?>
            <div><?php echo $error; ?></div>
        <?php endforeach; ?>
    </div>

<?php endif; ?>

<form action="" method="post" enctype="multipart/form-data">

    <!--
    <?php if ($borrower['picture']) { ?>
        <img src="/<?= 'JAI/public/' . $borrower['picture']; ?>" class="update-image">
    <?php } ?>
    -->


    <?php // <img src="/<?= 'JAI/public/pictures/Default/picture-placeholder.png'; " class="update-image"> 
    ?>

    <br><br>

    <div class="mb-3">
        <input placeholder="Amount" type="text" class="form-control" name="amount" value="<?php echo $amount ?>">
    </div>
    <div class="mb-3">
        <input placeholder="Mode" type="text" class="form-control" name="mode" value="<?php echo $mode ?>">
    </div>
    <div class="mb-3">
        <input placeholder="Term" type="text" class="form-control" name="term" value="<?php echo $term ?>">
    </div>
    <div class="mb-3">
        <input placeholder="Interest Rate" type="text" class="form-control" name="interestrate" value="<?php echo $interestrate ?>">
    </div>
    <div class="mb-3">
        <input placeholder="Release Date" type="text" class="form-control" name="contactno" value="<?php echo $releasedate ?>">
    </div>
    <div class="mb-3">
        <input placeholder="Status" type="text" class="form-control" name="birthday" value="<?php echo $status ?>">
    </div>

    <button type="submit" class="btn btn-primary">Submit</button>

</form>