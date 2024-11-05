<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cēsu TIC Datu Pārskatu Rīks</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f9ff;
            color: #333;
        }
        header {
            background: #007BFF;
            color: white;
            padding: 20px;
            text-align: center;
            border-bottom: 5px solid #0056b3;
        }
        main {
            padding: 20px;
        }
        .container {
            max-width: 900px;
            margin: auto;
            padding: 20px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        input[type="text"], input[type="password"], input[type="file"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #007BFF;
            border-radius: 5px;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #0056b3;
        }
        .hidden {
            display: none;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
            transition: background-color 0.3s;
        }
        th {
            background-color: #007BFF;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #d1e7ff;
        }
        canvas {
            max-width: 100%;
            margin-top: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background: white;
        }
        footer {
            text-align: center;
            padding: 10px;
            margin-top: 20px;
            font-size: 0.9em;
            color: #777;
        }
    </style>
</head>
<body>
    <header>
        <h1>Cēsu Tūrisma Informācijas Centrs</h1>
    </header>
    <main>
        <div class="container" id="authContainer">
            <h2 id="authTitle">Pierakstīties</h2>
            <input type="text" id="username" placeholder="Lietotājvārds" required />
            <input type="password" id="password" placeholder="Parole" required />
            <button id="authButton" onclick="login()">Pierakstīties</button>
            <p id="toggleAuth">Nav konta? <a href="#" onclick="showRegister()">Reģistrēties</a></p>
        </div>

        <div class="container hidden" id="registerContainer">
            <h2>Reģistrēties</h2>
            <input type="text" id="newUsername" placeholder="Lietotājvārds" required />
            <input type="password" id="newPassword" placeholder="Parole" required />
            <button id="registerButton" onclick="register()">Reģistrēties</button>
            <p id="toggleRegister">Jau ir konts? <a href="#" onclick="showLogin()">Pierakstīties</a></p>
        </div>

        <div class="container hidden" id="dataContainer">
            <h2>Augšupielādējiet CSV Failu</h2>
            <input type="file" id="csvFileInput" accept=".csv" />
            <table id="dataTable" style="display:none;"></table>
            <canvas id="attendanceChart" style="display:none;"></canvas>
        </div>
    </main>
    <footer>
        <p>Sveiks skolotāj!</p>
    </footer>

    <script>
        function showLogin() {
            document.getElementById('authContainer').classList.remove('hidden');
            document.getElementById('registerContainer').classList.add('hidden');
            document.getElementById('dataContainer').classList.add('hidden');
            document.getElementById('authTitle').textContent = 'Pierakstīties';
            document.getElementById('authButton').onclick = login;
        }

        function showRegister() {
            document.getElementById('registerContainer').classList.remove('hidden');
            document.getElementById('authContainer').classList.add('hidden');
            document.getElementById('dataContainer').classList.add('hidden');
        }

        function login() {
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;

            const storedPassword = localStorage.getItem(username);
            if (storedPassword && storedPassword === password) {
                alert('Veiksmīgi pierakstījies!');
                showDataContainer();
            } else {
                alert('Nepareizs lietotājvārds vai parole.');
            }
        }

        function register() {
            const username = document.getElementById('newUsername').value;
            const password = document.getElementById('newPassword').value;

            if (localStorage.getItem(username)) {
                alert('Lietotājvārds jau pastāv.');
            } else {
                localStorage.setItem(username, password);
                alert('Reģistrācija veiksmīga! Tagad varat pierakstīties.');
                showLogin();
            }
        }

        function showDataContainer() {
            document.getElementById('dataContainer').classList.remove('hidden');
            document.getElementById('authContainer').classList.add('hidden');
            document.getElementById('registerContainer').classList.add('hidden');
        }

        document.getElementById('csvFileInput').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const csvData = e.target.result;
                    const parsedData = parseCSV(csvData);
                    displayTable(parsedData);
                    const chartData = processCSVData(parsedData);
                    createChart(chartData);
                };
                reader.readAsText(file);
            }
        });

        function parseCSV(data) {
            const lines = data.split('\n').map(line => line.split(';').map(item => item.trim()));
            return lines.filter(line => line.length > 1); // Filter out empty lines
        }

        function displayTable(data) {
            const table = document.getElementById('dataTable');
            table.style.display = 'table';
            table.innerHTML = '';

            const headerRow = document.createElement('tr');
            data[0].forEach(header => {
                const th = document.createElement('th');
                th.textContent = header;
                headerRow.appendChild(th);
            });
            table.appendChild(headerRow);

            data.slice(1).forEach(row => {
                const tr = document.createElement('tr');
                row.forEach(cell => {
                    const td = document.createElement('td');
                    td.textContent = cell;
                    tr.appendChild(td);
                });
                table.appendChild(tr);
            });
        }

        function processCSVData(data) {
            const monthLabels = ['janvāris', 'februāris', 'marts', 'aprīlis', 'maijs', 'jūnijs', 'jūlijs', 'augusts', 'septembris', 'oktobris', 'novembris', 'decembris'];
            const monthlyMax = {};

            monthLabels.forEach(month => {
                monthlyMax[month] = { location: '', max: 0 };
            });

            data.slice(1).forEach(row => {
                const category = row[1];
                for (let i = 2; i < row.length; i++) {
                    const month = monthLabels[i - 2];
                    const value = parseInt(row[i]) || 0;

                    if (value > monthlyMax[month].max) {
                        monthlyMax[month] = { location: category, max: value };
                    }
                }
            });

            const labels = monthLabels;
            const dataPoints = labels.map(month => monthlyMax[month].max);

            return {
                labels: labels,
                datasets: [{
                    label: 'Populārākā Vietas Apmeklējumi',
                    data: dataPoints,
                    backgroundColor: 'rgba(75, 192, 192, 0.6)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            };
        }

        function createChart(chartData) {
            const ctx = document.getElementById('attendanceChart').getContext('2d');
            const attendanceChart = new Chart(ctx, {
                type: 'bar',
                data: chartData,
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Apmeklētāji'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Mēneši'
                            }
                        }
                    }
                }
            });
            document.getElementById('attendanceChart').style.display = 'block';
        }
    </script>
</body>
</html>