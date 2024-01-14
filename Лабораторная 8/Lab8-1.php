<html>
<head>
    <title>Таблица умножения</title>
    <style>
        table {
            border-collapse: collapse;
            width: 50%;
            margin: 30px auto;
        }
        th, td {
            padding: 15px;
            text-align: center;
            border: 1px solid pink;
        }       
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>Таблица умножения</h1>
    <table>
        <tr>
            <th>&times;</th>
     <?php
            for ($i = 0; $i <= 10; $i++) {
                echo "<th>$i</th>";
            }
            ?>
        </tr>
        <?php
        for ($i = 0; $i <= 10; $i++) {
            echo "<tr>";
            echo "<th>$i</th>";
            for ($j = 0; $j <= 10; $j++) {
                echo "<td>" . ($i * $j) . "</td>";
            }
            echo "</tr>";
        }
     ?>
    </table>
</body>
</html>ml>