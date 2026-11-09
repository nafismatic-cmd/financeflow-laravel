
const API = `${window.location.origin}/api`;

// Token khusus akun demo
const token = '1|5IfKbhShISqnxFzhyh67Z7cRiamBM5PRH60fdM0Jb131f723';

let financeChart = null;
let donutChart = null;


// ===================================
// AUTHENTICATION
// ===================================

async function apiRequest(endpoint, options = {}) {

    const response = await fetch(`${API}${endpoint}`, {
        ...options,

        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`,
            ...options.headers
        }
    });

    const result = await response.json();

    if (response.status === 401) {
        logout();
        throw new Error('Session berakhir. Silakan login kembali.');
    }

    if (!response.ok) {
        throw new Error(
            result.message || 'Terjadi kesalahan pada server'
        );
    }

    return result;
}

function logout() {
    const currentToken = localStorage.getItem('token');

    if (currentToken) {
        fetch(`${API}/logout`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Authorization': `Bearer ${currentToken}`
            }
        }).catch(() => {});
    }

    localStorage.removeItem('token');
    window.location.href = '/';
}


// ===================================
// FORMAT RUPIAH
// ===================================

function rupiah(number) {

    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0
    }).format(Number(number) || 0);

}


// Mencegah teks transaksi menjadi HTML aktif
function escapeHTML(value) {
    return String(value ?? '').replace(/[&<>"']/g, char => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    })[char]);
}


// ===================================
// LOAD DASHBOARD
// ===================================

async function loadDashboard() {

    try {

        const data = await apiRequest('/dashboard');

        document.getElementById('balance').textContent =
            rupiah(data.balance);

        document.getElementById('income').textContent =
            rupiah(data.total_income);

        document.getElementById('expense').textContent =
            rupiah(data.total_expense);

        document.getElementById('monthlyIncome').textContent =
            rupiah(data.monthly_income);

        document.getElementById('incomeLegend').textContent =
            rupiah(data.total_income);

        document.getElementById('expenseLegend').textContent =
            rupiah(data.total_expense);

        renderCharts(data);

        loadTransactions();

    } catch (error) {
        console.error(error);
        alert(error.message);
    }

}


// ===================================
// CHART.JS
// ===================================

function renderCharts(data) {

    const income = Number(data.monthly_income) || 0;
    const expense = Number(data.monthly_expense) || 0;

    // BAR CHART

    const barCanvas = document.getElementById('financeChart');

    if (financeChart) {
        financeChart.destroy();
    }

    financeChart = new Chart(barCanvas, {

        type: 'bar',

        data: {
            labels: ['Income', 'Expense'],

            datasets: [{
                label: 'Amount (Rp)',

                data: [income, expense],

                backgroundColor: [
                    '#009c58',
                    '#e34e4e'
                ],

                borderRadius: 10,

                barThickness: 55
            }]
        },

        options: {
            responsive: true,
            maintainAspectRatio: false,

            plugins: {
                legend: {
                    display: false
                }
            },

            scales: {
                y: {
                    beginAtZero: true,

                    ticks: {
                        callback: value =>
                            'Rp' + new Intl.NumberFormat('id-ID')
                                .format(value)
                    }
                }
            }
        }

    });


    // DONUT CHART

    const donutCanvas = document.getElementById('donutChart');

    if (donutChart) {
        donutChart.destroy();
    }

    donutChart = new Chart(donutCanvas, {

        type: 'doughnut',

        data: {
            labels: ['Income', 'Expense'],

            datasets: [{
                data: [Number(data.total_income), Number(data.total_expense)],

                backgroundColor: [
                    '#009c58',
                    '#e34e4e'
                ],

                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },

        options: {
            responsive: true,
            maintainAspectRatio: false,

            cutout: '65%',

            plugins: {
                legend: {
                    display: false
                }
            }
        }

    });

}


// ===================================
// LOAD TRANSACTIONS
// ===================================

async function loadTransactions() {

    const table = document.getElementById('transactionList');

    try {

        const result = await apiRequest('/transactions');

        const transactions = result.data || [];

        if (transactions.length === 0) {

            table.innerHTML = `
                <tr>
                    <td colspan="6">
                        Belum ada transaksi.
                    </td>
                </tr>
            `;

            return;
        }

        table.innerHTML = transactions.map(item => {

            const isIncome = item.type === 'income';

            const date = new Date(
                item.transaction_date + 'T00:00:00'
            );

            return `
                <tr>

                    <td>
                        <strong>
                            ${escapeHTML(item.description)}
                        </strong>
                    </td>

                    <td>
                        ${escapeHTML(item.category?.name || '-')}
                    </td>

                    <td>
                        ${date.toLocaleDateString('id-ID')}
                    </td>

                    <td class="${isIncome ? 'income-text' : 'expense-text'}">
                        ${isIncome ? 'Income' : 'Expense'}
                    </td>

                    <td class="${isIncome ? 'income-text' : 'expense-text'}">
                        ${isIncome ? '+' : '-'}${rupiah(item.amount)}
                    </td>

                    <td>
                        <button
                            class="delete-button"
                            onclick="deleteTransaction(${Number(item.id)})">
                            Delete
                        </button>
                    </td>

                </tr>
            `;

        }).join('');

    } catch (error) {

        console.error(error);

        table.innerHTML = `
            <tr>
                <td colspan="6">Gagal mengambil transaksi.</td>
            </tr>
        `;

    }

}


// ===================================
// ADD TRANSACTION
// ===================================

document.getElementById('transactionForm')
.addEventListener('submit', async function(e) {

    e.preventDefault();

    const message = document.getElementById('transactionMessage');

    const categoryValue = document.getElementById('category').value;

    const data = {

        type: document.getElementById('type').value,

        description: document.getElementById('description').value,

        amount: Number(document.getElementById('amount').value),

        category_id: categoryValue || null,

        transaction_date: document.getElementById('transactionDate').value

    };

    try {

        await apiRequest('/transactions', {

            method: 'POST',

            body: JSON.stringify(data)

        });

        message.textContent = 'Transaksi berhasil ditambahkan!';

        message.style.color = 'green';

        this.reset();

        document.getElementById('transactionDate').value =
            new Date().toISOString().split('T')[0];

        await loadDashboard();

    } catch (error) {

        message.textContent = error.message;
        message.style.color = 'red';

    }

});


// ===================================
// DELETE TRANSACTION
// ===================================

async function deleteTransaction(id) {

    if (!confirm('Yakin ingin menghapus transaksi ini?')) {
        return;
    }

    try {

        await apiRequest(`/transactions/${id}`, {
            method: 'DELETE'
        });

        alert('Transaksi berhasil dihapus!');

        await loadDashboard();

    } catch (error) {

        alert(error.message);

    }

}


// ===================================
// LOAD CATEGORIES
// ===================================

async function loadCategories() {

    try {

        const categories = await apiRequest('/categories');

        const select = document.getElementById('category');

        select.innerHTML = '<option value="">No category</option>';

        categories.forEach(category => {

            const option = document.createElement('option');

            option.value = category.id;

            option.textContent =
                `${category.name} (${category.type})`;

            select.appendChild(option);

        });

    } catch (error) {

        console.error('Gagal mengambil kategori:', error);

    }

}


// ===================================
// SETTINGS
// ===================================

async function loadSettings() {

    try {

        const data = await apiRequest('/settings');

        document.getElementById('initialBalance').value =
            data?.initial_balance ?? 0;

    } catch (error) {

        console.error(error);

    }

}


document.getElementById('settingsForm')
.addEventListener('submit', async function(e) {

    e.preventDefault();

    const message = document.getElementById('settingsMessage');

    const initialBalance = Number(
        document.getElementById('initialBalance').value
    );

    try {

        await apiRequest('/settings', {

            method: 'PUT',

            body: JSON.stringify({
                initial_balance: initialBalance
            })

        });

        message.textContent = 'Saldo awal berhasil diperbarui!';

        message.style.color = 'green';

        await loadDashboard();

    } catch (error) {

        message.textContent = error.message;
        message.style.color = 'red';

    }

});


// ===================================
// INITIALIZE
// ===================================

document.getElementById('currentDate').textContent =
    new Date().toLocaleDateString('id-ID', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    });

document.getElementById('transactionDate').value =
    new Date().toISOString().split('T')[0];

async function initializeDashboard() {

    await loadCategories();

    await loadSettings();

    await loadDashboard();

}

initializeDashboard();
