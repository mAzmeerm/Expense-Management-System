<?php
$navItems = [
    'AdminDashboard.php' => '📊Dashboard',
    'AdminExpenseApproval.php' => '✅Expense Approval',
    'AdminBudgetManagement.php' => '💸Budget Management',
    'AdminEmployeeManagement.php' => '🤵Employee Directory',
    'AdminSettings.php' => '⚙️Settings'
];
?>
<div class="sidebar">
    <nav>
        <?php foreach ($navItems as $file => $label): ?>
            <a class="<?= ($activePage ?? '') === $file ? 'active' : '' ?>" href="<?= $file ?>">
                <?= $label ?>
            </a>
        <?php endforeach; ?>
    </nav>
</div>
