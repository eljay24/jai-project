<?php
require_once "../../views/includes/dbconn.php";
require_once "../../views/partials/header.php";

?>

<body>
  <div class="content-container">
    <?= "Hello" ?>
    <div class="chart-div">
      <canvas id="testChart"></canvas>
    </div>

    <?php
    $statementTest = $conn->prepare("SELECT CONCAT(b.firstname, ' ', b.lastname) as bname, p.l_id, p.l_id, sum(p.amount) as amount
                                     FROM jai_db.payments as p
                                     INNER JOIN jai_db.borrowers as b
                                     ON b.b_id = p.b_id
                                     GROUP BY l_id");
    $statementTest->execute();
    $sumOfAmounts = $statementTest->fetchAll(PDO::FETCH_ASSOC);

    // echo "<pre>";
    // var_dump($sumOfAmounts);
    // exit;
    ?>

    <script>
      const borrower = <?php foreach ($sumOfAmounts as $i => $l_id) {
                          $borrower[] = $l_id['bname'] . " Loan#" . $l_id['l_id'];
                        }
                        echo json_encode($borrower);
                        ?>;

      const amount = <?php foreach ($sumOfAmounts as $i => $l_id) {
                        $amount[] = $l_id['amount'];
                      }
                      echo json_encode($amount);
                      ?>;

      console.log(borrower);

      // SETUP BLOCK
      const data = {
        labels: borrower,
        datasets: [{
          label: 'Total Payments',
          data: amount,
          backgroundColor: [
            'rgba(255, 99, 132, 0.8)',
            'rgba(255, 159, 64, 0.8)',
            'rgba(255, 205, 86, 0.8)',
            'rgba(75, 192, 192, 0.8)',
            'rgba(54, 162, 235, 0.8)',
            'rgba(153, 102, 255, 0.8)',
            'rgba(201, 203, 207, 0.8)'
          ],
          borderColor: [
            'rgb(255, 99, 132)',
            'rgb(255, 159, 64)',
            'rgb(255, 205, 86)',
            'rgb(75, 192, 192)',
            'rgb(54, 162, 235)',
            'rgb(153, 102, 255)',
            'rgb(201, 203, 207)'
          ],
          borderWidth: 2
        }]
      };

      // CONFIG BLOCK
      const config = {
        type: 'bar',
        data: data,
        options: {
          scales: {
            y: {
              beginAtZero: true
            }
          }
        }
      };

      // RENDER BLOCK
      const myChart = new Chart(
        document.getElementById('testChart'),
        config
      );
    </script>


  </div>