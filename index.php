<?php

require_once __DIR__ . '/config.php';


function fetch_top_errors($conn, $table, $fields, $type)
{
    
    $results = [];
    $sql = "SELECT e.id, e.search_count, m.name AS manufacturer_name, $fields
            FROM $table e
            JOIN manufacturers m ON e.manufacturer_id = m.id
            ORDER BY e.search_count DESC, e.id ASC
            LIMIT 5";

    $query = mysqli_query($conn, $sql);
    if ($query) {
        while ($row = mysqli_fetch_assoc($query)) {
            $row['error_type'] = $type;
            $results[] = $row;
        }
    }
    return $results;
}

$top_flash = fetch_top_errors($conn, 'flash_errors',
    "CONCAT('LED: ', red_count, 'R/', blue_count, 'B/', orange_count, 'O/', green_count, 'G/', yellow_count, 'Y/', white_count, 'W') AS error_label",
    'flash'
);

$top_beep = fetch_top_errors($conn, 'beep_errors',
    "CONCAT('Beep: ', COALESCE(short_beeps,0), 'x short / ', COALESCE(long_beeps,0), 'x long') AS error_label",
    'beep'
);

$top_numeric = fetch_top_errors($conn, 'numeric_errors',
    "error_code AS error_label",
    'numeric'
);

$top_all = array_merge($top_flash, $top_beep, $top_numeric);

usort($top_all, function ($a, $b) {
    // sortowanie listy
    return (int)$b['search_count'] <=> (int)$a['search_count'];
});

// skracanie najczestszych bledow
$top_all = array_slice($top_all, 0, 10);
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Aplikacja BIOS Errors - wyszukiwanie błędów BIOS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="app-shell">
    <div class="card">
        <header class="header">
            <div class="header-top">
                <div>
                    <div class="app-title">
                        <span class="logo-dot"></span>
                        <span>Diagnostyka błędów BIOS</span>
                    </div>
                    <div class="app-subtitle">Wyszukiwarka kodów świetlnych, dźwiękowych i numerycznych BIOS / UEFI</div>
                </div>
                <div class="chip">
                    <span class="chip-dot chip-dot-live"></span>
                    <span>Połączenie z bazą: <?php echo $conn ? 'OK' : 'Błąd'; ?></span>
                </div>
            </div>
            <div class="breadcrumbs">
                <span>Strona główna</span>
            </div>
        </header>

        <main class="main-grid">
            <section>
                <h2 class="section-title">Wybierz typ błędu</h2>
                <p class="section-subtitle">Rozpocznij diagnostykę, wybierając formę sygnalizacji błędu BIOS / UEFI.</p>
                <div class="tile-grid">
                    <a href="flash-errors.php" class="menu-tile menu-tile-type-flash">
                        <div class="menu-tile-header">
                            <div class="menu-tile-title">Błędy świetlne (LED / Flash)</div>
                            <span class="menu-tile-badge">Diody LED</span>
                        </div>
                        <div class="menu-tile-desc">Kody błysków diod LED sygnalizujące problemy z CPU, RAM, GPU, zasilaniem i innym sprzętem.</div>
                        <div class="menu-tile-footer">
                            <span class="menu-tile-cta">Rozpocznij diagnozę</span>
                        </div>
                    </a>

                    <a href="beep-errors.php" class="menu-tile menu-tile-type-beep">
                        <div class="menu-tile-header">
                            <div class="menu-tile-title">Błędy dźwiękowe (Beep)</div>
                            <span class="menu-tile-badge">Sygnały</span>
                        </div>
                        <div class="menu-tile-desc">Sekwencje krótkich i długich sygnałów dźwiękowych generowanych przez głośniczek systemowy.</div>
                        <div class="menu-tile-footer">
                            <span class="menu-tile-cta">Rozszyfruj sygnały</span>
                        </div>
                    </a>

                    <a href="numeric-errors.php" class="menu-tile menu-tile-type-numeric">
                        <div class="menu-tile-header">
                            <div class="menu-tile-title">Błędy numeryczne / kody POST</div>
                            <span class="menu-tile-badge">LED</span>
                        </div>
                        <div class="menu-tile-desc">Kody wyświetlane na ekranie, diodach POST lub Q-Code na płycie głównej.</div>
                        <div class="menu-tile-footer">
                            <span class="menu-tile-cta">Wyszukaj kod</span>
                        </div>
                    </a>
                </div>
            </section>

            <aside>
                <h2 class="section-title">Najczęściej wyszukiwane błędy</h2>
                <p class="section-subtitle">Lista 10 najpopularniejszych błędów BIOS / UEFI na podstawie liczby wyszukiwań.</p>
                <div class="list-compact">
                    <?php if (empty($top_all)): ?>
                        <div class="small text-muted">Brak danych o wyszukiwaniach. Wykonaj pierwsze wyszukanie, aby zbudować statystyki.</div>
                    <?php else: ?>
                        <?php foreach ($top_all as $row): ?>
                            <div class="list-item-card">
                                <div class="list-item-header">
                                    <div>
                                        <div><strong><?php echo htmlspecialchars($row['error_label']); ?></strong></div>
                                        <div class="small text-muted">
                                            <?php
                                            if ($row['error_type'] === 'flash') {
                                                echo 'Typ: Błąd świetlny (LED)';
                                            } elseif ($row['error_type'] === 'beep') {
                                                echo 'Typ: Błąd dźwiękowy (Beep)';
                                            } else {
                                                echo 'Typ: Błąd numeryczny / kod POST';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <div style="text-align: right;">
                                        <div class="badge-manufacturer"><?php echo htmlspecialchars($row['manufacturer_name']); ?></div>
                                        <div class="badge-counter"><?php echo (int)$row['search_count']; ?> wysz.</div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </aside>
        </main>
    </div>
</div>
<script src="script.js"></script>
</body>
</html>
