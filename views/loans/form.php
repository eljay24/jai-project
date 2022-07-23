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

//// SELECT COLLECTORS FROM COLLECTORS TABLE
$statementCollector = $conn->prepare("SELECT * FROM jai_db.collectors as c");
$statementCollector->execute();
$collectors = $statementCollector->fetchAll(PDO::FETCH_ASSOC);

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
        <select class="form-control" name="amount" required>
            <option value="" disabled selected>Select amount</option>
            <?php

            foreach ($amounts as $amount) {
                echo "<option>" . $amount['amount'] . "</option>";
            }

            ?>
        </select>
    </div>
    <div class="mb-3">
        <label>Mode</label>
        <select class="form-control" name="mode" required>
            <option value="" disabled selected>Select mode</option>
            <?php

            foreach ($modes as $mode) {
                echo "<option>" . ucwords(strtolower($mode['mode']))  . "</option>";
            }

            ?>
        </select>
    </div>
    <div class="mb-3">
        <label>Term</label>
        <select class="form-control" name="term" required>
            <option value="" disabled selected>Select term</option>
            <?php

            foreach ($terms as $term) {
                echo "<option>" . ucwords(strtolower($term['term']))  . "</option>";
            }

            ?>
        </select>
    </div>
    <div class="mb-3">
        <label>Collector</label>
        <select class="form-control" name="collector" required>
            <option value="" disabled selected>Select collector</option>
            <?php

            foreach ($collectors as $collector) {
                echo '<option value="' . $collector['c_id'] . '">' . ucwords(strtolower($collector['firstname'])) . ' ' . ucwords(strtolower($collector['lastname'])) . '</option>';
            }

            ?>
        </select>
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