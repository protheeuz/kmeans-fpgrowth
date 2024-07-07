<div class="app-sidebar menu-fixed" data-background-color="mint" data-image="<?php echo $www;?>assets/img/sidebar-bg/01.jpg" data-scroll-to-active="true">
    <div class="sidebar-header">
        <div class="logo clearfix">
            <a class="logo-text float-left" href="<?php echo $www;?>">
                <span class="text">KKGJ Mart</span>
            </a>
            <a class="nav-close d-block d-lg-block d-xl-none" id="sidebarClose" href="javascript:;"><i class="ft-x"></i></a>
        </div>
    </div>
    <div class="sidebar-content main-menu-content">
        <div class="nav-container">
            <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
                
                <!-- Kmeans Group -->
                <li class="navigation-header">
                    <a href="javascript:;" onclick="toggleDropdown('kmeansDropdown', 'kmeansIcon')" style="color: white; font-weight: bold; font-size: 1.2em; padding-left: 20px;">
                        Kmeans
                        <i id="kmeansIcon" class="ft-chevron-<?php echo ($current_page == '' || $current_page == 'kriteria' || $current_page == 'kriteria_update' || $current_page == 'alternatif' || $current_page == 'alternatif_update' || $current_page == 'cluster' || $current_page == 'cluster_update') ? 'down' : 'right'; ?>" style="float: right; margin-right: 20px;"></i>
                    </a>
                </li>
                <ul id="kmeansDropdown" class="submenu <?php if ($current_page == '' || $current_page == 'kriteria' || $current_page == 'kriteria_update' || $current_page == 'alternatif' || $current_page == 'alternatif_update' || $current_page == 'cluster' || $current_page == 'cluster_update') { echo 'show'; } ?>">
                    <li class="<?php if($current_page==''){echo 'active';}?> nav-item">
                        <a href="<?php echo $www;?>"><i class="ft-home"></i><span class="menu-title">Dashboard</span></a>
                    </li>
                    <li class="<?php if($current_page=='kriteria' or $current_page=='kriteria_update'){echo 'active';}?> nav-item">
                        <a href="<?php echo $www;?>kriteria"><i class="ft-box"></i><span class="menu-title">Kriteria</span></a>
                    </li>
                    <li class="<?php if($current_page=='alternatif' or $current_page=='alternatif_update'){echo 'active';}?> nav-item">
                        <a href="<?php echo $www;?>alternatif"><i class="ft-user"></i><span class="menu-title">Alternatif</span></a>
                    </li>
                    <li class="<?php if($current_page=='cluster' or $current_page=='cluster_update'){echo 'active';}?> nav-item">
                        <a href="<?php echo $www;?>cluster"><i class="ft-grid"></i><span class="menu-title">Cluster</span></a>
                    </li>
                </ul>

                <!-- FP-Growth Group -->
                <li class="navigation-header">
                    <a href="javascript:;" onclick="toggleDropdown('fpGrowthDropdown', 'fpGrowthIcon')" style="color: white; font-weight: bold; font-size: 1.2em; padding-left: 20px;">
                        FP-Growth
                        <i id="fpGrowthIcon" class="ft-chevron-<?php echo ($current_page == 'list_penjualan' || $current_page == 'penjualan' || $current_page == 'penjualan_update') ? 'down' : 'right'; ?>" style="float: right; margin-right: 20px;"></i>
                    </a>
                </li>
                <ul id="fpGrowthDropdown" class="submenu <?php if ($current_page == 'list_penjualan' || $current_page == 'penjualan' || $current_page == 'penjualan_update') { echo 'show'; } ?>">
                    <li class="<?php if($current_page=='list_penjualan'){echo 'active';}?> nav-item">
                        <a href="<?php echo $www;?>list_penjualan"><i class="ft-list"></i><span class="menu-title">Daftar Penjualan</span></a>
                    </li>
                    <li class="<?php if($current_page=='penjualan' or $current_page=='penjualan_update'){echo 'active';}?> nav-item">
                        <a href="<?php echo $www;?>penjualan"><i class="ft-shopping-cart"></i><span class="menu-title">Tambah Penjualan</span></a>
                    </li>
                </ul>

                <!-- Hasil -->
                <li class="<?php if($current_page=='hasil'){echo 'active';}?> nav-item">
                    <a href="<?php echo $www;?>hasil"><i class="ft-layers"></i><span class="menu-title">Hasil Perhitungan</span></a>
                </li>
            </ul>
        </div>
    </div>
    <div class="sidebar-background"></div>
</div>

<script type="text/javascript">
    function toggleDropdown(dropdownId, iconId) {
        var dropdown = document.getElementById(dropdownId);
        var icon = document.getElementById(iconId);
        
        if (dropdown.classList.contains('show')) {
            dropdown.classList.remove('show');
            icon.classList.remove('ft-chevron-down');
            icon.classList.add('ft-chevron-right');
        } else {
            dropdown.classList.add('show');
            icon.classList.remove('ft-chevron-right');
            icon.classList.add('ft-chevron-down');
        }
    }
</script>

<style>
    .submenu {
        display: none;
        padding-left: 20px;
    }

    .submenu.show {
        display: block;
    }

    .navigation-header {
        cursor: pointer;
    }

    .nav-item.active > a {
        background-color: #007bff;
        color: white !important;
    }

    .nav-item > a {
        color: white;
    }

    .navigation-header > a {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
</style>