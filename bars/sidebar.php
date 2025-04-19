<?php

include_once("properties.php");

echo '
 <!-- Sidebar -->
        <ul class="navbar-nav ' . $sidebarColor . ' sidebar sidebar-dark  toggled" id="accordionSidebar">

          

             <li class="nav-item">
                <a class="nav-link" href="basecode.php">
                
                 <strong>SSDC</strong></a>
            </li>
            <!-- Divider -->
            <hr class="sidebar-divider my-0">
            <!-- Nav Item - Dashboard -->
            <li class="nav-item">
                <a class="nav-link" href="basecode.php">
                    <i class="fas fa-fw fa-home"></i>
                    <span>Dashboard</span></a>
            </li>
           
         <li class="nav-item">
                <a class="nav-link" href="#" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false"
                    aria-controls="collapseTwo">
                    <i class="fas fa-address-card"></i>
                    <span>Client Profile</span>
                    </a>
                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo"
                    data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item" href="clientProfileList.php">View List</a>
				<a class="collapse-item" href="registerClient.php">Register Client</a>
                    </div>
                </div>
            </li>

    <li class="nav-item">
    <a class="nav-link" href="consentList.php" >
        <i class="fas fa-file"></i>
        <span>Consent List</span>
    </a>
    </li>
        <li class="nav-item">
    <a class="nav-link" href="hmoList.php" >
        <i class="fas fa-heart"></i>
        <span>HMO</span>
    </a>
   
    </li>
        <li class="nav-item">
                <a class="nav-link" href="#" data-toggle="collapse" data-target="#clientTreatment" aria-expanded="false"
                    aria-controls="clientTreatment">
                    <i class="fas fa-tooth"></i>
                    <span>Client Treatment</span>
                </a>
            <div id="clientTreatment" class="collapse" aria-labelledby="clientTreatment" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item" href="clientTreatmentList.php">Client Treatment List</a>
                    <a class="collapse-item" href="allSoaList.php">SOA List</a>

                </div>
            </div>
        </li>
        <li class="nav-item">
                <a class="nav-link" href="#" data-toggle="collapse" data-target="#config" aria-expanded="false"
                    aria-controls="config">
                    <i class="fas fa-cog"></i>
                    <span>Configurations</span>
                    </a>
                <div id="config" class="collapse" aria-labelledby="config"
                    data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item" href="treatmentList.php">Treatment List</a>
                     
                    </div>
                </div>
            </li>
<li class="nav-item">
    <a class="nav-link" href="#" data-toggle="collapse" data-target="#report" aria-expanded="false"
        aria-controls="config">
        <i class="fas fa-file"></i>
        <span>Reports</span>
    </a>
    <div id="report" class="collapse" aria-labelledby="report" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <a class="collapse-item" href="soadaterange.php">SOA Summary per Date</a>
            <a class="collapse-item" href="soaperclient.php">SOA Summary per Client</a>
             <a class="collapse-item" href="soaperdentist.php">SOA Summary per Dentist</a>
            <a class="collapse-item" href="clienttreatmentrecordperdate.php">CT Records per Date</a>
            <a class="collapse-item" href="clienttreatmentrecordperclient.php">CT Records per Client</a>
             <a class="collapse-item" href="clienttreatmentrecordperdentist.php">CT Records per Dentist</a>
            <a class="collapse-item" href="clienttreatmentrecordpertreatment.php">CT Records per Treatment</a>
        </div>
    </div>
</li>
        </ul>
        <!-- End of Sidebar -->

';
