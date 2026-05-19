<?php
$navItems = [
    'EmployeeDashboard.php' => '📊Dashboard',
    'EmployeeSubmitClaim.php' => '🧾Submit Claim',
    'EmployeeMyClaim.php' => '📁 My Claims',
    'EmployeeProfile.php' => '🤵 Profile',
    'logout.php' => '🚪Logout',
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
