<?php
require_once("bd.php");

if (isset($_POST['submit'])) {
    // Récupération et nettoyage des données du formulaire
    $Name = htmlspecialchars($_POST['Name']);
    $Surname = htmlspecialchars($_POST['Surname']);
    $Telephone = htmlspecialchars($_POST['Telephone']);
    $Adresse_mail = htmlspecialchars($_POST['Adresse_mail']);
    $Quantite = htmlspecialchars($_POST['Quantite']);
    $type_ticket = htmlspecialchars($_POST['type_ticket']);

    // Préparation et exécution de la requête SQL

        $requete = $bdd->prepare("INSERT INTO user(Name, Surname, Telephone, Adresse_mail, Quantite, type_ticket) VALUES (?, ?, ?, ?, ?, ?)");
        $requete->execute([$Name, $Surname, $Telephone, $Adresse_mail, $Quantite, $type_ticket]);
        
}




?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Ticket</title>
    <link rel="stylesheet" href="ticket.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <!-- <script src="https://www.google.com/recaptcha/api.js" async defer></script> -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>

<body>

    <header>
        <div id="menu">
            <div class="logo">
                <p><a href="index.php">Espace Ticket </a></p>
            </div>

            <div class="nav">
                <div id="closedButton"></div>
                <div id="Countdown" data-time="2024-09-23T15:13:00+0000">
                    <div class="bloc">
                        <strong id="Jours"></strong>
                        <em>Jours</em>
                    </div>
                    <div class="bloc">
                        <strong id="Heures"></strong>
                        <em>Heures</em>
                    </div>
                    <div class="bloc">
                        <strong id="Minutes"></strong>
                        <em>Minutes</em>
                    </div>
                    <div class="bloc">
                        <strong id="Secondes"></strong>
                        <em>Secondes</em>
                    </div>
                    <div class="bloc">
                        <ion-icon name="alarm-outline" id="icon"></ion-icon>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main>
        <section id="Dad">
         

            <section id="box">
                <div id="sideleft">
                    <p>Passe simple: <span id="color">2000 FCFA</span></p>
                    <p>Pass VIP: <span id="color">5000 FCFA</span></p>
                    <img src="img/Qrcode_wikipedia_fr_v2clean.png" alt="QR Code">
                    <button id="downloadPDF" style="display: none;"><a href="telecharger.php">Télécharger le ticket PDF</a>
                        <ion-icon name="qr-code-outline"></ion-icon>
                    </button>
                </div>

                <div id="sideright">
                    <h1>Acheter un ticket</h1>

                    <form action="" id="formulaire" method="post">
                        <select name="type_ticket" id="type" required>
                            <option value="2000">Pass simple</option>
                            <option value="5000">Pass VIP</option>
                        </select>
                        <div class="line"></div>
                        <div>
                            <p>Nom</p>
                            <input type="text" name="Name" placeholder="Entrer votre nom et prénom" required>
                        </div>
                        <div>
                            <p>Prénom</p>
                            <input type="text" name="Surname" placeholder="Entrer votre prénom" required>
                        </div>
                        <div>
                            <p>Numéro de téléphone</p>
                            <input type="text" name="Telephone" placeholder="Entrer votre numéro de téléphone" required>
                        </div>
                        <div>
                            <p>Adresse mail</p>
                            <input type="email" name="Adresse_mail" id="mail" placeholder="Entrer votre mail" required>
                        </div>
                        <div>
                            <p>Nombre de tickets</p>
                            <input type="number" name="Quantite" id="quantity" placeholder="Quantité" required>
                        </div>
                        <div id="sommes">
                            <span>Total:</span>
                            <p id="price"></p>
                        </div>
                    
                        <button type="submit" id="send" name="submit" style="margin-top: 40%;">Payer</button>
                        </form>
     

</body>
</html>

                     
                </div>
                
            </section>
        </section>
    </main>

 

    <?php require_once("footer.php") ?>

    <script src="https://cdn.fedapay.com/checkout.js?v=1.1.7"></script>
    <script>
        var priceElement = document.getElementById("price");
        var quantityElement = document.getElementById("quantity");
        var typeElement = document.getElementById("type");
        var totalPrice = 0;
        var pdfDoc;

        function calculateTotalPrice() {
            var quantity = parseInt(quantityElement.value, 10) || 0;
            var ticketPrice = parseInt(typeElement.value, 10) || 0;
            totalPrice = quantity * ticketPrice;
            priceElement.textContent = totalPrice + " FCFA";
        }

        quantityElement.addEventListener("input", calculateTotalPrice);
        typeElement.addEventListener("change", calculateTotalPrice);

        document.querySelector('input[name="Telephone"]').addEventListener('input', function (event) {
            const value = event.target.value;

            if (!/^\d*$/.test(value)) {
                event.target.value = value.replace(/\D/g, '');
                event.target.style.border = "2px solid red";
            } else {
                event.target.style.border = "";
            }
        });

        document.getElementById("formulaire").addEventListener("submit", function (e) {
         e.preventDefault(); // Empêche la soumission du formulaire pour les tests

            const quantity = document.getElementById("quantity").value;
            const mail = document.getElementById("mail").value;
            const name = document.querySelector('input[name="Name"]').value;
            const surname = document.querySelector('input[name="Surname"]').value;
            const phone = document.querySelector('input[name="Telephone"]').value;

            if (!quantity || isNaN(quantity) || quantity <= 0) {
                alert("Veuillez entrer un nombre de tickets valide.");
                return;
            }

            FedaPay.init('#send', {
                public_key: 'pk_live_HrRJONq9vKHf_trgjKqhoYTt',
                transaction: {
                    amount: totalPrice,
                    description: 'Achat de tickets'
                },
                customer: {
                    email: mail,
                    lastname: name,
                    firstname: surname,
                  
                },

                onComplete: function (transaction) {
                    generateQRCodeAndPDF(name, phone, mail, quantity);
                },
                onError: function (error) {
                    alert("Erreur de paiement : " + error.message);
                }
            });

            FedaPay.open();
        });

        function generateQRCodeAndPDF(name, phone, email, quantity) {
            const ticketType = typeElement.options[typeElement.selectedIndex].text;
            const ticketPrice = typeElement.value;
            const qrCodes = [];

            for (let i = 0; i < quantity; i++) {
                const qr = new QRious({
                    value: `Nom: ${name}, Téléphone: ${phone}, Email: ${email}, Type de ticket: ${ticketType}, Prix unitaire: ${ticketPrice} FCFA, Ticket: ${i + 1}/${quantity}`,
                    size: 150
                });
                qrCodes.push(qr.toDataURL());
            }

            generatePDF(name, phone, email, quantity, qrCodes);
        }

        function generatePDF(name, phone, email, quantity, qrCodes) {
            const { jsPDF } = window.jspdf;
            pdfDoc = new jsPDF();

            var img = new Image();
            img.src = 'img/412327773_949807930084807_3755909874633460271_n.jpg';

            img.onload = function () {
                qrCodes.forEach((qrCode, index) => {
                    if (index > 0) {
                        pdfDoc.addPage();
                    }

                    pdfDoc.addImage(img, 'JPEG', 0, 0, 210, 297);

                    pdfDoc.setFont('helvetica', 'bold');
                    pdfDoc.setFontSize(16);
                    pdfDoc.setTextColor(255, 255, 255);
                    pdfDoc.text('Ticket', 105, 20, null, null, 'center');

                    pdfDoc.setFontSize(12);
                    pdfDoc.setTextColor(0, 0, 0);
                    pdfDoc.text(`Nom: ${name}`, 10, 30);
                    pdfDoc.text(`Téléphone: ${phone}`, 10, 40);
                    pdfDoc.text(`Email: ${email}`, 10, 50);
                    pdfDoc.text(`Type de ticket: ${typeElement.options[typeElement.selectedIndex].text}`, 10, 60);
                    pdfDoc.text(`Prix unitaire: ${typeElement.value} FCFA`, 10, 70);
                    pdfDoc.text(`Quantité: ${quantity}`, 10, 80);
                    pdfDoc.text(`Total: ${totalPrice} FCFA`, 10, 90);

                    const posY = 100;
                    pdfDoc.addImage(qrCode, 'PNG', 10, posY, 50, 50);
                    pdfDoc.text(`Ticket ${index + 1}`, 70, posY + 30);
                });

                document.getElementById("downloadPDF").style.display = "block";
            };

            img.onerror = function () {
                alert("Erreur de chargement de l'image.");
            };
        }

        document.getElementById("downloadPDF").addEventListener("click", function () {
            pdfDoc.save('tickets.pdf');
        });

        const downloadButton = document.getElementById("downloadPDF");
        downloadButton.style.width = "300px";
        downloadButton.style.padding = "10px";
        downloadButton.style.marginTop = "10px";
        downloadButton.style.marginLeft = "55px";
        downloadButton.style.marginRight = "auto";
        downloadButton.style.backgroundColor = "#7f6522";
        downloadButton.style.color = "white";
        downloadButton.style.border = "none";
        downloadButton.style.borderRadius = "5px";
        downloadButton.style.fontSize = "15px";

        // Compte à rebours
        function disableFormFields() {
            var formFields = document.querySelectorAll('#formulaire input, #formulaire button');
            formFields.forEach(function (field) {
                field.disabled = true;
            });
        }

        function refreshCountdown() {
            var countdownElement = document.getElementById("Countdown");
            var endTime = new Date(countdownElement.dataset.time).getTime();
            var now = Date.now();

            if (now >= endTime) {
                document.getElementById("closedButton").classList.remove("hidden");
                disableFormFields();
                return;
            }

            const difference = endTime - now;
            const dif = {
                Jours: Math.floor(difference / (1000 * 60 * 60 * 24)),
                Heures: Math.floor((difference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)),
                Minutes: Math.floor((difference % (1000 * 60 * 60)) / (1000 * 60)),
                Secondes: Math.floor((difference % (1000 * 60)) / 1000)
            }

            function formatNumber(value) {
                return value < 10 ? '0' + value : value;
            }

            document.getElementById("Jours").innerText = formatNumber(dif.Jours);
            document.getElementById("Heures").innerText = formatNumber(dif.Heures);
            document.getElementById("Minutes").innerText = formatNumber(dif.Minutes);
            document.getElementById("Secondes").innerText = formatNumber(dif.Secondes);

            setTimeout(refreshCountdown, 1000);
        }

        refreshCountdown();





    </script>
</body>

</html>
