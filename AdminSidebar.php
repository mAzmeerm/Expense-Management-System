<?php
$navItems = [
    'AdminDashboard.php' => '<span class="menu-item-wrapper"><img src="IconDashboard.svg" alt="Dashboard" width="20" height="20" style="margin-right: 5px;"> Dashboard</span>',
    'AdminExpenseApproval.php' => '<span class="menu-item-wrapper"><img src="IconApproval.svg" alt="Expense Approval" width="20" height="20" style="margin-right: 5px;">Expense Approval</span>',
    'AdminBudgetManagement.php' => '<span class="menu-item-wrapper"><img src="IconBudget.svg" alt="Budget Management" width="20" height="20" style="margin-right: 5px;">Budget Management</span>',
    'AdminEmployeeManagement.php' => '<span class="menu-item-wrapper"><img src="IconEmployee.svg" alt="Employee Directory" width="20" height="20" style="margin-right: 5px;">Employee Directory</span>',
    'AdminCategoryManagement.php' => '<span class="menu-item-wrapper"><img src="IconCategory.svg" alt="Category Management" width="20" height="20" style="margin-right: 5px;">Category Management</span>',
    'AdminDepartmentManagement.php' => '<span class="menu-item-wrapper"><img src="IconDepartment.svg" alt="Department Management" width="20" height="20" style="margin-right: 5px;">Department Management</span>',
    'AdminSettings.php' => '<span class="menu-item-wrapper"><img src="IconSetting.svg" alt="Settings" width="20" height="20" style="margin-right: 5px;"> Settings</span>',
    'logout.php' => '<span class="menu-item-wrapper"><img src="IconLogOut.svg" alt="Logout" width="20" height="20" style="margin-right: 5px;"> Logout</span>',
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
