<?php
$navItems = [
    'AdminDashboard.php' => '<span class="menu-item-wrapper"><img src="IconDashboard.svg" alt="Dashboard" width="20" height="20"> Dashboard</span>',
    'AdminExpenseApproval.php' => '<span class="menu-item-wrapper"><img src="IconApproval.svg" alt="Expense Approval" width="20" height="20">  Expense Approval</span>',
    'AdminBudgetManagement.php' => '<span class="menu-item-wrapper"><img src="IconBudget.svg" alt="Budget Management" width="20" height="20">  Budget Management</span>',
    'AdminEmployeeManagement.php' => '<span class="menu-item-wrapper"><img src="IconEmployee.svg" alt="Employee Directory" width="20" height="20">  Employee Directory</span>',
    'AdminSettings.php' => '<span class="menu-item-wrapper"><img src="IconSetting.svg" alt="Settings" width="20" height="20"> Settings</span>',
    'logout.php' => '<span class="menu-item-wrapper"><img src="IconLogOut.svg" alt="Logout" width="20" height="20"> Logout</span>',
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
