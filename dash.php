<?php
session_start();
require_once("bd.php");

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['id'])) {
    header('Location: connexion.php'); // Redirige vers la page de connexion
    exit();
}

// Vérifiez si l'id est présent et est valide
if (isset($_GET['id']) && intval($_GET['id']) > 0) {
    $takeid = intval($_GET['id']);
    
    // Préparez la requête pour sélectionner les informations d'un utilisateur par id
    $requete = $bdd->prepare("SELECT * FROM admin WHERE id = ?");
    $requete->execute([$takeid]);
    $takeinfo = $requete->fetch(PDO::FETCH_ASSOC);

    // Assurez-vous que la variable $takeinfo contient des données valides
    if (!$takeinfo) {
        echo "Aucun utilisateur trouvé avec cet ID.";
    }
} else {
    echo "ID invalide.";
}

// Récupérer les informations des utilisateurs
$requete = $bdd->query("SELECT * FROM user");
$users = $requete->fetchAll(PDO::FETCH_ASSOC);

$totalPeople = count($users);
// Initialiser les compteurs
$ticketSimpleCount = 0;
$ticketVIPCount = 0;
$totalAmount = 0;

// Définir les prix des billets
$priceTicketSimple = 2000;
$priceTicketVIP = 5000;

// Parcourir les utilisateurs pour calculer les comptes de billets et le montant total
foreach ($users as $user) {
    if ($user['type_Ticket'] === '2000') {
        $ticketSimpleCount += $user['Quantite'];
        $totalAmount += $user['Quantite'] * $priceTicketSimple;
    } elseif ($user['type_Ticket'] === '5000') {
        $ticketVIPCount += $user['Quantite'];
        $totalAmount += $user['Quantite'] * $priceTicketVIP;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
    <style>
        /* Styles pour les sections */
        .view {
            display: none;
        }

        /* Styles pour la boîte de dialogue modale */
        .modal {
            display: none;
            /* Masquer la boîte de dialogue par défaut */
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0, 0, 0);
            background-color: rgba(0, 0, 0, 0.4);
            padding-top: 60px;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #fefefe;
            border: 1px solid #888;
            width: 40%;
            max-width: 500px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 8px;
            text-align: center;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .modal-button {
            background-color: #B0A536;
            color: white;
            border: none;
            padding: 10px 20px;
            margin: 10px;
            cursor: pointer;
        }

        .modal-button.cancel {
            background-color: #f44336;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="navigation">
            <ul>
                <li><a href="#" id="dashboardLink">Dashboard</a></li>
                <li><a href="#" id="statsLink">Statistiques</a></li>
                <li><a href="#" id="editEventLink">Modifier l'événement</a></li>
                <li><a href="#" id="logoutBtn">Déconnexion</a></li>
            </ul>
        </div>

        <div class="main">
            <!-- Dashboard View -->
            <div id="dashboardView" class="view">
                <div class="topbar">
                    <div class="toggle">
                        <ion-icon name="menu-outline"></ion-icon>
                    </div>
                    <div class="search">
                        <label for="searchInput">
                            <input type="text" id="searchInput" placeholder="Search here">
                            <ion-icon name="search-outline"></ion-icon>
                        </label>
                    </div>
                    <div class="user">
                        <img src="img/after.jpg" alt="">
                    </div>
                </div>

                <div class="cardBox">
                    <div class="card">
                        <div>
                            <div class="numbers"><?= number_format($totalAmount, 0, ',', ' ') ?></div>
                            <div class="cardName">Sommes</div>
                        </div>
                        <div class="iconBx">
                            <ion-icon name="cash-outline"></ion-icon>
                        </div>
                    </div>

                    <div class="card">
                        <div>
                            <div class="numbers"><?= number_format($ticketSimpleCount, 0, ',', ' ') ?></div>
                            <div class="cardName">Ticket Simple</div>
                        </div>
                        <div class="iconBx">
                            <ion-icon name="pricetag-outline"></ion-icon>
                        </div>
                    </div>

                    <div class="card">
                        <div>
                            <div class="numbers"><?= number_format($ticketVIPCount, 0, ',', ' ') ?></div>
                            <div class="cardName">Ticket VIP</div>
                        </div>
                        <div class="iconBx">
                            <ion-icon name="ticket-outline"></ion-icon>
                        </div>
                    </div>

                    <div class="card">
                        <div>
                            <div class="numbers"><?= number_format($totalPeople, 0, ',', ' ') ?></div>
                            <div class="cardName">Nbr de personnes</div>
                        </div>
                        <div class="iconBx">
                            <ion-icon name="people-circle-outline"></ion-icon>
                        </div>
                    </div>
                </div>

                <div class="details">
                    <div class="recentOrders">
                        <div class="cardHeader">
                            <h2>Recent Orders</h2>
                            <a href="telecharger.php" class="btn">Télécharger PDF</a> <!-- Bouton de téléchargement PDF -->
                        </div>

                        <table id="ordersTable">
                            <thead>
                                <tr>
                                    <td>id</td>
                                    <td>Nom</td>
                                    <td>Prenom</td>
                                    <td>Telephone</td>
                                    <td>Adresse mail</td>
                                    <td>Type ticket</td>
                                    <td>Quantité</td>
                                </tr>
                            </thead>

                            <tbody>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($user['id'] ?? 'N/A') ?></td>
                                        <td><?= htmlspecialchars($user['Name'] ?? 'N/A') ?></td>
                                        <td><?= htmlspecialchars($user['Surname'] ?? 'N/A') ?></td>
                                        <td><?= htmlspecialchars($user['Telephone'] ?? 'N/A') ?></td>
                                        <td><?= htmlspecialchars($user['Adresse_mail'] ?? 'N/A') ?></td>
                                        <td><?= htmlspecialchars($user['type_Ticket'] ?? 'N/A') ?></td>
                                        <td><?= htmlspecialchars($user['Quantite'] ?? 'N/A') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Statistiques View -->
            <div id="statsView" class="view">
                <h2>Statistiques</h2>
                <!-- Ajoutez ici le contenu des statistiques -->
                <p>Contenu des statistiques ici.</p>
            </div>

            <!-- Modifier l'événement View -->
            <div id="editEventView" class="view">
                <h2>Modifier l'événement</h2>
                <!-- Ajoutez ici le contenu pour modifier l'événement -->
                <p>Contenu pour modifier l'événement ici.</p>
            </div>
        </div>
    </div>

    <!-- Boîte de dialogue modale -->
    <div id="logoutModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <p>Êtes-vous sûr de vouloir vous déconnecter ?</p>
            <button id="confirmLogout" class="modal-button">Confirmer</button>
            <button id="cancelLogout" class="modal-button cancel">Annuler</button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@ionic/core/dist/ionic/ionic.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const dashboardLink = document.getElementById('dashboardLink');
            const statsLink = document.getElementById('statsLink');
            const editEventLink = document.getElementById('editEventLink');
            const logoutBtn = document.getElementById('logoutBtn');

            const dashboardView = document.getElementById('dashboardView');
            const statsView = document.getElementById('statsView');
            const editEventView = document.getElementById('editEventView');
            const logoutModal = document.getElementById('logoutModal');

            function showView(view) {
                dashboardView.style.display = 'none';
                statsView.style.display = 'none';
                editEventView.style.display = 'none';
                logoutModal.style.display = 'none';

                view.style.display = 'block';
            }

            dashboardLink.addEventListener('click', (event) => {
                event.preventDefault();
                showView(dashboardView);
            });

            statsLink.addEventListener('click', (event) => {
                event.preventDefault();
                showView(statsView);
            });

            editEventLink.addEventListener('click', (event) => {
                event.preventDefault();
                showView(editEventView);
            });

            logoutBtn.addEventListener('click', (event) => {
                event.preventDefault();
                logoutModal.style.display = 'block';
            });

            // Modal functionality
            const closeModalBtn = logoutModal.querySelector('.close');
            const confirmLogoutBtn = logoutModal.querySelector('#confirmLogout');
            const cancelLogoutBtn = logoutModal.querySelector('#cancelLogout');

            closeModalBtn.addEventListener('click', () => {
                logoutModal.style.display = 'none';
            });

            cancelLogoutBtn.addEventListener('click', () => {
                logoutModal.style.display = 'none';
            });

            confirmLogoutBtn.addEventListener('click', () => {
                window.location.href = 'deconnexion.php'; // Redirige vers la page de déconnexion
            });

            window.addEventListener('click', (event) => {
                if (event.target === logoutModal) {
                    logoutModal.style.display = 'none';
                }
            });

            // Set default view
            showView(dashboardView);
        });
    </script>
</body>

</html>
