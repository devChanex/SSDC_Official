<?php
include_once("properties.php");
error_reporting(0);
session_start();
?>

<link href="css/custom-v1.css" rel="stylesheet">

<style>
/* ===== TOPBAR FIX ===== */
.topbar-custom {
    background: #ffffff;
    height: 60px;
    display: flex;
    align-items: center;
    padding: 0 20px;
}

.topbar-left {
    display: flex;
    align-items: center;
    gap: 15px;
}

.topbar-right {
    margin-left: auto;
    display: flex;
    align-items: center;
    gap: 20px;
}

/* COMPANY NAME */
.company-name {
    font-weight: 600;
    font-size: 16px;
    color: #6c757d;
    white-space: nowrap;
}

/* WIFI */
.wifi-container {
    display: flex;
    align-items: flex-end;
    height: 18px;
    position: relative;
}

.wifi-bar {
    width: 4px;
    margin-right: 3px;
    background-color: #ddd;
    border-radius: 2px;
    transition: 0.3s;
}

.bar1 { height: 5px; }
.bar2 { height: 8px; }
.bar3 { height: 12px; }
.bar4 { height: 16px; }

.wifi-offline::after {
    content: "✖";
    color: red;
    font-size: 14px;
    position: absolute;
    left: 4px;
    top: -6px;
}

/* USER */
.user-info {
    display: flex;
    align-items: center;
    gap: 8px;
}

.user-name {
    font-size: 14px;
    color: #6c757d;
}

.img-profile {
    width: 32px;
    height: 32px;
    border-radius: 50%;
}

/* ===== RESPONSIVE ===== */
@media (max-width: 768px) {
    .company-name {
        font-size: 14px;
    }

    .user-name {
        display: none; /* Hide name on mobile */
    }

    .topbar-custom {
        padding: 0 10px;
    }
}
</style>

<nav class="topbar-custom shadow-sm">

    <!-- LEFT -->
    <div class="topbar-left">
        <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle">
            <i class="fa fa-bars"></i>
        </button>

        <div class="company-name">
            <?php echo $systemname; ?>
        </div>
    </div>

    <!-- RIGHT -->
    <div class="topbar-right">

        <!-- WIFI STATUS -->
         
        <div id="wifiStatus" class="wifi-container">
            <div class="wifi-bar bar1"></div>
            <div class="wifi-bar bar2"></div>
            <div class="wifi-bar bar3"></div>
            <div class="wifi-bar bar4"></div>
        </div>

        <!-- USER -->
        <div class="dropdown user-info">
            <a class="d-flex align-items-center text-decoration-none dropdown-toggle"
               href="#"
               id="userDropdown"
               data-toggle="dropdown">

                <span class="user-name">
                    <?php echo $_SESSION["username"]; ?>
                </span>

                <img class="img-profile" src="img/undraw_profile.svg">
            </a>

            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in">
                <a class="dropdown-item" href="#" onclick="backup()">
                    <i class="fas fa-download fa-sm fa-fw mr-2 text-gray-400"></i>
                    Backup
                </a>
                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                    Logout
                </a>
            </div>
        </div>

    </div>

</nav>

<script>
function updateWifiStatus() {
    fetch("network_status.php")
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById("wifiStatus");
            const bars = container.querySelectorAll(".wifi-bar");

            // Reset
            bars.forEach(bar => {
                bar.style.backgroundColor = "#ddd";
            });
            container.classList.remove("wifi-offline");

            if (data.status === "offline") {
                container.classList.add("wifi-offline");
                return;
            }

            let color = "green";
            if (data.status === "fair") color = "amber";
            if (data.status === "slow") color = "red";

            for (let i = 0; i < data.bars; i++) {
                bars[i].style.backgroundColor = color;
            }
        })
        .catch(() => {
            document.getElementById("wifiStatus").classList.add("wifi-offline");
        });
}

// Initial load
updateWifiStatus();

// Refresh every 5 seconds (no page reload)
setInterval(updateWifiStatus, 5000);
</script>

<?php
include_once("bars/toast.php");
?>
