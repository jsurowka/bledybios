// Podstawowa logika interfejsu i walidacji formularzy

// Funkcja pomocnicza do wyświetlania komunikatów błędów przy formularzu
function showFormError(formId, message) {
    var container = document.querySelector('#' + formId + ' .js-form-messages');
    if (!container) return;
    container.innerHTML = '<div class="alert alert-error">' + message + '</div>';
}

// Funkcja pomocnicza do czyszczenia komunikatów
function clearFormMessages(formId) {
    var container = document.querySelector('#' + formId + ' .js-form-messages');
    if (!container) return;
    container.innerHTML = '';
}

// Walidacja formularza błędów świetlnych
function validateFlashForm(event) {
    // łapię formularz od LEDów
    var form = document.getElementById('flash-form');
    if (!form) return;

    clearFormMessages('flash-form');

    var manufacturer = form.querySelector('select[name="manufacturer_id"]');
    var counts = form.querySelectorAll('input[data-color-count]');

    if (!manufacturer.value) {
        event.preventDefault();
        showFormError('flash-form', 'Wybierz producenta płyty głównej / komputera.');
        return;
    }

    var hasAnyValue = false;
    counts.forEach(function (input) {
        if (input.value && parseInt(input.value, 10) > 0) {
            hasAnyValue = true;
        }
    });

    if (!hasAnyValue) {
        event.preventDefault();
        showFormError('flash-form', 'Podaj liczbę błysków dla co najmniej jednego koloru.');
    }
}

// Walidacja formularza błędów dźwiękowych
function validateBeepForm(event) {
    // tutaj sprawdzam czy użytkownik coś wpisał przy beepach
    var form = document.getElementById('beep-form');
    if (!form) return;

    clearFormMessages('beep-form');

    var manufacturer = form.querySelector('select[name="manufacturer_id"]');
    var shortBeeps = form.querySelector('input[name="short_beeps"]');
    var longBeeps = form.querySelector('input[name="long_beeps"]');
    var sequence = form.querySelector('input[name="sequence"]');

    if (!manufacturer.value) {
        event.preventDefault();
        showFormError('beep-form', 'Wybierz producenta BIOS-u.');
        return;
    }

    var hasValue = false;
    if (shortBeeps.value && parseInt(shortBeeps.value, 10) > 0) hasValue = true;
    if (longBeeps.value && parseInt(longBeeps.value, 10) > 0) hasValue = true;
    if (sequence.value && sequence.value.trim().length > 0) hasValue = true;

    if (!hasValue) {
        event.preventDefault();
        showFormError('beep-form', 'Uzupełnij liczbę sygnałów (krótkich / długich) lub wpisz sekwencję.');
    }
}

// Walidacja formularza błędów numerycznych
function validateNumericForm(event) {
    // formularz od kodów typu A2, 0x00000050 itd.
    var form = document.getElementById('numeric-form');
    if (!form) return;

    clearFormMessages('numeric-form');

    var manufacturer = form.querySelector('select[name="manufacturer_id"]');
    var code = form.querySelector('input[name="error_code"]');

    if (!manufacturer.value) {
        event.preventDefault();
        showFormError('numeric-form', 'Wybierz producenta.');
        return;
    }

    if (!code.value || code.value.trim().length < 2) {
        event.preventDefault();
        showFormError('numeric-form', 'Podaj kod błędu (np. 0x00000050, POST 25, A2).');
        return;
    }
}

// Inicjalizacja nasłuchiwaczy po załadowaniu DOM
window.addEventListener('DOMContentLoaded', function () {
    // jak strona się załaduje, to podpinam walidacje pod formularze
    var flashForm = document.getElementById('flash-form');
    if (flashForm) {
        flashForm.addEventListener('submit', validateFlashForm);
    }

    var beepForm = document.getElementById('beep-form');
    if (beepForm) {
        beepForm.addEventListener('submit', validateBeepForm);
    }

    var numericForm = document.getElementById('numeric-form');
    if (numericForm) {
        numericForm.addEventListener('submit', validateNumericForm);
    }
});
