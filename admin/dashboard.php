<?php
session_start();
include('include/config.php');

// Proteksi halaman admin agar tidak bisa ditembus tanpa login
if(strlen($_SESSION['alogin'])==0) {   
    header('location:index.php');
    exit();
} else {
    date_default_timezone_set('Asia/Jakarta');

    // --- 1. QUERY KARTU STATISTIK (REAL-TIME) ---
    // Hitung total koleksi baju thrift
    $query_baju = mysqli_query($con, "SELECT COUNT(*) as total FROM products");
    $data_baju = mysqli_fetch_array($query_baju);

    // Hitung total pesanan pending
    $query_pending = mysqli_query($con, "SELECT COUNT(*) as total FROM orders WHERE orderStatus='Pending' OR orderStatus='' OR orderStatus IS NULL");
    $data_pending = mysqli_fetch_array($query_pending);

    // KOREKSI OMSET: Mengembalikan perkalian harga produk dengan kuantitas agar nilainya kembali tepat (Rp 3.250.000)
    $query_omset = mysqli_query($con, "SELECT SUM(products.productPrice * orders.quantity) as total FROM orders JOIN products ON orders.productId=products.id WHERE orders.orderStatus='Delivered'");
    $data_omset = mysqli_fetch_array($query_omset);
    $total_omset = $data_omset['total'] ? $data_omset['total'] : 0;


    // --- 2. QUERY GRAFIK BULANAN ASLI (Dinamis Berdasarkan Bulan Transaksi) ---
    $bulan_labels = [];
    $bulan_data = [];

    // Loop ke belakang untuk mengambil rentang data 6 bulan terakhir secara urut
    for ($i = 5; $i >= 0; $i--) {
        $tanggal_target = date('Y-m-d', strtotime("-$i month"));
        $nama_bulan = date('M', strtotime($tanggal_target));
        $angka_bulan = date('m', strtotime($tanggal_target));
        $tahun_target = date('Y', strtotime($tanggal_target));

        // Hitung total penjualan sukses di tiap bulan target
        $query_bulanan = mysqli_query($con, "SELECT COUNT(*) as total FROM orders WHERE orderStatus='Delivered' AND MONTH(orderDate) = '$angka_bulan' AND YEAR(orderDate) = '$tahun_target'");
        $data_bulanan = mysqli_fetch_array($query_bulanan);
        
        $bulan_labels[] = $nama_bulan;
        $bulan_data[] = $data_bulanan['total'] ? $data_bulanan['total'] : 0;
    }


    // --- 3. QUERY GRAFIK LINGKARAN (KATEGORI PALING LARIS) ---
    $query_pie = mysqli_query($con, "SELECT category.categoryName, SUM(orders.quantity) as total_terjual FROM orders JOIN products ON orders.productId = products.id JOIN category ON products.category = category.id WHERE orders.orderStatus='Delivered' GROUP BY category.id ORDER BY total_terjual DESC LIMIT 3");
    
    $pie_labels = [];
    $pie_data = [];
    while($row_pie = mysqli_fetch_array($query_pie)) {
        $pie_labels[] = $row_pie['categoryName'];
        $pie_data[] = $row_pie['total_terjual'];
    }
    
    if(empty($pie_data)) {
        $pie_labels = ['Belum Ada Penjualan'];
        $pie_data = [1];
    }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Analytics | Garage Sale Studio</title>
    
    <link type="text/css" href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
    <link type="text/css" href="css/theme.css" rel="stylesheet">
    <link type="text/css" href="images/icons/css/font-awesome.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Fraunces:ital,wght@0,700;1,400&display=swap" rel="stylesheet">
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f8f7f5; }
        .navbar-inner { background: #1e1e1e !important; padding: 10px 0; }
        .brand { color: #fff !important; font-weight: 800; }

        /* --- KARTU RINGKASAN DATA (STATS CARDS) --- */
        .analytics-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        @media (max-width: 768px) { .analytics-grid { grid-template-columns: 1fr; } }

        .stat-card {
            background: #ffffff;
            border-radius: 20px;
            padding: 25px 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.02);
            border: 1px solid rgba(141, 119, 95, 0.08);
            position: relative;
        }
        .stat-label { font-size: 12px; font-weight: 700; color: #8d775f; text-transform: uppercase; letter-spacing: 0.5px; }
        .stat-value { font-family: 'Fraunces', serif; font-size: 28px; font-weight: 700; color: #1e1e1e; margin-top: 5px; }
        .stat-icon { position: absolute; right: 30px; top: 30px; font-size: 24px; color: #e6e2d6; }

        /* --- CONTAINER UTAMA GRAFIK AKTIF --- */
        .chart-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 25px;
            margin-bottom: 30px;
        }
        @media (max-width: 900px) { .chart-grid { grid-template-columns: 1fr; } }

        .chart-box {
            background: #ffffff;
            border-radius: 24px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.02);
            border: 1px solid rgba(141, 119, 95, 0.08);
            box-sizing: border-box;
        }
        .chart-title { font-size: 15px; font-weight: 800; color: #1e1e1e; margin-bottom: 20px; }
        .canvas-wrapper { position: relative; height: 300px; width: 100%; }
    </style>
</head>
<body>

    <div class="navbar navbar-fixed-top">
        <div class="navbar-inner">
            <div class="container">
                <a class="brand" href="dashboard.php">⚙️ GS. STUDIO HUB CONTROL</a>
            </div>
        </div>
    </div>

    <div class="wrapper" style="padding-top: 40px;">
        <div class="container">
            <div class="row">
                
                <div class="span3">
                    <div class="sidebar">
                        <ul class="widget widget-menu unstyled">
                            <li class="active"><a href="dashboard.php"><i class="menu-icon icon-dashboard"></i>Dashboard Utama</a></li>
                            <li><a href="manage-products.php"><i class="menu-icon icon-table"></i>Gudang Produk</a></li>
                            <li><a href="insert-product.php"><i class="menu-icon icon-paste"></i>Drop Produk Baru</a></li>
                        </ul>
                        <ul class="widget widget-menu unstyled">
                            <li><a href="category.php"><i class="menu-icon icon-tasks"></i>Kategori</a></li>
                            <li><a href="subcategory.php"><i class="menu-icon icon-tasks"></i>Sub Kategori</a></li>
                        </ul>
                        <ul class="widget widget-menu unstyled">
                            <li><a href="pending-orders.php"><i class="menu-icon icon-tasks"></i>Pesanan Pending</a></li>
                            <li><a href="delivered-orders.php"><i class="menu-icon icon-inbox"></i>Pesanan Sukses</a></li>
                        </ul>
                    </div>
                </div>

                <div class="span9">
                    <div class="content">

                        <div style="margin-bottom: 30px;">
                            <h2 style="font-weight: 800; color: #1e1e1e; margin: 0; font-family: 'Fraunces', serif; font-size: 32px;">Overview Analytics</h2>
                            <p style="color: #888; font-size: 13px; margin-top: 2px;">Pantau performa sirkulasi bisnis pakaian curated thrift secara real-time.</p>
                        </div>

                        <div class="analytics-grid">
                            <div class="stat-card">
                                <div class="stat-label">Baju Koleksi</div>
                                <div class="stat-value"><?php echo $data_baju['total']; ?> <span style="font-size:14px; font-family:'Plus Jakarta Sans'; color:#888;">Items</span></div>
                                <i class="icon-folder-open stat-icon"></i>
                            </div>
                            <div class="stat-card" style="border-left: 3px solid #d4a373;">
                                <div class="stat-label" style="color: #d4a373;">Antrean Pending</div>
                                <div class="stat-value" style="color: #d4a373;"><?php echo $data_pending['total']; ?> <span style="font-size:14px; font-family:'Plus Jakarta Sans';">Orders</span></div>
                                <i class="icon-time stat-icon" style="color:#fbe8b3;"></i>
                            </div>
                            <div class="stat-card">
                                <div class="stat-label">Total Omset</div>
                                <div class="stat-value" style="font-size: 24px;">Rp <?php echo number_format($total_omset, 0, ',', '.'); ?></div>
                                <i class="icon-money stat-icon"></i>
                            </div>
                        </div>

                        <div class="chart-grid">
                            <div class="chart-box">
                                <div class="chart-title">📈 Trafik Transaksi Sukses (6 Bulan Terakhir)</div>
                                <div class="canvas-wrapper">
                                    <canvas id="trafficChart"></canvas>
                                </div>
                            </div>
                            <div class="chart-box">
                                <div class="chart-title">🏷️ Kategori Terlaris</div>
                                <div class="canvas-wrapper">
                                    <canvas id="categoryChart"></canvas>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="scripts/jquery-1.9.1.min.js" type="text/javascript"></script>
    <script src="bootstrap/js/bootstrap.min.js" type="text/javascript"></script>

    <script>
    // --- 1. CONFIG GRAFIK GARIS BULANAN ASLI ---
    const ctxTraffic = document.getElementById('trafficChart').getContext('2d');
    new Chart(ctxTraffic, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($bulan_labels); ?>,
            datasets: [{
                label: 'Pesanan Selesai',
                data: <?php echo json_encode($bulan_data); ?>,
                borderColor: '#8d775f',
                backgroundColor: 'rgba(141, 119, 95, 0.05)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#1e1e1e',
                pointHoverBackgroundColor: '#8d775f',
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { 
                    beginAtZero: true, 
                    ticks: { 
                        stepSize: 1, 
                        font: { family: 'Plus Jakarta Sans', size: 11 } 
                    }, 
                    grid: { color: 'rgba(0,0,0,0.03)' } 
                },
                x: { 
                    grid: { display: false }, 
                    ticks: { font: { family: 'Plus Jakarta Sans', size: 11 } } 
                }
            }
        }
    });

    // --- 2. CONFIG GRAFIK LINGKARAN (KATEGORI NYATA) ---
    const ctxCategory = document.getElementById('categoryChart').getContext('2d');
    new Chart(ctxCategory, {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode($pie_labels); ?>,
            datasets: [{
                data: <?php echo json_encode($pie_data); ?>,
                backgroundColor: ['#1e1e1e', '#8d775f', '#d4a373'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { boxWidth: 12, font: { family: 'Plus Jakarta Sans', size: 11 } } }
            },
            cutout: '70%'
        }
    });
    </script>
</body>
</html>
<?php } ?>
