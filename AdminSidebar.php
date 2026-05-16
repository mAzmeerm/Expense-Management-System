<?php
$navItems = [
    'AdminDashboard.php' => '📊Dashboard',
    'AdminExpenseApproval.php' => '✅Expense Approval',
    'AdminBudgetManagement.php' => '💸Budget Management',
    'AdminEmployeeManagement.php' => '🤵Employee Directory',
    'AdminSettings.php' => '⚙️Settings',
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
