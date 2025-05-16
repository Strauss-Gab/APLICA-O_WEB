<?php
session_start();
$conn = new mysqli("localhost", "root", "", "aplicacao");

if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

$usuario = $_POST['usuario'];
$senha = hash('sha256', $_POST['senha']);

$sql = "SELECT * FROM administrador WHERE usuario = ? AND senha = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $usuario, $senha);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $_SESSION['adm'] = $usuario;
    header("Location: index.php");
    exit;
} else {
    echo "<script>alert('Usuário ou senha inválido!'); window.location='login.php';</script>";
}
?>