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

        function changeFunc() {
            var selectBox = document.getElementById("borrower");
            var selectedValue = selectBox.options[selectBox.selectedIndex].value;
            var payment = document.getElementById("payment");

            console.log(selectedValue);
            $.ajax({
                url: "../ajax-calls/get-borrower.php",
                method: "POST",
                data: {
                    b_id: selectedValue,
                },
                dataType: "json",
                success: function(borrowerDetails) {
                    
                    // console.log(borrowerDetails[0]['amount']);

                    $('#loanamount').val(borrowerDetails[0]['amount'].toFixed(2));
                    $('#payable').val(borrowerDetails[0]['payable'].toFixed(2));
                    $('#remainingbalance').val(borrowerDetails[0]['balance'].toFixed(2));
                    $('#amortization').val(borrowerDetails[0]['amortization'].toFixed(2));
                    $('#mode').val(borrowerDetails[0]['mode']);
                    $('#term').val(borrowerDetails[0]['term']);
                    $('#collector').val(borrowerDetails[0]['cfname'] + ' ' + borrowerDetails[0]['clname']);
                    $('#loanid').val(borrowerDetails[0]['l_id']);
                    $('#collectorid').val(borrowerDetails[0]['c_id']);
                    // $('#payment').val(borrowerDetails[0]['amortization']);
                    payment.placeholder = borrowerDetails[0]['amortization'].toFixed(2);

                },
                error: function(errorData) {
                    console.log(errorData);
                },
            })
        }

        function setToZero() {
            var selectBox = document.getElementById("type");
            var selectedValue = selectBox.options[selectBox.selectedIndex].value;
            if (selectedValue == "Pass") {
                $('#payment').val(0);
            }
        }
    </script>

    <input data-borrower-name="" type="text" name="name" id="namesearch" placeholder="Search for borrowers...">
    <br>
    <br>
    <span></span>

    <select id="borrower" name="borrower" class="form-control" onchange="changeFunc();" data-live-search="true" required>
        <option value="" disabled selected>Select borrower</option>
        <?php
        foreach ($borrowers as $i => $borrower) {
            echo '<option value="' . $borrower['b_id'] . '">' . $borrower['b_id'] . ' ' . ucwords(strtolower($borrower['firstname']))  . ' ' . ucwords(strtolower($borrower['middlename'])) . ' ' . ucwords(strtolower($borrower['lastname']))  . '</option>';
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

    <br>
    <div class="mb-3">
        <label>Loan Amount</label>
        <input id="loanamount" name="loanamount" placeholder="Loan Amount" type="text" class="form-control" readonly required>
    </div>
    <div class="mb-3">
        <label>Payable</label>
        <input id="payable" name="payable" placeholder="Remaining Balance" type="number" class="form-control" readonly required>
    </div>
    <div class="mb-3">
        <label>Remaining Balance</label>
        <input id="remainingbalance" name="remainingbalance" placeholder="Remaining Balance" type="number" class="form-control" readonly required>
    </div>
    <div class="mb-3">
        <label>Mode</label>
        <input id="mode" name="mode" placeholder="Mode" type="text" class="form-control" readonly required>
    </div>
    <div class="mb-3">
        <label>Term</label>
        <input id="term" name="term" placeholder="Term" type="text" class="form-control" readonly required>
    </div>
    <div class="mb-3">
        <label>Amortization</label>
        <input id="amortization" name="amortization" placeholder="Amortization" type="number" class="form-control" readonly required>
    </div>
    <div class="mb-3">
        <label for="payment">Payment</label>
        <input id="payment" name="payment" placeholder="Payment" type="number" class="form-control" required>
    </div>
    <div class="mb-3">
        <label for="type">Type of Payment</label>
        <select name="type" id="type" class="form-control" onchange="setToZero();" required>
            <option value="" disabled selected>Select type</option>
            <option value="Cash">Cash</option>
            <option value="GCash">GCash</option>
            <option value="Pass">Pass</option>
        </select>
    </div>
    <div class="mb-3">
        <label for="collector">Collector</label>
        <input id="collector" name="collector" placeholder="Collector" type="text" class="form-control" readonly required>
        <select>

        </select>
    </div>

    <input id="loanid" name="loanid" hidden>
    <input id="collectorid" name="collectorid" hidden>

    <button type="submit" class="btn btn-primary">Submit</button>

</form>