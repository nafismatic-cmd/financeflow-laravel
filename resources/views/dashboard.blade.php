
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>FinanceFlow - Dashboard</title>

    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

<div class="app">

    <!-- SIDEBAR -->
    <aside class="sidebar">

        <div class="brand">
            <div class="brand-icon">F</div>
            <h2>FinanceFlow</h2>
        </div>

        <p class="menu-title">MENU</p>

        <a href="#dashboard" class="menu active">
            ◫ Dashboard
        </a>

        <a href="#transactions" class="menu">
            ↗ Transactions
        </a>

        <a href="#statistics" class="menu">
            ▥ Statistics
        </a>

        <p class="menu-title">FINANCIAL</p>

        <a href="#add-transaction" class="menu">
            ＋ Add Transaction
        </a>

        <a href="#savings" class="menu">
            ◎ Savings
        </a>

        <p class="menu-title">TOOLS</p>

        <a href="#settings" class="menu">
            ⚙ Settings
        </a>


        <div class="sidebar-bottom">
            <h3>Manage Your Money</h3>
            <p>Track your income and expenses easily.</p>
            <span>FinanceFlow © 2026</span>
        </div>

    </aside>


    <!-- MAIN CONTENT -->
    <main class="main-content" id="dashboard">

        <!-- HEADER -->
        <header class="header">

            <div>
                <h1>Dashboard</h1>
                <p id="currentDate">Financial Overview</p>
            </div>

            <div class="profile">
                <div class="profile-avatar">U</div>
                <div>
                    <strong id="userName">User</strong>
                    <small>Personal Account</small>
                </div>
            </div>

        </header>


        <!-- STATISTIC CARDS -->
        <section class="stats-grid">

            <div class="stat-card balance-card">
                <div class="stat-top">
                    <span>Total Balance</span>
                    <span class="stat-icon">◈</span>
                </div>

                <h2 id="balance">Rp0</h2>
                <small>Your current balance</small>
            </div>


            <div class="stat-card">
                <div class="stat-top">
                    <span>Total Income</span>
                    <span class="stat-icon green">↗</span>
                </div>

                <h2 id="income">Rp0</h2>
                <small>All income transactions</small>
            </div>


            <div class="stat-card">
                <div class="stat-top">
                    <span>Total Expense</span>
                    <span class="stat-icon red">↘</span>
                </div>

                <h2 id="expense">Rp0</h2>
                <small>All expense transactions</small>
            </div>


            <div class="stat-card">
                <div class="stat-top">
                    <span>Monthly Income</span>
                    <span class="stat-icon green">▤</span>
                </div>

                <h2 id="monthlyIncome">Rp0</h2>
                <small>This month</small>
            </div>

        </section>


        <!-- CHART AREA -->
        <section class="content-grid" id="statistics">

            <div class="panel chart-panel">

                <div class="panel-header">
                    <div>
                        <h2>Financial Statistics</h2>
                        <p>Income vs expense this month</p>
                    </div>
                </div>

                <div class="chart-container">
                    <canvas id="financeChart"></canvas>
                </div>

            </div>


            <div class="panel chart-panel">

                <div class="panel-header">
                    <div>
                        <h2>Financial Overview</h2>
                        <p>Income and expense distribution</p>
                    </div>
                </div>

                <div class="donut-container">
                    <canvas id="donutChart"></canvas>
                </div>

                <div class="chart-legend">
                    <div>
                        <span class="dot green-dot"></span>
                        Income
                        <strong id="incomeLegend">Rp0</strong>
                    </div>

                    <div>
                        <span class="dot red-dot"></span>
                        Expense
                        <strong id="expenseLegend">Rp0</strong>
                    </div>
                </div>

            </div>

        </section>


        <!-- ADD TRANSACTION -->
        <section class="panel transaction-form-panel"
            id="add-transaction">

            <div class="panel-header">
                <div>
                    <h2>Add Transaction</h2>
                    <p>Record your income or expense</p>
                </div>
            </div>

            <form id="transactionForm" class="transaction-form">

                <div class="form-group">
                    <label>Transaction Type</label>

                    <select id="type" required>
                        <option value="income">Income</option>
                        <option value="expense">Expense</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Description</label>

                    <input type="text" id="description"
                        placeholder="Example: Lunch" required>
                </div>

                <div class="form-group">
                    <label>Amount (Rp)</label>

                    <input type="number" id="amount"
                        placeholder="50000" min="1" step="0.01" required>
                </div>

                <div class="form-group">
                    <label>Category</label>

                    <select id="category">
                        <option value="">No category</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Date</label>

                    <input type="date" id="transactionDate" required>
                </div>

                <button type="submit" class="primary-button">
                    + Save Transaction
                </button>

            </form>

            <p id="transactionMessage"></p>

        </section>


        <!-- TRANSACTION HISTORY -->
        <section class="panel history-panel" id="transactions">

            <div class="panel-header">
                <div>
                    <h2>Recent Transactions</h2>
                    <p>Your latest financial activities</p>
                </div>

                <button class="secondary-button" onclick="loadTransactions()">
                    Refresh
                </button>
            </div>

            <div class="table-wrapper">

                <table>
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th>Category</th>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody id="transactionList">
                        <tr>
                            <td colspan="6">Loading transactions...</td>
                        </tr>
                    </tbody>

                </table>

            </div>

        </section>


        <!-- SETTINGS -->
        <section class="panel settings-panel" id="settings">

            <div class="panel-header">
                <div>
                    <h2>Financial Settings</h2>
                    <p>Manage your initial balance</p>
                </div>
            </div>

            <form id="settingsForm" class="settings-form">

                <div class="form-group">
                    <label>Initial Balance (Rp)</label>

                    <input type="number" id="initialBalance"
                        min="0" step="0.01" required>
                </div>

                <button type="submit" class="primary-button">
                    Update Balance
                </button>

            </form>

            <p id="settingsMessage"></p>

        </section>


        <footer class="footer">
            FinanceFlow © 2026 - Personal Finance Management
        </footer>

    </main>

</div>


<script src="{{ asset('js/dashboard.js') }}"></script>

</body>
</html>
