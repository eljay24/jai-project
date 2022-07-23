<?php if (!empty($errors)) : ?>
    <div class="alert alert-danger" role="alert">
        <?php foreach ($errors as $error) : ?>
            <div><?php echo $error; ?></div>
        <?php endforeach; ?>
    </div>

<?php endif;
//// SELECT AMOUNTS FROM RATES TABLE
$statementAmount = $conn->prepare("SELECT 
                             DISTINCT amount
                             FROM jai_db.rates as r
                             ORDER BY amount ASC    
");
$statementAmount->execute();
$amounts = $statementAmount->fetchAll(PDO::FETCH_ASSOC);

//// SELECT MODES FROM RATES TABLE
$statementMode = $conn->prepare("SELECT 
                             DISTINCT mode
                             FROM jai_db.rates as r
");
$statementMode->execute();
$modes = $statementMode->fetchAll(PDO::FETCH_ASSOC);

//// SELECT TERMS FROM RATES TABLE
$statementTerm = $conn->prepare("SELECT 
                             DISTINCT term
                             FROM jai_db.rates as r
");
$statementTerm->execute();
$terms = $statementTerm->fetchAll(PDO::FETCH_ASSOC);

?>

<form action="" method="post" enctype="multipart/form-data">

    <script>
        $(document).ready(function() {
            $("#namesearch").keyup(function() {
                var name = $("#namesearch").val();
                $.ajax({
                    url: "suggestions.php",
                    method: "POST",
                    data: {
                        suggestion: name
                    },
                    dataType: "html",
                    beforeSend: function() {},
                    success: function(data) {

                        //response (data);
                        $("#borrower").html(data);
                        console.log(data)
                    },
                    error: function(response) {
                        console.log(response);
                    },
                });
            });
        });
    </script>

    <input data-borrower-name="" type="text" name="name" id="namesearch" placeholder="Search for borrowers...">
    <br>
    <span></span>
    <div id="borrower" name="borrower">
      
    </div>

    <!--
    <?php if ($borrower['picture']) { ?>
        <img src="/<?= 'JAI/public/' . $borrower['picture']; ?>" class="update-image">
    <?php } ?>
    -->


    <?php // <img src="/<?= 'JAI/public/pictures/Default/picture-placeholder.png'; " class="update-image"> 
    ?>

    <br><br>

    <div class="mb-3">
        <label>Amount</label>
        <select class="form-control" name="amount">
            <?php

            foreach ($amounts as $amount) {
                echo "<option>" . $amount['amount'] . "</option>";
            }

            ?>
        </select>
        <!-- <input placeholder="Amount" type="text" class="form-control" name="amount" value="<?php echo $amount ?>"> -->
    </div>
    <div class="mb-3">
        <label>Mode</label>
        <select class="form-control" name="mode">
            <?php

            foreach ($modes as $mode) {
                echo "<option>" . ucwords(strtolower($mode['mode']))  . "</option>";
            }

            ?>
        </select>
        <!-- <input placeholder="Mode" type="text" class="form-control" name="mode" value="<?php echo $mode ?>"> -->
    </div>
    <div class="mb-3">
        <label>Term</label>
        <select class="form-control" name="term">
            <?php

            foreach ($terms as $term) {
                echo "<option>" . ucwords(strtolower($term['term']))  . "</option>";
            }

            ?>
        </select>
        <!-- <input placeholder="Term" type="text" class="form-control" name="term" value="<?php echo $term ?>"> -->
    </div>
    <div class="mb-3">
        <label>Release Date</label>
        <input name="releasedate" placeholder="Release Date" type="text" class="form-control">
    </div>
    <div class="mb-3">
        <label>Due Date</label>
        <input name="duedate" placeholder="Due Date" type="text" class="form-control">
    </div>

    <button type="submit" class="btn btn-primary">Submit</button>

</form>