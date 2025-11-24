<?php

require_once __DIR__ . '/config.php';


$error_message = '';
$result = null;
$suggestions = [];
$type_label = '';


function get_post_int($name)
{
    // pobieranie liczb 
    if (!isset($_POST[$name]) || $_POST[$name] === '') {
        return null;
    }
    return (int)$_POST[$name];
}


$error_type = isset($_POST['error_type']) ? $_POST['error_type'] : '';
$manufacturer_id = get_post_int('manufacturer_id');

//podzial; na bleddy

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $error_message = 'Nieprawidłowy sposób wywołania strony wyszukiwania.';
} elseif (!$error_type || !$manufacturer_id) {
    $error_message = 'Brak wymaganego typu błędu lub producenta.';
} else {
    
    if ($error_type === 'flash') {
        $type_label = 'błąd świetlny (LED)';

        $blue = get_post_int('blue_count') ?? 0;
        $orange = get_post_int('orange_count') ?? 0;
        $red = get_post_int('red_count') ?? 0;
        $green = get_post_int('green_count') ?? 0;
        $yellow = get_post_int('yellow_count') ?? 0;
        $white = get_post_int('white_count') ?? 0;

        if ($blue + $orange + $red + $green + $yellow + $white === 0) {
            $error_message = 'Podaj liczbę błysków dla co najmniej jednego koloru.';
        } else {
            $sql = "SELECT f.*, m.name AS manufacturer_name
                    FROM flash_errors f
                    JOIN manufacturers m ON f.manufacturer_id = m.id
                    WHERE f.manufacturer_id = ?
                      AND f.blue_count = ?
                      AND f.orange_count = ?
                      AND f.red_count = ?
                      AND f.green_count = ?
                      AND f.yellow_count = ?
                      AND f.white_count = ?
                    LIMIT 1";

            $stmt = mysqli_prepare($conn, $sql);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 'iiiiiii', $manufacturer_id, $blue, $orange, $red, $green, $yellow, $white);
                mysqli_stmt_execute($stmt);
                $res = mysqli_stmt_get_result($stmt);
                if ($res && $row = mysqli_fetch_assoc($res)) {
                    $result = $row;
                    // zwiekszanie priorytetu wyszukiwania
                    $updateSql = "UPDATE flash_errors SET search_count = search_count + 1 WHERE id = " . (int)$row['id'];
                    mysqli_query($conn, $updateSql);
                }
                mysqli_stmt_close($stmt);
            }

            if (!$result) {
                // szukanie zblizonej kombinacji
                $sugSql = "SELECT f.*, m.name AS manufacturer_name,
                                  ABS(f.blue_count - ?) + ABS(f.orange_count - ?) + ABS(f.red_count - ?) +
                                  ABS(f.green_count - ?) + ABS(f.yellow_count - ?) + ABS(f.white_count - ?) AS diff
                           FROM flash_errors f
                           JOIN manufacturers m ON f.manufacturer_id = m.id
                           WHERE f.manufacturer_id = ?
                           ORDER BY diff ASC, f.id ASC
                           LIMIT 5";
                $sugStmt = mysqli_prepare($conn, $sugSql);
                if ($sugStmt) {
                    mysqli_stmt_bind_param($sugStmt, 'iiiiiii', $blue, $orange, $red, $green, $yellow, $white, $manufacturer_id);
                    mysqli_stmt_execute($sugStmt);
                    $sugRes = mysqli_stmt_get_result($sugStmt);
                    if ($sugRes) {
                        while ($row = mysqli_fetch_assoc($sugRes)) {
                            $suggestions[] = $row;
                        }
                    }
                    mysqli_stmt_close($sugStmt);
                }
            }
        }
    } elseif ($error_type === 'beep') {
        $type_label = 'błąd dźwiękowy (beep codes)';

        $short_beeps = get_post_int('short_beeps');
        $long_beeps = get_post_int('long_beeps');
        $sequence = isset($_POST['sequence']) ? trim($_POST['sequence']) : '';

        if ((!$short_beeps && !$long_beeps) && $sequence === '') {
            $error_message = 'Uzupełnij liczbę sygnałów lub podaj sekwencję.';
        } else {
            // wyszukanie beepy
            $sql = "SELECT b.*, m.name AS manufacturer_name
                    FROM beep_errors b
                    JOIN manufacturers m ON b.manufacturer_id = m.id
                    WHERE b.manufacturer_id = ?
                      AND (b.short_beeps = ? OR ? IS NULL)
                      AND (b.long_beeps = ? OR ? IS NULL)
                    ORDER BY b.search_count DESC, b.id ASC
                    LIMIT 1";

            $stmt = mysqli_prepare($conn, $sql);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 'iiiii', $manufacturer_id, $short_beeps, $short_beeps, $long_beeps, $long_beeps);
                mysqli_stmt_execute($stmt);
                $res = mysqli_stmt_get_result($stmt);
                if ($res && $row = mysqli_fetch_assoc($res)) {
                    $result = $row;
                    $updateSql = "UPDATE beep_errors SET search_count = search_count + 1 WHERE id = " . (int)$row['id'];
                    mysqli_query($conn, $updateSql);
                }
                mysqli_stmt_close($stmt);
            }

            // proba znalezienia po tekscie
            if (!$result && $sequence !== '') {
                $like = '%' . mysqli_real_escape_string($conn, $sequence) . '%';
                $sql2 = "SELECT b.*, m.name AS manufacturer_name
                         FROM beep_errors b
                         JOIN manufacturers m ON b.manufacturer_id = m.id
                         WHERE b.manufacturer_id = ?
                           AND b.sequence LIKE ?
                         ORDER BY b.search_count DESC, b.id ASC
                         LIMIT 1";
                $stmt2 = mysqli_prepare($conn, $sql2);
                if ($stmt2) {
                    mysqli_stmt_bind_param($stmt2, 'is', $manufacturer_id, $like);
                    mysqli_stmt_execute($stmt2);
                    $res2 = mysqli_stmt_get_result($stmt2);
                    if ($res2 && $row = mysqli_fetch_assoc($res2)) {
                        $result = $row;
                        $updateSql = "UPDATE beep_errors SET search_count = search_count + 1 WHERE id = " . (int)$row['id'];
                        mysqli_query($conn, $updateSql);
                    }
                    mysqli_stmt_close($stmt2);
                }
            }

            // sugestie
            if (!$result) {
                $short_val = $short_beeps ?? 0;
                $long_val = $long_beeps ?? 0;

                $sugSql = "SELECT b.*, m.name AS manufacturer_name,
                                  ABS(COALESCE(b.short_beeps,0) - ?) + ABS(COALESCE(b.long_beeps,0) - ?) AS diff
                           FROM beep_errors b
                           JOIN manufacturers m ON b.manufacturer_id = m.id
                           WHERE b.manufacturer_id = ?
                           ORDER BY diff ASC, b.id ASC
                           LIMIT 5";
                $sugStmt = mysqli_prepare($conn, $sugSql);
                if ($sugStmt) {
                    mysqli_stmt_bind_param($sugStmt, 'iii', $short_val, $long_val, $manufacturer_id);
                    mysqli_stmt_execute($sugStmt);
                    $sugRes = mysqli_stmt_get_result($sugStmt);
                    if ($sugRes) {
                        while ($row = mysqli_fetch_assoc($sugRes)) {
                            $suggestions[] = $row;
                        }
                    }
                    mysqli_stmt_close($sugStmt);
                }
            }
        }
    } elseif ($error_type === 'numeric') {
        $type_label = 'błąd numeryczny / kod POST';

        $code = isset($_POST['error_code']) ? trim($_POST['error_code']) : '';
        if ($code === '') {
            $error_message = 'Podaj kod błędu (np. 0x00000050, POST 25, A2).';
        } else {
            $like = '%' . mysqli_real_escape_string($conn, $code) . '%';

            $sql = "SELECT n.*, m.name AS manufacturer_name
                    FROM numeric_errors n
                    JOIN manufacturers m ON n.manufacturer_id = m.id
                    WHERE n.manufacturer_id = ?
                      AND n.error_code LIKE ?
                    ORDER BY n.search_count DESC, n.id ASC
                    LIMIT 1";

            $stmt = mysqli_prepare($conn, $sql);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 'is', $manufacturer_id, $like);
                mysqli_stmt_execute($stmt);
                $res = mysqli_stmt_get_result($stmt);
                if ($res && $row = mysqli_fetch_assoc($res)) {
                    $result = $row;
                    $updateSql = "UPDATE numeric_errors SET search_count = search_count + 1 WHERE id = " . (int)$row['id'];
                    mysqli_query($conn, $updateSql);
                }
                mysqli_stmt_close($stmt);
            }

            if (!$result) {
                // wyszukiwanie podobnego
                $sugSql = "SELECT n.*, m.name AS manufacturer_name
                           FROM numeric_errors n
                           JOIN manufacturers m ON n.manufacturer_id = m.id
                           WHERE n.manufacturer_id = ?
                             AND n.error_code LIKE ?
                           ORDER BY n.id ASC
                           LIMIT 5";
                $sugStmt = mysqli_prepare($conn, $sugSql);
                if ($sugStmt) {
                    mysqli_stmt_bind_param($sugStmt, 'is', $manufacturer_id, $like);
                    mysqli_stmt_execute($sugStmt);
                    $sugRes = mysqli_stmt_get_result($sugStmt);
                    if ($sugRes) {
                        while ($row = mysqli_fetch_assoc($sugRes)) {
                            $suggestions[] = $row;
                        }
                    }
                    mysqli_stmt_close($sugStmt);
                }
            }
        }
    } else {
        $error_message = 'Nieznany typ błędu.';
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Wynik wyszukiwania błędu BIOS</title>
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
                        <span>Wynik wyszukiwania błędu BIOS</span>
                    </div>
                    <div class="app-subtitle"><?php echo htmlspecialchars($type_label ?: 'Wyszukiwanie błędu BIOS'); ?></div>
                </div>
                <div class="chip">
                    <span class="chip-dot chip-dot-live"></span>
                    <span>Moduł wyników</span>
                </div>
            </div>
            <div class="breadcrumbs">
                <a href="index.php">Strona główna</a>
                <span>/</span>
                <span>Wynik wyszukiwania</span>
            </div>
        </header>

        <main class="form-layout">
            <section class="form-card">
                <?php if ($error_message): ?>
                    <div class="alert alert-error"><?php echo htmlspecialchars($error_message); ?></div>
                <?php endif; ?>

                <?php if ($result): ?>
                    <div class="alert alert-success">Znaleziono dopasowany błąd BIOS.</div>
                    <article class="result-card">
                        <h3>Opis problemu</h3>
                        <div class="result-meta">
                            Producent: <strong><?php echo htmlspecialchars($result['manufacturer_name']); ?></strong>
                            <?php if ($error_type === 'flash'): ?>
                                | LED: R=<?php echo (int)$result['red_count']; ?>, B=<?php echo (int)$result['blue_count']; ?>, O=<?php echo (int)$result['orange_count']; ?>, G=<?php echo (int)$result['green_count']; ?>, Y=<?php echo (int)$result['yellow_count']; ?>, W=<?php echo (int)$result['white_count']; ?>
                            <?php elseif ($error_type === 'beep'): ?>
                                | Krótkie: <?php echo (int)$result['short_beeps']; ?>, Długie: <?php echo (int)$result['long_beeps']; ?>
                                <?php if (!empty($result['sequence'])): ?>
                                    | Sekwencja: <?php echo htmlspecialchars($result['sequence']); ?>
                                <?php endif; ?>
                            <?php elseif ($error_type === 'numeric'): ?>
                                | Kod błędu: <?php echo htmlspecialchars($result['error_code']); ?>
                            <?php endif; ?>
                        </div>

                        <div class="result-section-title">Szczegóły błędu</div>
                        <p><?php echo nl2br(htmlspecialchars($result['description'])); ?></p>

                        <div class="result-section-title">Sugerowane rozwiązanie</div>
                        <p><?php echo nl2br(htmlspecialchars($result['solution'])); ?></p>

                        <p class="small-muted">Ten błąd był wyszukiwany już <?php echo (int)$result['search_count'] + 1; ?> razy (po aktualnym wyszukiwaniu).</p>
                    </article>
                <?php else: ?>
                    <?php if (!$error_message): ?>
                        <div class="alert alert-info">Nie znaleziono dokładnego dopasowania dla podanych parametrów.</div>
                    <?php endif; ?>
                <?php endif; ?>

                <div class="actions-row" style="margin-top: 18px;">
                    <a href="index.php" class="btn btn-secondary">Powrót do strony głównej</a>
                    <?php if ($error_type === 'flash'): ?>
                        <a href="flash-errors.php" class="btn btn-primary">Nowe wyszukiwanie LED</a>
                    <?php elseif ($error_type === 'beep'): ?>
                        <a href="beep-errors.php" class="btn btn-primary">Nowe wyszukiwanie beep</a>
                    <?php elseif ($error_type === 'numeric'): ?>
                        <a href="numeric-errors.php" class="btn btn-primary">Nowe wyszukiwanie kodu</a>
                    <?php endif; ?>
                </div>
            </section>

            <aside class="form-card">
                <h2 class="section-title">Podobne / sugerowane błędy</h2>
                <?php if (!empty($suggestions)): ?>
                    <p class="small-muted">Nie znaleziono dokładnego dopasowania, ale poniżej znajdują się najbardziej zbliżone wpisy dla wybranego producenta.</p>
                    <div class="list-compact">
                        <?php foreach ($suggestions as $s): ?>
                            <div class="list-item-card">
                                <div class="list-item-header">
                                    <div>
                                        <?php if ($error_type === 'flash'): ?>
                                            <div><strong>LED: R=<?php echo (int)$s['red_count']; ?>, B=<?php echo (int)$s['blue_count']; ?>, O=<?php echo (int)$s['orange_count']; ?>, G=<?php echo (int)$s['green_count']; ?>, Y=<?php echo (int)$s['yellow_count']; ?>, W=<?php echo (int)$s['white_count']; ?></strong></div>
                                        <?php elseif ($error_type === 'beep'): ?>
                                            <div><strong>Beep: krótkie=<?php echo (int)$s['short_beeps']; ?>, długie=<?php echo (int)$s['long_beeps']; ?></strong></div>
                                            <?php if (!empty($s['sequence'])): ?>
                                                <div class="small text-muted">Sekwencja: <?php echo htmlspecialchars($s['sequence']); ?></div>
                                            <?php endif; ?>
                                        <?php elseif ($error_type === 'numeric'): ?>
                                            <div><strong>Kod: <?php echo htmlspecialchars($s['error_code']); ?></strong></div>
                                        <?php endif; ?>
                                    </div>
                                    <div style="text-align:right;">
                                        <div class="badge-manufacturer"><?php echo htmlspecialchars($s['manufacturer_name']); ?></div>
                                        <div class="badge-counter"><?php echo (int)$s['search_count']; ?> wysz.</div>
                                    </div>
                                </div>
                                <div class="small text-muted">Opis: <?php echo htmlspecialchars(mb_strimwidth($s['description'], 0, 110, '...')); ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="small-muted">Brak sugestii dla podanych parametrów. Spróbuj nieco zmodyfikować liczbę błysków / sygnałów lub fragment kodu błędu.</p>
                <?php endif; ?>
            </aside>
        </main>
    </div>
</div>
</body>
</html>
