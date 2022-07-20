<?php if (!empty($errors)) : ?>
    <div class="alert alert-danger" role="alert">
        <?php foreach ($errors as $error) : ?>
            <div><?php echo $error; ?></div>
        <?php endforeach; ?>
    </div>

<?php endif; ?>

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

        // $( "#namesearch" ).autocomplete({
        //   source: function( request, response ) {
        //     $.ajax( {
        //       url: "suggestions.php",
        //       dataType: "jsonp",
        //       data: {
        //         term: request.term
        //       },
        //       success: function( data ) {
        //         response( data );
        //       }
        //     } );
        //   },
        //   minLength: 2,
        //   select: function( event, ui ) {
        //     log( "Selected: " + ui.item.value + " aka " + ui.item.id );
        //   }
        // } );
    </script>

    <input data-borrower-name="" type="text" name="name" id="namesearch" placeholder="Search for borrowers...">
    <br>
    <span></span>
    <select id="borrower" name="borrower">
        <?php
        foreach ($loans as $i => $loan) {
            echo '<option>' . $loan['b_id'] . '</option>';
        }
        ?>
    </select>

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
            <option>5000</option>
            <option>10000</option>
            <option>12000</option>
            <option>14000</option>
            <option>15000</option>
            <option>20000</option>
            <option>25000</option>
            <option>30000</option>
            <option>35000</option>
            <option>40000</option>
            <option>45000</option>
            <option>50000</option>
            <option>55000</option>
            <option>60000</option>
            <option>65000</option>
            <option>70000</option>
            <option>75000</option>
            <option>80000</option>
            <option>85000</option>
            <option>90000</option>
            <option>95000</option>
            <option>100000</option>
        </select>
        <!-- <input placeholder="Amount" type="text" class="form-control" name="amount" value="<?php echo $amount ?>"> -->
    </div>
    <div class="mb-3">
        <label>Mode</label>
        <select class="form-control" name="mode">
            <option>Daily</option>
            <option>Weekly</option>
            <option>Monthly</option>
        </select>
        <!-- <input placeholder="Mode" type="text" class="form-control" name="mode" value="<?php echo $mode ?>"> -->
    </div>
    <div class="mb-3">
        <label>Term</label>
        <select class="form-control" name="term">
            <option>1 month</option>
            <option>2 months</option>
            <option>3 months</option>
            <option>4 months</option>
            <option>5 months</option>
            <option>6 months</option>
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