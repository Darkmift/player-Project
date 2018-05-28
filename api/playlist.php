<?php

if (!empty($_GET['404']) || empty($_GET['type']) || !in_array($_GET['type'], array('playlist', 'songs'))) {
    fail(404);
}
require 'instructions.php';

try {
    // db connection
    $servername = "localhost";
    $username = "root";
    $password = "root12";
    $dbname = "playlist";
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS' || $_GET['type'] == 'instructions') {
        response($GLOBALS['instructions'], true);
        die();
    }
    // operate request
    if ($_GET['type'] == 'playlist') {
        // playlist
        if (isset($_GET['id'])) {
            playlist_item($_GET['id']);
        } else {
            playlists();
        }
    } else {
        if (isset($_GET['id'])) {
            // songs
            playlist_songs($_GET['id']);
        } else {
            response(false,
                array(
                    "Notice" => $_SERVER['REQUEST_URI'] .
                    " is not a valid request.Please check: http://" .
                    $_SERVER['SERVER_NAME'] . "/playlist/api for api info"),
                true);
        }
    }
} catch (PDOException $e) {
    echo json_encode(array("success" => "false", "error" => "Sorry,Connection to DB failed,please contact site manager at:admin@playlist.com"));
    fail(503);
}

// ----- helpers

function playlist_item($id)
{
    global $conn;
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            $data = array();
            $stmt = $conn->prepare("SELECT id,name,image FROM playlists WHERE id=:id");
            if ($stmt->execute(array('id' => $id))) {
                $data = $stmt->fetch(PDO::FETCH_ASSOC);
            }
            response(true, $data, true);
            break;
        case 'POST':
            $p = json_decode(file_get_contents('php://input'), true);
            $update = array();
            if (!empty($p['name'])) {
                $update['name'] = $p['name'];
            }

            if (!empty($p['image'])) {
                $update['image'] = $p['image'];
            }

            if (empty($update)) {
                response();
            } else {
                $update['id'] = $id;
                $stmt = $conn->prepare('UPDATE playlists SET ' . implode(", ", array_map(function ($v) {return "{$v}=:{$v}";}, array_keys($update))) . ' WHERE id=:id');
                $stmt->execute($update);
                response(true);
            }
            break;
        case 'DELETE':
            $stmt = $conn->prepare("DELETE FROM playlists WHERE id=:id");
            $stmt->execute(array(
                'id' => $id,
            ));
            response(true);
            break;
        default:
            response($GLOBALS['instructions'], true);
            fail(400);
    }
}

function playlists()
{
    global $conn;
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            $data = array();
            $stmt = $conn->prepare("SELECT id,name,image FROM playlists");
            if ($stmt->execute()) {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $data[] = $row;
                }
            }
            response(true, $data, true);
            break;
        case 'POST':
            $okresult = false;
            $data = null;
            $p = json_decode(file_get_contents('php://input'), true);
            if (!empty($p['name']) && !empty($p['image']) && !empty($p['songs']) && is_array($p['songs'])) {
                $okresult = true;
                $c = count($p['songs']);
                for ($i = 0; $i < $c && $okresult; $i++) {
                    $okresult = !empty($p['songs'][$i]['name']) && !empty($p['songs'][$i]['url']);
                }
                if ($okresult) {
                    $stmt = $conn->prepare("INSERT INTO playlists(name,image,songs) VALUES(:name, :image, :songs)");
                    $stmt->execute(array(
                        'name' => $p['name'],
                        'image' => $p['image'],
                        'songs' => json_encode($p['songs'], true),
                    ));
                    $data = [
                        'id' => $conn->lastInsertId(),
                    ];
                }
            }
            response($okresult, $data);
            break;
        default:
            fail(400);
            break;
    }
}
function playlist_songs($id)
{
    global $conn;
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            $data = array();
            $stmt = $conn->prepare("SELECT id,songs FROM playlists WHERE id=:id");
            if ($stmt->execute(array('id' => $id))) {
                $data = $stmt->fetch(PDO::FETCH_ASSOC);
            }
            response(true, [
                'songs' => empty($data['songs']) ? [] : json_decode($data['songs'], true),
            ]);
            break;
        case 'POST':
            $okresult = false;
            $p = json_decode(file_get_contents('php://input'), true);
            if (!empty($p['songs']) && is_array($p['songs'])) {
                $okresult = true;
                $c = count($p['songs']);
                for ($i = 0; $i < $c && $okresult; $i++) {
                    $okresult = !empty($p['songs'][$i]['name']) && !empty($p['songs'][$i]['url']);
                }
                if ($okresult) {
                    $stmt = $conn->prepare('UPDATE playlists SET songs=:songs WHERE id=:id');
                    $stmt->execute(array(
                        'songs' => json_encode($p['songs']),
                        'id' => $id));
                }
            }
            response($okresult);
            break;
        default:
            fail(400);
    }
}

function response($success = false, $payload = array(), $forceResult = false)
{
    $status = $success ? 200 : 400;
    $ret = array('success' => $success);
    if ($payload || $forceResult) {$ret['data'] = $payload;}
    header('Content-Type: application/json');
    http_response_code($status);
    echo json_encode($ret);
}

function fail($code)
{
    header('Content-Type: application/html');
    http_response_code($code);
}
