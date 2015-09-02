<?php

    $conn = new PDO('mysql:host=localhost;dbname=test', 'root', '');
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (isset($_POST['days'])){

    $id = (int)$_GET['id'];
    $days = (int)$_POST['days'];
    extendPublication($conn, $days, $id);
}



function sendMail(PDO $conn, $row, $days)
{
    //Sends message to email with all info about article and $days var ;
}

function getPublishedTo(PDO $conn, $id)
{

    $query = "SELECT publicated_to FROM items WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $date = $stmt->fetch(PDO::FETCH_ASSOC);

    return new DateTime($date['publicated_to']);
}

function extendPublication(PDO $conn, $days, $id)
{
    $date = getPublishedTo($conn, $id);

    $date->modify("+{$days} days");
    $publicated_to = $date->format("Y-m-d H:i:s");

    $query = "UPDATE items SET publicated_to = :publicated_to WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':publicated_to', $publicated_to);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
}

function days(PDO $conn, $id)
{
    $current_time = new DateTime();
    $published_to = getPublishedTo($conn, $id);
    $time_remains = $published_to->diff($current_time);
    return $time_remains->days;

}
function main(PDO $conn)
{
    //$offset
    $rows = findAllItems();
    foreach ($rows as $row)
    {
        if (days($conn, $row['id']) == 5 ||
            days($conn, $row['id']) == 2 ||
            days($conn, $row['id']) == 1){
            sendMail($conn, $row, days($conn, $row['id']));
        }

    }
}

function publish(PDO $conn, $id)
{
    $publicated_to = new DateTime();
    $publicated_to->modify("+30 days");
    $publicated_to = $publicated_to->format("Y-m-d H:i:s");


    $query = "UPDATE items SET status = 2, publicated_to = :publicated_to WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':publicated_to', $publicated_to);
    $stmt->bindParam(":id", $id);
    $stmt->execute();

}

function findAllItems(PDO $conn, $limit, $offset)
{
    $query = "SELECT * FROM items LIMIT :limit OFFSET :offset";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $res = $stmt->fetch(PDO::FETCH_ASSOC);
    return array($res);
}
?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"

        "http://www.w3.org/TR/html4/loose.dtd">



<html lang="en">



<head>



	<meta http-equiv="content-type" content="text/html; charset=utf-8">

	<title>Title Goes Here</title>



</head>



<body>
<?php if (!isset($_POST['days']))
{

?>
        <form action="index.php?id=<?=$_GET['id']?>" method="post">
            Enter number of days:
            <input type="text" name="days">
            <input type="hidden" name="id" value="">
            <input type="submit" name="submit" value="Submit">
        </form>

<?php
} else {
    echo "Success";
} ?>


</body>



</html>


