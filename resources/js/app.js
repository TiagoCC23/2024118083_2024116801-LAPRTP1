document.addEventListener('click', (e) => {
    const btn = e.target.closest('#btn-tab-login, #btn-tab-register');
    if (!btn) return;

    e.preventDefault();

    const formLogin = document.getElementById('form-login');
    const formRegister = document.getElementById('form-register');
    const btnLogin = document.getElementById('btn-tab-login');
    const btnRegister = document.getElementById('btn-tab-register');

    if (!formLogin || !formRegister) return;

    if (btn.id === 'btn-tab-login') {
        formLogin.classList.remove('d-none');
        formRegister.classList.add('d-none');
        btnLogin?.classList.add('active');
        btnRegister?.classList.remove('active');
    } else if (btn.id === 'btn-tab-register') {
        formLogin.classList.add('d-none');
        formRegister.classList.remove('d-none');
        btnRegister?.classList.add('active');
        btnLogin?.classList.remove('active');
    }
});

document.addEventListener('DOMContentLoaded', () => {
    const calendarInline = document.getElementById('calendar-inline');
    if (calendarInline && typeof window.flatpickr !== 'undefined') {
        const inputData = document.getElementById('input-data-selecionada');
        const badgeData = document.getElementById('data-selecionada-badge');

        window.flatpickr(calendarInline, {
            inline: true,
            minDate: "today",
            dateFormat: "Y-m-d",
            defaultDate: "today",
            onChange: function (selectedDates, dateStr) {
                if (inputData) inputData.value = dateStr;
                if (badgeData && selectedDates[0]) {
                    badgeData.innerText = selectedDates[0].toLocaleDateString('pt-PT');
                }
            }
        });
    }
});
