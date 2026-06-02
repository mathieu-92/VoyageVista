<?php
session_start();
require_once 'config.php';

// Récupération sécurisée de l'ID réservation
$id_reservation = isset($_GET['id_reservation']) ? (int)$_GET['id_reservation'] : 1;

// 1. Récupération des éléments avec gestion d'erreurs
$stmt = $pdo->prepare("SELECT * FROM itineraire WHERE id_reservation = ? ORDER BY date_element ASC");
$stmt->execute([$id_reservation]);
$elements = $stmt->fetchAll();

// 2. Calcul automatique du coût total
$total_stmt = $pdo->prepare("SELECT SUM(prix_element) as total FROM itineraire WHERE id_reservation = ?");
$total_stmt->execute([$id_reservation]);
$prix_total = $total_stmt->fetchColumn() ?: 0;
?>

<div style="font-family: sans-serif; max-width: 600px; margin: 20px auto; border: 1px solid #ddd; padding: 25px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
    <h2 style="color: #2c3e50; border-bottom: 2px solid #3498db; padding-bottom: 10px;">
        <i class="fa-solid fa-suitcase-rolling"></i> Détail de votre séjour #<?= $id_reservation ?>
    </h2>

    <?php if (empty($elements)): ?>
        <p style="color: #e74c3c;">Aucun élément trouvé pour cet itinéraire.</p>
    <?php else: ?>
        <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
            <thead>
                <tr style="background: #f4f4f4; text-align: left;">
                    <th style="padding: 10px;">Date</th>
                    <th style="padding: 10px;">Type</th>
                    <th style="padding: 10px;">Description</th>
                    <th style="padding: 10px;">Prix</th>
                </tr>
            </thead> 
            <tbody>
                <?php foreach ($elements as $el): 
                    // Icônes dynamiques basées sur le type
                    $type = strtoupper($el['type_element']);
                    $icon = ($type == 'VOL') ? 'fa-plane' : (($type == 'HEB') ? 'fa-bed' : 'fa-person-running');
                ?>
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 10px; font-size: 0.9em;"><?= date('d/m', strtotime($el['date_element'])) ?></td>
                        <td style="padding: 10px;"><i class="fa-solid <?= $icon ?>"></i></td>
                        <td style="padding: 10px;"><strong><?= htmlspecialchars($el['nom_element']) ?></strong></td>
                        <td style="padding: 10px; text-align: right;"><?= number_format($el['prix_element'], 2) ?> €</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div style="background: #ecf0f1; padding: 15px; border-radius: 8px; text-align: right; margin-top: 20px;">
            <h3 style="margin: 0; color: #2c3e50;">
                Coût total automatique : <span style="color: #27ae60;"><?= number_format($prix_total, 2) ?> €</span>
            </h3>
        </div>
    <?php endif; ?>

    <div style="margin-top: 20px; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer; background: #3498db; color: white; border: none; border-radius: 5px;">
            <i class="fa-solid fa-print"></i> Imprimer l'itinéraire
        </button>
    </div>
</div> 