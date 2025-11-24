<?php

require_once __DIR__ . '/config.php';


$manufacturers = [];
// tutaj biore sobie listę producentów z bazy do selecta
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
    <title>Błędy świetlne BIOS - wyszukiwanie</title>
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
                        <span>Błędy świetlne BIOS / UEFI</span>
                    </div>
                    <div class="app-subtitle">Wprowadź sekwencję błysków diod LED, aby odnaleźć opis błędu.</div>
                </div>
                <div class="chip">
                    <span class="chip-dot chip-dot-live"></span>
                    <span>Tryb: LED / Flash</span>
                </div>
            </div>
            <div class="breadcrumbs">
                <a href="index.php">Strona główna</a>
                <span>/</span>
                <span>Błędy świetlne</span>
            </div>
        </header>

        <main class="form-layout">
            <section class="form-card" id="flash-form">
                <div class="js-form-messages"></div>

                <form action="search.php" method="post" novalidate>
                    <input type="hidden" name="error_type" value="flash">

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
                        <label class="label">Sekwencja błysków LED <span>(podaj liczbę błysków dla używanych kolorów)</span></label>

                        <div class="color-row">
                            <label class="color-chip">
                                <input type="checkbox" name="colors[]" value="blue">
                                <span class="color-preview color-blue"></span>
                                <span>Niebieski</span>
                            </label>
                            <input class="input color-count-input" type="number" name="blue_count" data-color-count min="0" max="10" placeholder="0">
                        </div>

                        <div class="color-row">
                            <label class="color-chip">
                                <input type="checkbox" name="colors[]" value="orange">
                                <span class="color-preview color-orange"></span>
                                <span>Pomarańczowy</span>
                            </label>
                            <input class="input color-count-input" type="number" name="orange_count" data-color-count min="0" max="10" placeholder="0">
                        </div>

                        <div class="color-row">
                            <label class="color-chip">
                                <input type="checkbox" name="colors[]" value="red">
                                <span class="color-preview color-red"></span>
                                <span>Czerwony</span>
                            </label>
                            <input class="input color-count-input" type="number" name="red_count" data-color-count min="0" max="10" placeholder="0">
                        </div>

                        <div class="color-row">
                            <label class="color-chip">
                                <input type="checkbox" name="colors[]" value="green">
                                <span class="color-preview color-green"></span>
                                <span>Zielony</span>
                            </label>
                            <input class="input color-count-input" type="number" name="green_count" data-color-count min="0" max="10" placeholder="0">
                        </div>

                        <div class="color-row">
                            <label class="color-chip">
                                <input type="checkbox" name="colors[]" value="yellow">
                                <span class="color-preview color-yellow"></span>
                                <span>Żółty</span>
                            </label>
                            <input class="input color-count-input" type="number" name="yellow_count" data-color-count min="0" max="10" placeholder="0">
                        </div>

                        <div class="color-row">
                            <label class="color-chip">
                                <input type="checkbox" name="colors[]" value="white">
                                <span class="color-preview color-white"></span>
                                <span>Biały</span>
                            </label>
                            <input class="input color-count-input" type="number" name="white_count" data-color-count min="0" max="10" placeholder="0">
                        </div>

                        <div class="form-helper">Zaznacz kolory, które występują w sekwencji, i wpisz liczbę błysków dla każdego z nich.</div>
                    </div>

                    <div class="actions-row">
                        <a href="index.php" class="btn btn-secondary">Powrót</a>
                        <button type="submit" class="btn btn-primary">Szukaj błędu</button>
                    </div>
                </form>
            </section>

            <aside class="form-card">
                <h2 class="section-title">Wskazówki do odczytu kodów LED</h2>
                <p class="small-muted">Policz dokładnie liczbę błysków dla każdego koloru. Jeżeli diody migają w pętli, obserwuj cały cykl.</p>
                <p class="small-muted">W wynikach wyszukiwania zobaczysz opis problemu oraz sugerowane działania serwisowe.</p>
                <p class="small-muted">Jeśli wynik nie będzie dokładny, aplikacja spróbuje zaproponować zbliżone kombinacje błysków.</p>
            </aside>
        </main>
    </div>
</div>
<script src="script.js"></script>
</body>
</html>
