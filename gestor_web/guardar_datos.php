<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $json = file_get_contents('php://input');
    file_put_contents('data.json', $json);
    echo json_encode(["status" => "ok"]);
} else {
    echo json_encode(["status" => "error"]);
}