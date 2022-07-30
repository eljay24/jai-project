<?php if (!empty($errors)) : ?>
    <div class="alert alert-danger" role="alert">
        <?php foreach ($errors as $error) : ?>
            <div><?php echo $error; ?></div>
        <?php endforeach; ?>
    </div>

<?php endif; ?>

<form action="" method="post" enctype="multipart/form-data">

    <?php if ($borrower['picture']) { ?>
        <img src="/<?= 'JAI/public/' . $borrower['picture']; ?>" class="update-image">
    <?php } ?>

    <?php // <img src="/<?= 'JAI/public/pictures/Default/picture-placeholder.png'; " class="update-image"> 
    ?>

    <br><br>

    <div class="mb-3">
        <label class="form-label">Picture</label>
        <br>
        <input type="file" name="picture">
    </div>
    <div class="mb-3">
        <input placeholder="First name" type="text" class="form-control letters-only" name="firstname" value="<?php echo $firstname ?>">
    </div>
    <div class="mb-3">
        <input placeholder="Middle name" type="text" class="form-control letters-only" name="middlename" value="<?php echo $middlename ?>">
    </div>
    <div class="mb-3">
        <input placeholder="Last name" type="text" class="form-control letters-only" name="lastname" value="<?php echo $lastname ?>">
    </div>
    <div class="mb-3">
        <input placeholder="Address" type="text" class="form-control alphanumeric" name="address" value="<?php echo $address ?>">
    </div>
    <div class="mb-3">
        <input placeholder="Contact number" type="text" class="form-control phone-number" name="contactno" value="<?php echo $contactno ?>">
    </div>
    <div class="mb-3">
        <input placeholder="Birthday" type="text" class="datepicker form-control" name="birthday" value="<?php echo $birthday ?>" onkeydown="return false" >
    </div>
    <div class="mb-3">
        <input placeholder="Business name" type="text" class="form-control alphanumeric" name="businessname" value="<?php echo $businessname ?>">
    </div>
    <div class="mb-3">
        <input placeholder="Occupation" type="text" class="form-control letters-only" name="occupation" value="<?php echo $occupation ?>">
    </div>
    <div class="mb-3">
        <input placeholder="Comaker Full Name" type="text" class="form-control letters-only" name="comaker" value="<?php echo $comaker ?>">
    </div>
    <div class="mb-3">
        <input placeholder="Comaker Contact Number" type="text" class="form-control phone-number" name="comakerno" value="<?php echo $comakerno ?>">
    </div>
    <div class="mb-3">
        <textarea placeholder="Remarks" type="text" class="form-control" name="remarks"><?php echo $remarks ?></textarea>
    </div>

    <button type="submit" class="btn btn-primary">Submit</button>

</form>