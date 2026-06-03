<?php
$navItems = [
    'EmployeeDashboard.php' => '<span class="menu-item-wrapper"><img src="IconDashboard.svg" alt="Dashboard" width="20" height="20" style="margin-right: 5px;"> Dashboard</span>',
    'EmployeeSubmitClaim.php' => '<span class="menu-item-wrapper"><img src="IconSubmitClaim.svg" alt="Submit Claim" width="20" height="20" style="margin-right: 5px;"> Submit Claim</span>',
    'EmployeeMyClaim.php' => '<span class="menu-item-wrapper"><img src="IconMyClaim.svg" alt="My Claims" width="20" height="20" style="margin-right: 5px;"> My Claims</span>',
    'EmployeeProfile.php' => '<span class="menu-item-wrapper"><img src="IconProfile.svg" alt="Profile" width="20" height="20" style="margin-right: 5px;"> Profile</span>',
    'logout.php' => '<span class="menu-item-wrapper"><img src="IconLogout.svg" alt="Logout" width="20" height="20" style="margin-right: 5px;"> Logout</span>',
];
?>
<div class="sidebar">
    <div class="sidebar-header">AdadasSport Enterprises</div>
    <nav>
        <?php foreach ($navItems as $file => $label): ?>
            <a class="<?= ($activePage ?? '') === $file ? 'active' : '' ?>" href="<?= $file ?>">
                <?= $label ?>
            </a>
        <?php endforeach; ?>
    </nav>
</div>
