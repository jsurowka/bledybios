-- Tworzenie bazy danych dla aplikacji BIOS Errors
CREATE DATABASE IF NOT EXISTS bios_errors CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE bios_errors;

-- Tabela producentów
CREATE TABLE IF NOT EXISTS manufacturers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela błędów świetlnych (flash/LED)
CREATE TABLE IF NOT EXISTS flash_errors (
  id INT AUTO_INCREMENT PRIMARY KEY,
  manufacturer_id INT NOT NULL,
  blue_count INT DEFAULT 0,
  orange_count INT DEFAULT 0,
  red_count INT DEFAULT 0,
  green_count INT DEFAULT 0,
  yellow_count INT DEFAULT 0,
  white_count INT DEFAULT 0,
  description TEXT NOT NULL,
  solution TEXT NOT NULL,
  search_count INT NOT NULL DEFAULT 0,
  CONSTRAINT fk_flash_manufacturer FOREIGN KEY (manufacturer_id) REFERENCES manufacturers(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela błędów dźwiękowych (beep)
CREATE TABLE IF NOT EXISTS beep_errors (
  id INT AUTO_INCREMENT PRIMARY KEY,
  manufacturer_id INT NOT NULL,
  short_beeps INT DEFAULT 0,
  long_beeps INT DEFAULT 0,
  sequence VARCHAR(255) DEFAULT NULL,
  description TEXT NOT NULL,
  solution TEXT NOT NULL,
  search_count INT NOT NULL DEFAULT 0,
  CONSTRAINT fk_beep_manufacturer FOREIGN KEY (manufacturer_id) REFERENCES manufacturers(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela błędów numerycznych (kody POST / numeryczne)
CREATE TABLE IF NOT EXISTS numeric_errors (
  id INT AUTO_INCREMENT PRIMARY KEY,
  manufacturer_id INT NOT NULL,
  error_code VARCHAR(50) NOT NULL,
  description TEXT NOT NULL,
  solution TEXT NOT NULL,
  search_count INT NOT NULL DEFAULT 0,
  CONSTRAINT fk_numeric_manufacturer FOREIGN KEY (manufacturer_id) REFERENCES manufacturers(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dane producentów
INSERT INTO manufacturers (name) VALUES
('AMI'),
('Award'),
('Phoenix'),
('Dell'),
('HP'),
('Lenovo'),
('ASUS'),
('MSI'),
('Gigabyte');

-- Przykładowe błędy świetlne (ok. 20 wpisów)
INSERT INTO flash_errors (manufacturer_id, blue_count, orange_count, red_count, green_count, yellow_count, white_count, description, solution) VALUES
(1, 0, 0, 5, 0, 0, 0, 'Problem z procesorem (AMI, 5 czerwonych błysków)', 'Sprawdź montaż procesora, wyczyść styki, zresetuj BIOS, jeśli to możliwe przetestuj inny procesor.'),
(4, 0, 2, 0, 0, 0, 3, 'Błąd pamięci RAM (Dell, 2 pomarańczowe, 3 białe)', 'Przetestuj moduły RAM pojedynczo, wyczyść styki, sprawdź zgodność pamięci z płytą główną.'),
(5, 0, 0, 3, 0, 0, 2, 'Awaria karty graficznej (HP, 3 czerwone, 2 białe)', 'Sprawdź poprawność osadzenia karty, podłącz dodatkowe zasilanie, przetestuj inną kartę jeśli to możliwe.'),
(6, 0, 0, 2, 0, 0, 1, 'Lenovo: 2 czerwone, 1 biały – błąd pamięci systemowej', 'Uruchom diagnostykę pamięci, wymień uszkodzony moduł RAM.'),
(7, 0, 0, 4, 0, 0, 0, 'ASUS: 4 czerwone – przegrzewanie procesora', 'Sprawdź układ chłodzenia, wyczyść radiator, nałóż nową pastę termoprzewodzącą.'),
(8, 0, 0, 2, 0, 0, 2, 'MSI: 2 czerwone, 2 białe – błąd GPU lub PCI-E', 'Przeinstaluj kartę w slocie, sprawdź inne gniazdo PCI-E, zaktualizuj BIOS/UEFI.'),
(9, 0, 1, 0, 0, 0, 3, 'Gigabyte: 1 pomarańczowa, 3 białe – błąd dysku systemowego', 'Sprawdź podłączenie dysku, wykonaj test SMART, rozważ wymianę dysku.'),
(1, 0, 0, 2, 0, 0, 0, 'AMI: 2 czerwone – błąd pamięci podręcznej CPU', 'Wyłącz w BIOS funkcje cache, zaktualizuj BIOS, przetestuj inny CPU.'),
(2, 0, 0, 3, 0, 0, 0, 'Award: 3 czerwone – błąd kontrolera klawiatury', 'Sprawdź podłączenie klawiatury, wyłącz urządzenia USB, zresetuj ustawienia BIOS.'),
(3, 0, 0, 1, 0, 0, 2, 'Phoenix: 1 czerwony, 2 białe – błąd kontrolera dysku', 'Zmień tryb SATA w BIOS, sprawdź kable SATA, zaktualizuj firmware dysku.'),
(4, 1, 0, 0, 0, 0, 1, 'Dell: 1 niebieski, 1 biały – błąd zasilacza', 'Sprawdź przewody zasilające, podmień zasilacz na sprawny.'),
(5, 0, 0, 4, 0, 0, 1, 'HP: 4 czerwone, 1 biały – błąd płyty głównej', 'Sprawdź widoczne uszkodzenia na PCB, rozważ wymianę płyty głównej.'),
(6, 0, 2, 0, 0, 0, 2, 'Lenovo: 2 pomarańczowe, 2 białe – błąd BIOS', 'Przeprowadź procedurę odzyskiwania BIOS, zaktualizuj do najnowszej wersji.'),
(7, 0, 0, 2, 0, 1, 0, 'ASUS: 2 czerwone, 1 żółty – błąd pamięci wideo', 'Przeinstaluj sterowniki GPU, przetestuj inną kartę, sprawdź monitor na innym komputerze.'),
(8, 0, 0, 1, 0, 0, 1, 'MSI: 1 czerwony, 1 biały – błąd bootowania systemu', 'Sprawdź kolejność bootowania w BIOS, podłącz prawidłowy dysk systemowy.'),
(9, 0, 0, 2, 0, 0, 2, 'Gigabyte: 2 czerwone, 2 białe – błąd pamięci RAM', 'Przetestuj moduły RAM narzędziem MemTest, wymień wadliwy moduł.'),
(1, 0, 0, 3, 0, 1, 0, 'AMI: 3 czerwone, 1 żółty – błąd kontrolera PCI', 'Wyjmij wszystkie karty rozszerzeń, podłączaj pojedynczo i obserwuj zachowanie.'),
(2, 0, 1, 2, 0, 0, 0, 'Award: 1 pomarańczowy, 2 czerwone – błąd pamięci CMOS', 'Wymień baterię CMOS, zresetuj ustawienia BIOS do domyślnych.'),
(3, 0, 0, 2, 0, 0, 1, 'Phoenix: 2 czerwone, 1 biały – błąd portu USB', 'Odłącz wszystkie urządzenia USB, aktualizuj BIOS i sterowniki chipsetu.'),
(4, 0, 0, 0, 0, 2, 2, 'Dell: 2 żółte, 2 białe – ogólny błąd sprzętowy', 'Uruchom pełną diagnostykę Dell, zanotuj kody błędów i postępuj zgodnie z zaleceniami.'),
(5, 0, 0, 1, 0, 1, 1, 'HP: 1 czerwony, 1 żółty, 1 biały – błąd zasilania CPU', 'Sprawdź wtyczkę zasilania CPU (EPS), podmień zasilacz, wyjmij dodatkowe karty rozszerzeń.');

-- Przykładowe błędy dźwiękowe (ok. 20 wpisów)
INSERT INTO beep_errors (manufacturer_id, short_beeps, long_beeps, sequence, description, solution) VALUES
(1, 2, 1, '1 long, 2 short', 'Problem z kartą graficzną (AMI)', 'Sprawdź czy karta graficzna jest poprawnie osadzona, podłącz dodatkowe zasilanie GPU, przetestuj inną kartę.'),
(2, 3, 1, '1 long, 3 short', 'Błąd pamięci (Award)', 'Sprawdź moduły RAM, przetestuj pojedynczo, wyczyść styki gumką techniczną.'),
(3, 3, 0, '3 short, pause, 4 short, pause, 2 short', 'Błąd klawiatury (Phoenix)', 'Sprawdź podłączenie klawiatury, spróbuj innej, wyłącz urządzenia USB powodujące konflikt.'),
(1, 1, 0, '1 short', 'Standardowy sygnał POST OK (AMI)', 'Brak błędu – system przeszedł test POST.'),
(1, 3, 0, '3 short', 'Błąd pamięci podstawowej 64 KB (AMI)', 'Sprawdź starsze moduły pamięci lub zgodność pamięci z płytą główną.'),
(1, 6, 0, '6 short', 'Błąd kontrolera klawiatury (AMI)', 'Sprawdź kontroler klawiatury na płycie głównej, spróbuj innej klawiatury.'),
(2, 1, 0, '1 short', 'Błąd odświeżania pamięci (Award)', 'Przetestuj pamięć RAM, zaktualizuj BIOS, sprawdź napięcie RAM.'),
(2, 2, 0, '2 short', 'Ogólny błąd parzystości pamięci (Award)', 'Przetestuj moduły RAM, wymień wadliwe kości.'),
(2, 0, 1, '1 long', 'Błąd pamięci (Award, poważny)', 'Sprawdź wszystkie moduły RAM, sprawdź banki na płycie głównej.'),
(3, 1, 0, '1 short', 'Błąd odświeżania pamięci (Phoenix)', 'Przetestuj RAM, sprawdź ustawienia timingów w BIOS.'),
(3, 2, 0, '2 short', 'Błąd pamięci parzystości (Phoenix)', 'Wymień uszkodzone moduły RAM, sprawdź kompatybilność.'),
(4, 3, 0, '3 short', 'Dell – błąd pamięci RAM', 'Wyjmij i ponownie zainstaluj moduły, uruchom diagnostykę Dell.'),
(4, 4, 0, '4 short', 'Dell – błąd płyty głównej', 'Skontaktuj się z serwisem, sprawdź widoczne uszkodzenia płyty.'),
(5, 3, 1, '1 long, 3 short', 'HP – błąd karty graficznej', 'Sprawdź kartę graficzną, przetestuj na innym komputerze.'),
(5, 5, 0, '5 short', 'HP – błąd procesora', 'Sprawdź osadzenie procesora, stan pinów, temperatury.'),
(6, 3, 0, '3 short', 'Lenovo – błąd RAM', 'Przetestuj pamięć, wymień podejrzane moduły.'),
(6, 5, 0, '5 short', 'Lenovo – błąd płyty systemowej', 'Skontaktuj się z serwisem Lenovo, możliwa wymiana płyty.'),
(7, 1, 2, '2 long, 1 short', 'ASUS – błąd karty graficznej', 'Przeinstaluj sterowniki, sprawdź kartę i zasilanie.'),
(8, 2, 1, '1 long, 2 short', 'MSI – błąd RAM lub GPU', 'Sprawdź pamięć i kartę graficzną, przetestuj konfiguracje minimalne.'),
(9, 3, 1, '1 long, 3 short', 'Gigabyte – błąd RAM', 'Uruchom MemTest, wymień wadliwy moduł RAM.');

-- Przykładowe błędy numeryczne (ok. 20 wpisów)
INSERT INTO numeric_errors (manufacturer_id, error_code, description, solution) VALUES
(1, 'POST Code A2', 'AMI: Błąd inicjalizacji IDE / SATA', 'Sprawdź podłączenie dysków, ustaw tryb SATA w BIOS (AHCI/IDE), zaktualizuj firmware dysku.'),
(4, '2000-0142', 'Dell: Awaria dysku twardego', 'Wykonaj kopię zapasową danych, uruchom diagnostykę dysku, rozważ jego wymianę.'),
(5, '601', 'HP: Błąd baterii RTC / dyskietki', 'Wymień baterię CMOS, sprawdź ustawienia daty i godziny w BIOS.'),
(1, '0x00000050', 'AMI: PAGE_FAULT_IN_NONPAGED_AREA', 'Sprawdź pamięć RAM, zaktualizuj sterowniki, sprawdź integralność plików systemowych.'),
(1, '0x0000007B', 'AMI: INACCESSIBLE_BOOT_DEVICE', 'Sprawdź kontroler dysku, tryb SATA, poprawność wpisów bootloadera.'),
(2, 'POST 25', 'Award: Błąd inicjalizacji pamięci rozszerzonej', 'Sprawdź konfigurację pamięci, usuń podkręcanie, zresetuj BIOS.'),
(2, 'POST 52', 'Award: Błąd detekcji RAM', 'Przetestuj moduły pamięci, użyj innych banków na płycie.'),
(3, 'POST 75', 'Phoenix: Błąd detekcji dysków IDE', 'Sprawdź kable, zworki (jeśli dotyczy), tryb pracy kontrolera.'),
(3, 'POST 94', 'Phoenix: Błąd inicjalizacji wideo', 'Sprawdź kartę graficzną, monitor, przełącz wyjście wideo.'),
(4, '0x0000009F', 'Dell: DRIVER_POWER_STATE_FAILURE', 'Zaktualizuj sterowniki zarządzania energią, usuń wadliwe urządzenia USB.'),
(4, '0x000000F4', 'Dell: CRITICAL_OBJECT_TERMINATION', 'Sprawdź dysk systemowy, wykonaj testy SMART, przeskanuj system na obecność malware.'),
(5, '0x0000001A', 'HP: MEMORY_MANAGEMENT', 'Przetestuj pamięć RAM, wyłącz podkręcanie, sprawdź temperatury.'),
(5, '0x00000024', 'HP: NTFS_FILE_SYSTEM', 'Sprawdź dysk narzędziem CHKDSK, wykonaj kopię zapasową danych.'),
(6, 'ERROR 2100', 'Lenovo: Błąd dysku twardego', 'Sprawdź podłączenie dysku, wykonaj diagnostykę i rozważ wymianę.'),
(6, 'ERROR 1802', 'Lenovo: Nieautoryzowana karta sieciowa', 'Zainstaluj wspieraną kartę lub zmodyfikuj BIOS (jeśli to możliwe).'),
(7, 'Q-Code 55', 'ASUS: Brak zainstalowanej pamięci RAM', 'Sprawdź czy moduły RAM są poprawnie zainstalowane, przetestuj inne sloty.'),
(7, 'Q-Code A2', 'ASUS: Problem z inicjalizacją dysków', 'Sprawdź dyski i kable SATA, ustaw poprawną kolejność bootowania.'),
(8, 'D7', 'MSI: Błąd klawiatury', 'Sprawdź podłączenie klawiatury, przetestuj inną, usuń koncentratory USB.'),
(8, '55', 'MSI: Brak lub błąd pamięci', 'Przetestuj moduły RAM pojedynczo, popraw ich osadzenie.'),
(9, 'AE', 'Gigabyte: Boot do systemu operacyjnego', 'Status informacyjny – sprawdź konfigurację bootowania, jeśli występują problemy z uruchomieniem systemu.'),
(9, 'D5', 'Gigabyte: Brak dysku rozruchowego', 'Podłącz dysk z systemem, ustaw poprawne urządzenie startowe w BIOS.');
