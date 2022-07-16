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
        <input placeholder="Contact number" type="text" class="form-control" name="contactno" value="<?php echo $contactno ?>">
    </div>
    <div class="mb-3">
        <input placeholder="Birthday" type="text" class="form-control" name="birthday" value="<?php echo $birthday ?>">
    </div>
    <div class="mb-3">
        <input placeholder="Business name" type="text" class="form-control" name="businessname" value="<?php echo $businessname ?>">
    </div>
    <div class="mb-3">
        <input placeholder="Occupation" type="text" class="form-control" name="occupation" value="<?php echo $occupation ?>">
    </div>
    <div class="mb-3">
        <input placeholder="Comaker" type="text" class="form-control" name="comaker" value="<?php echo $comaker ?>">
    </div>
    <div class="mb-3">
        <input placeholder="Comaker Contact Number" type="text" class="form-control" name="comakerno" value="<?php echo $comakerno ?>">
    </div>
    <div class="mb-3">
        <textarea placeholder="Remarks" type="text" class="form-control" name="remarks"><?php echo $remarks ?></textarea>
    </div>

    <button type="submit" class="btn btn-primary">Submit</button>

</form>