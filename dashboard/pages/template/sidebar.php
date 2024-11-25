<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <div class="sidebar-brand-wrapper d-none d-lg-flex align-items-center justify-content-center fixed-top">
        <a class="sidebar-brand brand-logo" href="../../index.php"><img src="../../assets/images/logo1.svg" alt="" /></a>
        <a class="sidebar-brand brand-logo-mini" href="../../index.php"><img src="../../assets/images/logo1-mini.svg" alt="" /></a>
    </div>
    <ul class="nav">
        <li class="nav-item profile">
            <div class="profile-desc profile-pic">
                <div class="profile-pic">
                    <div class="profile-name">
                        <h5 class="mb-0 font-weight-normal" id="emailDisplay">
                            <?php echo isset($_SESSION['email']) ? $_SESSION['email'] : 'Guest'; ?>
                        </h5>
                        <span id="roleDisplay">
                            <?php echo isset($_SESSION['role']) ? $_SESSION['role'] : 'Guest'; ?>
                        </span>
                    </div>
                </div>
                <a href="#" id="profile-dropdown" data-toggle="dropdown"><i class="mdi mdi-dots-vertical"></i></a>
                <div class="dropdown-menu dropdown-menu-right sidebar-dropdown preview-list" aria-labelledby="profile-dropdown">
                    <div class="dropdown-divider"></div>
                    <a href="changePwd.php" class="dropdown-item preview-item">
                        <div class="preview-thumbnail">
                            <div class="preview-icon bg-dark rounded-circle">
                                <i class="mdi mdi-onepassword text-info"></i>
                            </div>
                        </div>
                        <div class="preview-item-content">
                            <p class="preview-subject ellipsis mb-1 text-small">Change Password</p>
                        </div>
                    </a>
                    <div class="dropdown-divider"></div>
                </div>
            </div>
        </li>
        <li class="nav-item nav-category">
            <span class="nav-link">Navigation</span>
        </li>
        <li class="nav-item menu-items">
            <a class="nav-link" href="../../index.php">
                <span class="menu-icon">
                    <i class="mdi mdi-speedometer"></i>
                </span>
                <span class="menu-title">Dashboard</span>
            </a>
        </li>
        <li class="nav-item menu-items">
            <a class="nav-link" data-toggle="collapse" href="#tabel">
                <span class="menu-icon">
                    <i class="mdi mdi-table-large"></i>
                </span>
                <span class="menu-title">Tabel</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="tabel">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"> 
                        <a class="nav-link" href="../tables/tabelProduk.php">Tabel Produk</a>
                    </li>
                    <li class="nav-item"> 
                        <a class="nav-link" href="../tables/tabelPemasok.php">Tabel Pemasok</a>
                    </li>
                    <li class="nav-item"> 
                        <a class="nav-link" href="../tables/tabelPembelian.php">Tabel Pembelian</a>
                    </li>
                    <li class="nav-item"> 
                        <a class="nav-link" href="../tables/tabelPenjualan.php">Tabel Penjualan</a>
                    </li>
                </ul>
            </div>
        </li>
    </ul>
</nav>