// ============================================================
// Helper per animazioni (sostituti di slideDown/Up/Toggle, fadeIn/Out)
// ============================================================

function slideDown(el) {
    el.style.removeProperty('display');
    if (getComputedStyle(el).display === 'none') {
        el.style.display = 'block';
    }
}

function slideUp(el) {
    el.style.display = 'none';
}

function slideToggle(el) {
    if (getComputedStyle(el).display === 'none') {
        slideDown(el);
    } else {
        slideUp(el);
    }
}

function fadeIn(el) {
    el.style.display = '';
    el.style.opacity = '1';
}

function fadeOut(el) {
    el.style.display = 'none';
    el.style.opacity = '';
}

// ============================================================
// Funzioni ex-plugin jQuery ($.fn.choice, choiceZero, choiceUp, valPosition)
// Ora accettano l'elemento come primo argomento
// ============================================================

function choice(el, i, value = undefined) {
    const cat = el.dataset.cat;
    const input = el.querySelector('input[name="' + cat + '[' + i + ']"]');
    if (value !== undefined && input) {
        input.value = value;
    }
    const val = input ? input.value : '';
    if (i == 0 && val == 0) {
        return 'U';
    }
    return val;
}

function choiceZero(el, val) {
    choice(el, 1, val);
    choice(el, 2, '0');
    choice(el, 3, '0');
}

function choiceUp(el, val, target = 0) {
    if (choice(el, 1) == 'N' || choice(el, 1) == 'U') {
        choice(el, 1, 0);
    }

    if (target == 0) {
        target = valPosition(el, val) - 1;
        if (target == 0) {
            choice(el, 1, choice(el, 2));
            choice(el, 2, choice(el, 3));
            choice(el, 3, 0);
            return;
        }
    }

    console.log(choice(el, 2));
    if (target <= 0) {
        target = 3;
    }
    if (target == 3 && choice(el, 2) == 0) {
        target = 2;
    }
    if (target == 2 && choice(el, 1) == 0) {
        target = 1;
    }

    console.log(target);
    const previous = choice(el, target);
    choice(el, target, val);
    if (target < 3 && previous > 0) {
        choice(el, target + 1, previous);
    }
}

function valPosition(el, val) {
    if (choice(el, 1) == val) { return 1; }
    if (choice(el, 2) == val) { return 2; }
    if (choice(el, 3) == val) { return 3; }
    return 0;
}

// ============================================================
// Funzioni globali
// ============================================================

function alboSearch(text) {
    if (!text) {
        document.querySelectorAll('.albo-anno').forEach(el => slideDown(el));
        document.querySelectorAll('[data-desc]').forEach(el => slideDown(el));
        document.getElementById('filterFound').innerHTML = '';
    } else {
        text = text.toLowerCase();
        document.querySelectorAll('[data-desc]').forEach(el => {
            if (!el.dataset.desc.toLowerCase().includes(text)) {
                slideUp(el);
            } else {
                slideDown(el);
            }
        });
        document.querySelectorAll('.albo-anno').forEach(el => {
            if (el.querySelector('[data-desc]') &&
                [...el.querySelectorAll('[data-desc]')].some(d => d.dataset.desc.toLowerCase().includes(text))) {
                slideDown(el);
            } else {
                slideUp(el);
            }
        });
        const found = document.querySelectorAll('[data-desc]').length
            ? [...document.querySelectorAll('[data-desc]')].filter(d => d.dataset.desc.toLowerCase().includes(text)).length
            : 0;
        let foundText = '';
        if (found == 1) { foundText = found + ' premio trovato'; }
        else if (found > 1) { foundText = found + ' premi trovati'; }
        document.getElementById('filterFound').innerHTML = foundText;
    }
}

function openAggiungi() {
    const aggiungi = document.getElementById('aggiungi');
    if (aggiungi) {
        if (window.location.hash === '#aggiungi') {
            slideDown(aggiungi);
            slideUp(document.getElementById('aggiungi-no'));
        } else {
            slideUp(aggiungi);
            slideDown(document.getElementById('aggiungi-no'));
        }
    }
}

function stickyIdentity() {
    const confirmEl = document.getElementById('confirm');
    document.querySelectorAll('.row-identita').forEach(el => {
        if (confirmEl && confirmEl.checked) {
            el.classList.remove('sticky-top');
        } else {
            el.classList.add('sticky-top');
        }
    });
}

function scroll_to(id) {
    const el = document.getElementById(id);
    if (el) {
        el.scrollIntoView({ behavior: 'smooth' });
    }
}

function countdown(endDate) {
    let days, hours, minutes, seconds;

    endDate = new Date(endDate).getTime();

    if (isNaN(endDate)) {
        return;
    }

    setInterval(calculate, 1000);

    function calculate() {
        const startDate = new Date().getTime();
        let timeRemaining = parseInt((endDate - startDate) / 1000);

        if (timeRemaining >= 0) {
            days = parseInt(timeRemaining / 86400);
            timeRemaining = timeRemaining % 86400;

            hours = parseInt(timeRemaining / 3600);
            timeRemaining = timeRemaining % 3600;

            minutes = parseInt(timeRemaining / 60);
            timeRemaining = timeRemaining % 60;

            seconds = parseInt(timeRemaining);

            let string = ''; let plurale = false; let plurale_set = false;

            if (parseInt(days, 10) > 1) {
                string += parseInt(days, 10) + ' giorni, '; plurale = true; plurale_set = true;
            } else if (parseInt(days, 10) == 1) {
                string += 'un giorno, '; plurale = false; plurale_set = true;
            }

            if (parseInt(hours, 10) > 1) {
                string += parseInt(hours, 10) + ' ore, ';
                if (!plurale_set) { plurale = true; plurale_set = true; }
            } else if (parseInt(hours, 10) == 1) {
                string += 'un\'ora, ';
                if (!plurale_set) { plurale = false; plurale_set = true; }
            }

            if (parseInt(minutes, 10) > 1) {
                string += parseInt(minutes, 10) + ' minuti, ';
                if (!plurale_set) { plurale = true; plurale_set = true; }
            } else if (parseInt(minutes, 10) == 1) {
                string += 'un minuto, ';
                if (!plurale_set) { plurale = false; plurale_set = true; }
            }

            if (parseInt(seconds, 10) > 1) {
                string += parseInt(seconds, 10) + ' secondi, ';
                if (!plurale_set) { plurale = true; plurale_set = true; }
            } else if (parseInt(seconds, 10) == 1) {
                string += 'un secondo, ';
                if (!plurale_set) { plurale = false; plurale_set = true; }
            }

            string = string.replace(/,,+/, ',');
            string = string.replace(/, $/, '');
            string = string.replace(/,([^,]+)$/, ' e$1');
            string = (plurale ? 'mancano ' : 'manca ') + string;

            const countdownEls = document.querySelectorAll('[data-countdown]');
            if (string === 'manca ' || string === 'mancano ') {
                countdownEls.forEach(el => el.innerHTML = ': il termine è scaduto');
                window.location.reload();
                return;
            }
            countdownEls.forEach(el => el.innerHTML = string);
        } else {
            document.querySelectorAll('[data-countdown]').forEach(el => el.innerHTML = ': il termine è scaduto');
            window.location.reload();
        }
    }
}

// ============================================================
// DOMContentLoaded – blocco 1: request-token, vote form, countdown
// ============================================================

document.addEventListener('DOMContentLoaded', function () {

    // Invio email di invito
    const requestTokenForm = document.getElementById('request-token');
    if (requestTokenForm) {
        requestTokenForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const emailInput = document.getElementById('request-token-email');
            const feedback = document.getElementById('request-token-feedback');
            emailInput.classList.remove('is-valid', 'is-invalid');
            feedback.textContent = '';
            const email = emailInput.value;
            if (!email || !email.match(/.+@.+/)) {
                emailInput.classList.add('is-invalid');
                feedback.textContent = 'Non hai inserito un indirizzo email valido';
            } else {
                fetch('/api/manda-invito?' + new URLSearchParams({ email }))
                    .then(r => r.json())
                    .then(function (result) {
                        console.log(result);
                        if (result.status === 'error') {
                            let error_message = 'Si è verificato un errore';
                            switch (result.error) {
                                case 'missing-email': error_message = 'Email non indicata'; break;
                                case 'email-not-found': error_message = 'Email non trovata: forse non sei un elettore registrato.'; break;
                                case 'user-invalid': error_message = 'Il tuo diritto di voto è al momento sospeso.'; break;
                            }
                            emailInput.classList.add('is-invalid');
                            feedback.textContent = error_message;
                        } else {
                            emailInput.classList.add('is-valid');
                        }
                    });
            }
        });
    }

    // Rule detail toggle
    document.querySelectorAll('.rule-detail').forEach(el => {
        el.addEventListener('click', function (e) {
            e.preventDefault();
            const detail = this.closest('div').querySelector('.detail');
            if (detail) { slideToggle(detail); }
        });
    });

    // Btn save
    document.querySelectorAll('.btn-save').forEach(el => {
        el.addEventListener('click', function (e) {
            e.preventDefault();
            const form = document.getElementById('vote-form');
            const url = form.getAttribute('action');
            const confirmEl = document.getElementById('confirm');
            if (!confirmEl || !confirmEl.checked) {
                const dangerAlert = document.querySelector('.alert-danger.save-alert');
                dangerAlert.innerHTML = 'Prima di salvare devi <a href="#" onclick="scroll_to(\'confirm\')">confermare la tua identità</a>';
                fadeIn(dangerAlert);
                setTimeout(() => document.querySelectorAll('.save-alert').forEach(a => fadeOut(a)), 5000);
            } else {
                fetch(url, {
                    method: 'POST',
                    body: new URLSearchParams(new FormData(form)),
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                })
                    .then(r => r.json())
                    .then(function (result) {
                        if (result.status === 'success') {
                            const successAlert = document.querySelector('.alert-success.save-alert');
                            fadeIn(successAlert);
                            setTimeout(() => document.querySelectorAll('.save-alert').forEach(a => fadeOut(a)), 5000);
                        } else {
                            const dangerAlert = document.querySelector('.alert-danger.save-alert');
                            dangerAlert.innerHTML = result.error;
                            fadeIn(dangerAlert);
                            setTimeout(() => document.querySelectorAll('.save-alert').forEach(a => fadeOut(a)), 5000);
                        }
                    });
            }
        });
    });

    // Btn send confirm
    document.querySelectorAll('.btn-send-confirm').forEach(el => {
        el.addEventListener('click', function () {
            const confirmEl = document.getElementById('confirm');
            if (!confirmEl || !confirmEl.checked) {
                const dangerAlert = document.querySelector('.alert-danger.save-alert');
                dangerAlert.innerHTML = 'Prima di salvare devi <a href="#" onclick="scroll_to(\'confirm\')">confermare la tua identità</a>';
                fadeIn(dangerAlert);
            } else {
                if (confirm("Una volta inviato il voto non potrà più essere modificato per nessun motivo. Confermi l'invio?")) {
                    document.querySelector('#vote-form input[name=action]').value = 'send';
                    document.getElementById('vote-form').submit();
                }
            }
        });
    });

    // Btn send (apre modal Bootstrap)
    document.querySelectorAll('.btn-send').forEach(el => {
        el.addEventListener('click', function () {
            const confirmEl = document.getElementById('confirm');
            if (!confirmEl || !confirmEl.checked) {
                const dangerAlert = document.querySelector('.alert-danger.save-alert');
                dangerAlert.innerHTML = 'Prima di salvare devi <a href="#" onclick="scroll_to(\'confirm\')">confermare la tua identità</a>';
                fadeIn(dangerAlert);
            } else {
                const modalEl = document.getElementById('confirmSend');
                if (modalEl) {
                    bootstrap.Modal.getOrCreateInstance(modalEl).show();
                }
            }
        });
    });

    // Checkbox conferma identità
    const confirmEl = document.getElementById('confirm');
    if (confirmEl) {
        confirmEl.addEventListener('click', function () {
            document.querySelectorAll('.save-alert').forEach(el => fadeOut(el));
            stickyIdentity();
        });
    }

    stickyIdentity();

    // Countdown
    document.querySelectorAll('[data-countdown]').forEach(el => {
        countdown(el.dataset.countdown);
    });
});

// ============================================================
// DOMContentLoaded – blocco 2: finalisti, candidatura form
// ============================================================

document.addEventListener('DOMContentLoaded', function () {

    // Pulsanti finalisti
    document.querySelectorAll('.finalisti button').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const parent = this.closest('.finalisti');
            const val = this.dataset.value;
            const pos = this.dataset.position;

            console.log(parent);
            console.log(val);
            console.log(pos);

            if (val === 'U' || val === 'N') {
                choiceZero(parent, val);
                parent.querySelectorAll('button.activable').forEach(b => b.classList.remove('active'));
                const targetBtn = parent.querySelector('button[data-value="' + val + '"].activable');
                if (targetBtn) { targetBtn.classList.add('active'); }
            } else {
                parent.querySelectorAll('button.activable').forEach(b => b.classList.remove('active'));
                choiceUp(parent, val);
                for (let i = 1; i <= 3; i++) {
                    const v = choice(parent, i);
                    const activeBtn = parent.querySelector('button[data-value="' + v + '"][data-position="' + i + '"].activable');
                    if (activeBtn) { activeBtn.classList.add('active'); }
                }
            }
        });
    });

    openAggiungi();

    window.addEventListener('hashchange', function () {
        openAggiungi();
    });

    // Form candidatura
    const candidaturaForm = document.getElementById('candidatura-form');
    if (candidaturaForm) {
        candidaturaForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            let complete = true;
            for (const [, value] of formData.entries()) {
                if (!value) { complete = false; }
            }
            if (!complete) {
                const dangerAlert = document.querySelector('.alert-danger.candidatura-alert');
                dangerAlert.innerHTML = 'Non hai compilato tutti i campi';
                fadeIn(dangerAlert);
                setTimeout(() => document.querySelectorAll('.candidatura-alert').forEach(a => fadeOut(a)), 5000);
                return;
            }
            fetch(this.getAttribute('action'), {
                method: 'POST',
                body: new URLSearchParams(formData),
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            })
                .then(r => r.json())
                .then(function (result) {
                    if (result.status === 'success') {
                        const successAlert = document.querySelector('.alert-success.candidatura-alert');
                        fadeIn(successAlert);
                        setTimeout(() => document.querySelectorAll('.save-alert').forEach(a => fadeOut(a)), 5000);
                        document.getElementById('candidatura-form').reset();
                    } else {
                        const dangerAlert = document.querySelector('.alert-danger.candidatura-alert');
                        dangerAlert.innerHTML = result.error;
                        fadeIn(dangerAlert);
                        setTimeout(() => document.querySelectorAll('.candidatura-alert').forEach(a => fadeOut(a)), 5000);
                    }
                });
        });
    }

    // Clear segnalazione
    document.querySelectorAll('.clear-segnalazione').forEach(el => {
        el.addEventListener('click', function (e) {
            e.preventDefault();
            const clearId = this.dataset.clear;
            document.querySelectorAll('[data-riga-segnalazione="' + clearId + '"]').forEach(input => input.value = '');
        });
    });

    // Insert candidature
    document.querySelectorAll('.insert-candidature a').forEach(el => {
        el.addEventListener('click', function (e) {
            e.preventDefault();
            const categoria = this.dataset.categoria;
            const campi = this.dataset.candidatura ? JSON.parse(this.dataset.candidatura) : {};

            let disponibile = 0;
            for (let i = 1; i <= 3; i++) {
                let vuoto = true;
                for (const campo in campi) {
                    if (document.querySelector('[name="' + categoria + '-' + i + '-' + campo + '"]').value) {
                        vuoto = false;
                    }
                }
                if (vuoto) {
                    disponibile = i;
                    break;
                }
            }

            if (!disponibile) {
                alert('Non ci sono slot disponibili; devi svuotarne uno se vuoi inserire questa candidatura');
            } else {
                for (const campo in campi) {
                    document.querySelector('[name="' + categoria + '-' + disponibile + '-' + campo + '"]').value = campi[campo];
                }
            }
        });
    });
});

// ============================================================
// DOMContentLoaded – blocco 3: partecipazione convention
// ============================================================

document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('[data-partecipazione]').forEach(el => {
        el.addEventListener('click', function () {
            const partecipazioneId = this.dataset.partecipazione;
            let url = '/api/convention/' + partecipazioneId;
            if (this.dataset.valore === 'no') { url += '/no'; }

            fetch(url)
                .then(r => r.json())
                .then(function (result) {
                    if (result.status === 'success') {
                        console.log(result.convention_id);
                        console.log(result.partecipato);
                        const partecipazione = result.partecipato;
                        const nonpartecipazione = result.partecipato === 'yes' ? 'no' : 'yes';
                        const activeClass = 'btn-' + (partecipazione === 'yes' ? 'success' : 'secondary');
                        const inactiveClass = 'btn-' + (partecipazione === 'yes' ? 'secondary' : 'success');

                        document.querySelectorAll(
                            '[data-partecipazione="' + result.convention_id + '"][data-valore="' + partecipazione + '"]'
                        ).forEach(b => {
                            b.classList.remove('btn-light');
                            b.classList.add(activeClass);
                        });

                        document.querySelectorAll(
                            '[data-partecipazione="' + result.convention_id + '"][data-valore="' + nonpartecipazione + '"]'
                        ).forEach(b => {
                            b.classList.remove(inactiveClass);
                            b.classList.add('btn-light');
                        });
                    }
                });
        });
    });

});
