<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js" type="text/javascript"></script>
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

        <link href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css" type="text/css"/>
        <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js" type="text/javascript"></script>
        <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js" type="text/javascript"></script>
    </head>
    <body>

        <form method="post" enctype="multipart/form-data">
            <input type="file" name="model_file" />
            <button>save</button>
        </form>
        <?php
        if (isset($_FILES["model_file"])) {
            $path = $_FILES["model_file"]["tmp_name"];
            require_once './ExcelFunction.php';
            require_once './IntraDay_candle_stick.php';

            $excel = new ExcelFunction();


            $header_array = array("Date", "Time", "Open", "High", "Low", "Close", "Quantity", "Average");

            $data_type_array = array("number" => array(2, 3, 4, 5, 6, 7), "string" => array(0, 1), "datetime" => array());

            $compulsary_column = "All";

            $rowCollection = $excel->excel_fetch_data($path, $header_array, $data_type_array, $compulsary_column);
            load_data($rowCollection);
        }
        ?>
        <script>
            $(document).ready(function () {

                $("#table").DataTable();
            });
        </script>
    </body>
</html>
