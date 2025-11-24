<?php

require_once __DIR__ . '/config.php';


$manufacturers = [];
// pobieram producentów
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
    <title>Błędy dźwiękowe BIOS - wyszukiwanie</title>
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
                        <span>Błędy dźwiękowe BIOS / UEFI</span>
                    </div>
                    <div class="app-subtitle">Wprowadź liczbę krótkich i długich sygnałów lub sekwencję beepów.</div>
                </div>
                <div class="chip">
                    <span class="chip-dot chip-dot-live"></span>
                    <span>Tryb: Beep codes</span>
                </div>
            </div>
            <div class="breadcrumbs">
                <a href="index.php">Strona główna</a>
                <span>/</span>
                <span>Błędy dźwiękowe</span>
            </div>
        </header>

        <main class="form-layout">
            <section class="form-card" id="beep-form">
                <div class="js-form-messages"></div>

                <form action="search.php" method="post" novalidate>
                    <input type="hidden" name="error_type" value="beep">

                    <div class="form-group">
                        <label class="label">Producent <span>(wymagane)</span></label>
                        <select name="manufacturer_id" class="select" required>
                            <option value="">Wybierz producenta...</option>
                            <?php foreach ($manufacturers as $m): ?>
                                <option value="<?php echo (int)$m['id']; ?>"><?php echo htmlspecialchars($m['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-helper">Wybierz producenta BIOS / płyty głównej z listy.</div>
                    </div>

                    <div class="form-group">
                        <label class="label">Liczba sygnałów krótkich i długich</label>
                        <div class="color-row">
                            <span class="color-chip">
                                <span class="color-preview color-blue"></span>
                                <span>Krótkie sygnały</span>
                            </span>
                            <input class="input color-count-input" type="number" name="short_beeps" min="0" max="10" placeholder="0">
                        </div>
                        <div class="color-row">
                            <span class="color-chip">
                                <span class="color-preview color-red"></span>
                                <span>Długie sygnały</span>
                            </span>
                            <input class="input color-count-input" type="number" name="long_beeps" min="0" max="10" placeholder="0">
                        </div>
                        <div class="form-helper">Jeśli BIOS podaje prostą kombinację (np. „1 długi, 2 krótkie”), uzupełnij pola powyżej.</div>
                    </div>

                    <div class="form-group">
                        <label class="label">Sekwencja sygnałów <span>(opcjonalnie)</span></label>
                        <input class="input" type="text" name="sequence" placeholder="np. 3 short, pause, 2 long">
                        <div class="form-helper">Możesz opisać złożoną sekwencję, np. „3 short, pause, 3 long”.</div>
                    </div>

                    <div class="actions-row">
                        <a href="index.php" class="btn btn-secondary">Powrót</a>
                        <button type="submit" class="btn btn-primary">Szukaj błędu</button>
                    </div>
                </form>
            </section>

            <aside class="form-card">
                <h2 class="section-title">Jak liczyć sygnały dźwiękowe?</h2>
                <p class="small-muted">Krótkie sygnały to krótkie „piknięcia”, długie – wyraźnie dłuższe tony. Zlicz je osobno.</p>
                <p class="small-muted">Niektóre BIOS-y stosują sekwencje rozdzielone pauzami (np. 3 krótkie, pauza, 2 długie) – możesz je opisać w polu sekwencji.</p>
            </aside>
        </main>
    </div>
</div>
<script src="script.js"></script>
</body>
</html>
