<?php
session_start();
require_once("bd.php");


if (!isset($_SESSION['id'])) {
    header('Location: connexion.php'); // Redirige vers la page de connexion
    exit();
}

$takeid = $_SESSION['id'];

// Préparez la requête pour sélectionner les informations d'un utilisateur par id
$requete = $bdd->prepare("SELECT * FROM admin WHERE id = ?");
$requete->execute([$takeid]);
$takeinfo = $requete->fetch(PDO::FETCH_ASSOC);

if (!$takeinfo) {
    echo "Aucun utilisateur trouvé avec cet ID.";
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
    if ($user['Type_ticket'] == 2000) {
        $ticketSimpleCount += $user['Quantite'];
        $totalAmount += $user['Quantite'] * $priceTicketSimple;
    } elseif ($user['Type_ticket'] == 5000) {
        $ticketVIPCount += $user['Quantite'];
        $totalAmount += $user['Quantite'] * $priceTicketVIP;
    } else {
        echo "Type de ticket inconnu : " . $user['Type_ticket'];
    }
}

// Debugging output





// ===================Modification script=================================:


    
   
    // Vérifiez si le formulaire a été soumis
    if (isset($_POST['update'])) {
        $Date = $_POST['Date'];
        $Lieu = $_POST['Lieu'];
        $Edition = $_POST['Edition'];
        $Admin = $_POST['Admin'];
    
    
        try {
            $requete = $bdd->prepare("UPDATE event SET Date = ?, Lieu = ? , Edition = ? WHERE Admin = ?");
            $requete->execute([$Date, $Lieu, $Edition, $Admin]);
    
            // Vérifiez le nombre de lignes affectées
            if ($requete->rowCount() > 0) {
               
            } else {
      
            }
            
        } catch (PDOException $e) {
            echo "Erreur lors de la modification des données : " . $e->getMessage();
        }
    }
    
    
    


// ====================== Changement d'edition====================================


if (isset($_POST['endEdition'])) {
    // Trouver le dernier numéro d'édition
    $requete = $bdd->query("SELECT MAX(edition) AS last_edition FROM user_story");
    $result = $requete->fetch(PDO::FETCH_ASSOC);
    $nextEdition = $result['last_edition'] + 1; // Incrémenter pour obtenir le prochain numéro d'édition

    // Copier les données de la table user vers user_story avec le nouveau numéro d'édition
    $requete = $bdd->prepare("INSERT INTO user_story (Name, Surname, Telephone, Adresse_mail, Quantite, Type_ticket, edition, date_edition)
    SELECT Name, Surname, Telephone, Adresse_mail, Quantite, Type_ticket, ?, NOW() FROM user");

    // Exécuter la requête d'insertion
    if (!$requete->execute([$nextEdition])) {
        // Afficher les erreurs éventuelles
        print_r($requete->errorInfo());
    }

    // Réinitialiser les données de la table user
    if (!$bdd->query("DELETE FROM user")) {
        // Afficher les erreurs éventuelles
        print_r($bdd->errorInfo());
    }
}



// ============================ Editions précédentes==================================:




$filterEdition = isset($_GET['edition']) ? htmlspecialchars($_GET['edition']) : '';

// Préparer la requête pour récupérer les données filtrées
$query = "SELECT * FROM user_story";
$params = [];

if ($filterEdition) {
    $query .= " WHERE edition = ?";
    $params[] = $filterEdition;
}

$requete = $bdd->prepare($query);
$requete->execute($params);
$userStories = $requete->fetchAll(PDO::FETCH_ASSOC);

// Initialiser les variables pour les statistiques


// Calculer les statistiques pour chaque édition

?>









<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>

    <!-- ======================= Styles================== -->
    <link rel="stylesheet" href="dashboard.css">
    <style>
        /* Styles pour la boîte de dialogue modale */
        .modal {
            display: none;
            /* Masquer la boîte de dialogue par défaut */
            position: fixed;
            z-index: 2;
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

        /* Styles pour la navigation */

        /* Styles pour les sections de contenu */
        .content-section {
            display: none;
        }

        .content-section.active {
            display: block;
        }
    </style>
</head>

<body>
    <!-- ==================== Navigation==================== -->
    <div class="container">
        <div class="navigation">
            <ul>
                <li>
                    <a href="#dashboardContent">
                        <!-- <span class="icon">
                        <ion-icon name="desktop-outline"></ion-icon> -->
                        </span>
                        <span class="title"><?= htmlspecialchars($takeinfo['Mail'] ?? 'N/A') ?></span>
                    </a>
                </li>

                <li>
                    <a href="#dashboardContent">
                        <span class="icon">
                            <ion-icon name="home-outline"></ion-icon>
                        </span>
                        <span class="title">Dashboard</span>
                    </a>
                </li>

                <li>
                    <a href="#statistiquesContent">
                        <span class="icon">
                            <ion-icon name="stats-chart-outline"></ion-icon>
                        </span>
                        <span class="title">Statistiques</span>
                    </a>
                </li>

                <li>
                    <a href="#modifierContent">
                        <span class="icon">
                            <ion-icon name="create-outline"></ion-icon>
                        </span>
                        <span class="title">Modifier l'événement</span>
                    </a>
                </li>

                <li>
                    <a href="deconnexion.php" id="logoutBtn">
                        <span class="icon">
                            <ion-icon name="log-out-outline"></ion-icon>
                        </span>
                        <span class="title">Déconnexion</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- ============================= Main================== -->
        <div class="main">
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

            <!-- ======================  Cards================ -->


            <!-- ================ Order Details List===================== -->
            <div id="dashboardContent" class="content-section active">
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

                                        <td><?= htmlspecialchars($user['Name'] ?? 'N/A') ?></td>
                                        <td><?= htmlspecialchars($user['Surname'] ?? 'N/A') ?></td>
                                        <td><?= htmlspecialchars($user['Telephone'] ?? 'N/A') ?></td>
                                        <td><?= htmlspecialchars($user['Adresse_mail'] ?? 'N/A') ?></td>
                                        <td><?= htmlspecialchars($user['Type_ticket'] ?? 'N/A') ?></td>
                                        <td><?= htmlspecialchars($user['Quantite'] ?? 'N/A') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                   
                </div>



            </div>
            <style>
                #statistiquesContent h2 {
                    margin-top: 30px;
                    color: #333;
                    border-bottom: 2px solid #ddd;
                    padding-bottom: 10px;
                    margin-bottom: 20px;
                }

                #statistiquesContent form {
                    margin-bottom: 20px;
                }

                #statistiquesContent label {
                    font-weight: bold;
                }

                #statistiquesContent input[type="number"],
                input[type="submit"] {
                    padding: 10px;
                    margin: 5px 0;
                }

                #statistiquesContent input[type="submit"] {
                    background-color: #B0A536;
                    color: white;
                    border: none;
                    cursor: pointer;
                    border-radius: 16px;
                }

                input[type="submit"]:hover {
                    background-color: #B0A536;
                }

                #statistiquesContent table {
                    width: 100%;
                    border-radius: 16px;
                    border-collapse: collapse;
                    margin: 100px 20px;
                    background-color: #fff;
                    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                }

                #statistiquesContent th {
                    background-color: #fff;
                }

                #statistiquesContent th,
                td {
                    padding: 10px 10px;
                    text-align: left;
                    border-bottom: 1px solid #ddd;
                }

                #statistiquesContent th {
                    padding: 30px;
                    color: #333;
                    margin-bottom: 20px;
                }

                #statistiquesContent tr:nth-child(even) {
                    background-color: #fff;
                }

                #statistiquesContent tr:hover {
                    background-color: #B0A536;
                }

                #statistiquesContent td[colspan="8"] {
                    text-align: center;
                    font-style: italic;
                }
            </style>
            <div id="statistiquesContent" class="content-section">
                <!-- Page vide pour l'instant -->
                <h2>Statistiques</h2>
                <form method="GET" action="">
                    <div class="filter" style="margin: 20px;">

                        <label for="edition"><ion-icon name="filter-circle-outline" style="font-size: 30px;"></ion-icon></label>
                        <input type="number" id="edition" name="edition" value="<?= htmlspecialchars($filterEdition) ?>">
                        <input type="submit" value="Filtrer">
                    </div>
                </form>

                <table>
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Téléphone</th>
                            <th>Email</th>
                            <th>Quantité</th>
                            <th>Type ticket</th>
                            <th>Édition</th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($userStories)) : ?>
                            <tr>
                                <td colspan="8" style="color: #f44336;">Aucun donnée disponible pour cette édition.</td>
                            </tr>
                        <?php else : ?>
                            <?php foreach ($userStories as $story) : ?>
                                <tr>
                                    <td><?= htmlspecialchars($story['Name']) ?></td>
                                    <td><?= htmlspecialchars($story['Surname']) ?></td>
                                    <td><?= htmlspecialchars($story['Telephone']) ?></td>
                                    <td><?= htmlspecialchars($story['Adresse_mail']) ?></td>
                                    <td><?= htmlspecialchars($story['Quantite']) ?></td>
                                    <td><?= htmlspecialchars($story['Type_ticket']) ?></td>
                                    <td><?= htmlspecialchars($story['Edition']) ?></td>

                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>

                
                </table>

            </div>

            <div id="modifierContent" class="content-section">
                <!-- Section modification -->

                <section id="modif">
    <h2>Modifier l'événement <ion-icon name="pencil-outline"></ion-icon></h2>
    <form action="" id="event-form" method="post">
        <input type="text" name="Date" id="Date" placeholder="Date de l'événement" required><br>
        <input type="text" name="Lieu" id="Lieu" placeholder="Lieu" required><br>
        <input type="text" name="Edition" id="Edition" placeholder="Édition" required><br>
        <input type="text" name="Admin" id="Edition" placeholder="Admin" required><br>
        <button type="submit" name="update" style="cursor: pointer">Modifier<ion-icon name="open-outline"></ion-icon></button>
       

    </form>
    <form method="post">
                        <button type="submit" name="endEdition" class="endEditionBtn">Fin d'Édition</button>
                    </form>
    
</section>

                
            

                <!-- =========== bouton de fin================ -->
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


    <!-- ============== script================== -->
    <script src="dashboard.js"></script>

    <!-- ============================ Icone=========================================: -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const navigationLinks = document.querySelectorAll('.navigation ul li a');
            const contentSections = document.querySelectorAll('.content-section');

            navigationLinks.forEach(link => {
                link.addEventListener('click', (event) => {
                    event.preventDefault();
                    const targetId = link.getAttribute('href').substring(1); // Extrait l'ID de la section cible

                    // Masquer toutes les sections
                    contentSections.forEach(section => {
                        section.classList.remove('active');
                    });

                    // Afficher la section cible
                    const targetSection = document.getElementById(targetId);
                    if (targetSection) {
                        targetSection.classList.add('active');
                    }
                });
            });

            // Logout modal functionality
            const logoutBtn = document.getElementById('logoutBtn');
            const logoutModal = document.getElementById('logoutModal');
            const closeModal = document.querySelector('.modal .close');
            const confirmLogout = document.getElementById('confirmLogout');
            const cancelLogout = document.getElementById('cancelLogout');

            logoutBtn.addEventListener('click', () => {
                logoutModal.style.display = 'block';
            });

            closeModal.addEventListener('click', () => {
                logoutModal.style.display = 'none';
            });

            confirmLogout.addEventListener('click', () => {
                // Code to handle logout
                window.location.href = 'deconnexion.php'; // Remplacez ceci par la route de déconnexion réelle
            });

            cancelLogout.addEventListener('click', () => {
                logoutModal.style.display = 'none';
            });



        });
    </script>
</body>

</html>