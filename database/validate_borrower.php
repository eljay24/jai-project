<?php

$firstname = $_POST['firstname'];
$middlename = $_POST['middlename'];
$lastname = $_POST['lastname'];
$address = $_POST['address'];
$contactno = $_POST['contactno'];
$birthday = $_POST['birthday'];
$businessname = $_POST['businessname'];
$occupation = $_POST['occupation'];
$comaker = $_POST['comaker'];
$remarks = $_POST['remarks'];

$picturePath = '';

$fullname = $_POST['firstname'].' '.$_POST['middlename'].' '.$_POST['lastname'];

if (!$firstname) {
    $errors[] = 'Please provide first name.';
}
if (!$middlename) {
    $errors[] = 'Please provide middle name.';
}
if (!$lastname) {
    $errors[] = 'Please provide last name.';
}
if (!$address) {
    $errors[] = 'Please provide address.';
}
if (!$contactno) {
    $errors[] = 'Please provide contact number.';
}
if (!$birthday) {
    $errors[] = 'Please provide birthday.';
}
if (!$businessname) {
    $errors[] = 'Please provide business name.';
}
if (!$occupation) {
    $errors[] = 'Please provide occupation.';
}
if (!$comaker) {
    $errors[] = 'Please provide comaker.';
}


if (!is_dir(__DIR__.'/public/pictures')) {
    mkdir(__DIR__.'/public/pictures');
}

if (empty($errors)) {

    $picture = $_FILES['picture'] ?? null;
    $picturePath = $borrower['picture'];
    
    if ($picture && $picture['name']) {

        if ($borrower['picture']) {
            unlink(__DIR__.'/public/'.$borrower['picture']);
        }

        $picturePath = 'pictures/'.$fullname.'/'.$picture['name'];
        if (!is_dir($picturePath)) {
            mkdir(dirname(__DIR__.'/public/'.$picturePath));
        }
        move_uploaded_file($picture['tmp_name'], __DIR__.'/public/'.$picturePath);

    }

}

?>