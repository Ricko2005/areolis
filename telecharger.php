<?php
session_start();
require_once("bd.php");
require('fpdf186/fpdf.php');

// Préparer la requête pour récupérer les données
$requete = $bdd->query("SELECT * FROM user");
$users = $requete->fetchAll(PDO::FETCH_ASSOC);

// Créer une instance de FPDF
$pdf = new FPDF();
$pdf->AddPage();

// Définir la police par défaut (Arial)
$pdf->SetFont('Arial', 'B', 12);

// Ajuster les largeurs des colonnes pour correspondre à la largeur de la page

$pdf->Cell(25, 10, 'Nom', 1);
$pdf->Cell(30, 10, 'Prenom', 1);
$pdf->Cell(30, 10, 'Telephone', 1);
$pdf->Cell(55, 10, 'Adresse mail', 1);
$pdf->Cell(30, 10, 'Type ticket', 1);
$pdf->Cell(25, 10, 'Quantite', 1);
$pdf->Ln();

// Ajouter les données
$pdf->SetFont('Arial', '', 12);
foreach ($users as $user) {
  
    $pdf->Cell(25, 10, htmlspecialchars($user['Name'] ?? 'N/A'), 1);
    $pdf->Cell(30, 10, htmlspecialchars($user['Surname'] ?? 'N/A'), 1);
    $pdf->Cell(30, 10, htmlspecialchars($user['Telephone'] ?? 'N/A'), 1);
    $pdf->Cell(55, 10, htmlspecialchars($user['Adresse_mail'] ?? 'N/A'), 1);
    $pdf->Cell(30, 10, htmlspecialchars($user['type_Ticket'] ?? 'N/A'), 1);
    $pdf->Cell(25, 10, htmlspecialchars($user['Quantite'] ?? 'N/A'), 1);
    $pdf->Ln();
}

// Envoyer le PDF au navigateur
$pdf->Output('D', 'orders_' . date('Ymd_His') . '.pdf');
exit();
?>


