<?php
include_once 'includes/db.php';

error_reporting(0);
ini_set('display_errors', '0');

class FilmService {
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    // 1. Film műveletek

    // Filmek listázása
    public function getFilms() {
        $query = "SELECT * FROM film";
        $result = $this->conn->query($query);
        $films = [];
        while ($row = $result->fetch_assoc()) {
            $films[] = $row;
        }
        return $films;
    }

    // Film hozzáadása
    public function addFilm($cim, $gyartas, $hossz, $bemutato, $youtube) {
        $query = "INSERT INTO film (cim, gyartas, hossz, bemutato, youtube) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("siiis", $cim, $gyartas, $hossz, $bemutato, $youtube);
        return $stmt->execute();
    }

    // Film frissítése
    public function updateFilm($id, $cim, $gyartas, $hossz, $bemutato, $youtube) {
        $query = "UPDATE film SET cim = ?, gyartas = ?, hossz = ?, bemutato = ?, youtube = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("siisii", $cim, $gyartas, $hossz, $bemutato, $youtube, $id);
        return $stmt->execute();
    }

    // Film törlése
    public function deleteFilm($id) {
        $query = "DELETE FROM film WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    // 2. Feladat műveletek

    // Feladatok listázása
    public function getTasks() {
        $query = "SELECT * FROM feladat";
        $result = $this->conn->query($query);
        $tasks = [];
        while ($row = $result->fetch_assoc()) {
            $tasks[] = $row;
        }
        return $tasks;
    }

    // Feladat hozzáadása
    public function addTask($filmid, $szemelyid, $megnevezes) {
        $query = "INSERT INTO feladat (filmid, szemelyid, megnevezes) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iis", $filmid, $szemelyid, $megnevezes);
        return $stmt->execute();
    }

    // Feladat frissítése
    public function updateTask($id, $filmid, $szemelyid, $megnevezes) {
        $query = "UPDATE feladat SET filmid = ?, szemelyid = ?, megnevezes = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iisi", $filmid, $szemelyid, $megnevezes, $id);
        return $stmt->execute();
    }

    // Feladat törlése
    public function deleteTask($id) {
        $query = "DELETE FROM feladat WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    // 3. Szemely műveletek

    // Szemelyek listázása
    public function getPeople() {
        $query = "SELECT * FROM szemely";
        $result = $this->conn->query($query);
        $people = [];
        while ($row = $result->fetch_assoc()) {
            $people[] = $row;
        }
        return $people;
    }

    // Szemely hozzáadása
    public function addPerson($nev, $nem) {
        $query = "INSERT INTO szemely (nev, nem) VALUES (?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ss", $nev, $nem);
        return $stmt->execute();
    }

    // Szemely frissítése
    public function updatePerson($id, $nev, $nem) {
        $query = "UPDATE szemely SET nev = ?, nem = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssi", $nev, $nem, $id);
        return $stmt->execute();
    }

    // Szemely törlése
    public function deletePerson($id) {
        $query = "DELETE FROM szemely WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}

$options = array(
    'uri' => 'http://hujbermate.nhely.hu/soapserver.php'
);
$server = new SoapServer(null, $options);
$server->setClass('FilmService');
$server->handle();
?>
