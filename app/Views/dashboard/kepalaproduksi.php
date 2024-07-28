<?= $this->extend('layout/default') ?>

<?= $this->section('title') ?>
<title>Home &mdash; Kreuz Bike Indonesia</title>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <section class="section">
        <div class="section-header">
            <h1>Dashboard</h1>
        </div>

        <div class="section-body">
            <p>Welcome, Kepala <?= session()->get('username') ?>!</p>
        </div>
            <div class="row">
                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-primary">
                        <i class="fas fa-inbox" aria-hidden="true"></i>
                        </div>
                        <div class="card-wrap">
                        <div class="card-header">
                            <h4>Permintaan</h4>
                        </div>
                        <div class="card-body">
                            <?= $permintaanCount ?>
                        </div>
                        </div>
                    </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-md-6 col-lg-6">
                        <div class="card">
                        <div class="card-header">
                            <h4>Bar Chart</h4>
                        </div>
                        <div class="card-body"><div class="chartjs-size-monitor"><div class="chartjs-size-monitor-expand"><div class=""></div></div><div class="chartjs-size-monitor-shrink"><div class=""></div></div></div>
                            <canvas id="myChart2" width="610" height="305" style="display: block; width: 610px; height: 305px;" class="chartjs-render-monitor"></canvas>
                        </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6">
                        <div class="card">
                        <div class="card-header">
                            <h4>Pie Chart</h4>
                        </div>
                        <div class="card-body"><div class="chartjs-size-monitor"><div class="chartjs-size-monitor-expand"><div class=""></div></div><div class="chartjs-size-monitor-shrink"><div class=""></div></div></div>
                            <canvas id="myChart4" width="610" height="305" style="display: block; width: 610px; height: 305px;" class="chartjs-render-monitor"></canvas>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                // Bar Chart
                var ctx2 = document.getElementById('myChart2').getContext('2d');
                var myChart2 = new Chart(ctx2, {
                    type: 'bar',
                    data: {
                        labels: ['Permintaan'],
                        datasets: [{
                            label: 'Permintaan',
                            data: [<?= $permintaanCount ?>],
                            backgroundColor: [
                                'rgba(94, 75, 158, 0.8)', 
                                'rgba(255, 0, 0, 0.8)',   
                                'rgba(255, 165, 0, 0.8)', 
                                'rgba(0, 128, 0, 0.8)'    
                            ],
                            borderColor: [
                                'rgba(94, 75, 158, 1)',
                                'rgba(255, 0, 0, 1)',
                                'rgba(255, 165, 0, 1)',
                                'rgba(0, 128, 0, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });

                // Pie Chart
                var ctx4 = document.getElementById('myChart4').getContext('2d');
                var myChart4 = new Chart(ctx4, {
                    type: 'pie',
                    data: {
                        labels: ['Permintaan'],
                        datasets: [{
                            label: 'Total',
                            data: [<?= $permintaanCount ?>],
                            backgroundColor: [
                                'rgba(94, 75, 158, 0.8)', 
                                'rgba(255, 0, 0, 0.8)',   
                                'rgba(255, 165, 0, 0.8)', 
                                'rgba(0, 128, 0, 0.8)'    
                            ],
                            borderColor: [
                                'rgba(94, 75, 158, 1)',
                                'rgba(255, 0, 0, 1)',
                                'rgba(255, 165, 0, 1)',
                                'rgba(0, 128, 0, 1)'
                            ],
                            borderWidth: 1
                        }]
                    }
                });
            });
        </script>
    </section>
<?= $this->endSection() ?>