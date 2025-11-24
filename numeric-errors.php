<?php

require_once __DIR__ . '/config.php';

// Pobranie listy producentów do dropdownu
$manufacturers = [];
$sqlM = "SELECT id, name FROM manufacturers ORDER BY name";
$resM = mysqli_query($conn, $sqlM);
if ($resM) {
    while ($row = mysqli_fetch_assoc($resM)) {
        $manufacturers[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Błędy numeryczne BIOS / kody POST - wyszukiwanie</title>
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
                        <span>Kody numeryczne BIOS / POST</span>
                    </div>
                    <div class="app-subtitle">Wpisz kod wyświetlany na ekranie, diodach POST lub Q-Code.</div>
                </div>
                <div class="chip">
                    <span class="chip-dot chip-dot-live"></span>
                    <span>Tryb: Numeric / POST</span>
                </div>
            </div>
            <div class="breadcrumbs">
                <a href="index.php">Strona główna</a>
                <span>/</span>
                <span>Błędy numeryczne</span>
            </div>
        </header>

        <main class="form-layout">
            <section class="form-card" id="numeric-form">
                <div class="js-form-messages"></div>

                <form action="search.php" method="post" novalidate>
                    <input type="hidden" name="error_type" value="numeric">

                    <div class="form-group">
                        <label class="label">Producent <span>(wymagane)</span></label>
                        <select name="manufacturer_id" class="select" required>
                            <option value="">Wybierz producenta...</option>
                            <?php foreach ($manufacturers as $m): ?>
                                <option value="<?php echo (int)$m['id']; ?>"><?php echo htmlspecialchars($m['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-helper">Wybierz producenta BIOS / płyty lub komputera.</div>
                    </div>

                    <div class="form-group">
                        <label class="label">Kod błędu <span>(wymagane)</span></label>
                        <input class="input" type="text" name="error_code" placeholder="np. 0x00000050, POST 25, A2" required>
                        <div class="form-helper">Możesz wpisać pełny kod lub jego fragment, np. „0x00000050” lub „A2”.</div>
                    </div>

                    <div class="actions-row">
                        <a href="index.php" class="btn btn-secondary">Powrót</a>
                        <button type="submit" class="btn btn-primary">Szukaj błędu</button>
                    </div>
                </form>
            </section>

            <aside class="form-card">
                <h2 class="section-title">Gdzie znaleźć kod błędu?</h2>
                <p class="small-muted">Kody mogą być wyświetlane jako komunikaty na ekranie, jako kody POST na wyświetlaczu płyty głównej lub jako kody błysków diod.</p>
                <p class="small-muted">Wynik zawiera opis problemu oraz sugerowane rozwiązanie serwisowe.</p>
            </aside>
        </main>
    </div>
</div>
<script src="script.js"></script>
</body>
</html>
