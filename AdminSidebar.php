<?php
$navItems = [
    'AdminDashboard.php' => '<span class="menu-item-wrapper"><img src="IconDashboard.svg" alt="Dashboard" width="20" height="20" style="margin-right: 5px;"> Dashboard</span>',
    'AdminExpenseApproval.php' => '<span class="menu-item-wrapper"><img src="IconApproval.svg" alt="Expense Approval" width="20" height="20" style="margin-right: 5px;">Expense Approval</span>',
    'AdminBudgetManagement.php' => '<span class="menu-item-wrapper"><img src="IconBudget.svg" alt="Budget Management" width="20" height="20" style="margin-right: 5px;">Budget Management</span>',
    'AdminEmployeeManagement.php' => '<span class="menu-item-wrapper"><img src="IconEmployee.svg" alt="Employee Directory" width="20" height="20" style="margin-right: 5px;">Employee Directory</span>',
    'AdminCategoryManagement.php' => '<span class="menu-item-wrapper"><img src="IconCategory.svg" alt="Category Management" width="20" height="20" style="margin-right: 5px;">Category Management</span>',
    'AdminDepartmentManagement.php' => '<span class="menu-item-wrapper"><img src="IconDepartment.svg" alt="Department Management" width="20" height="20" style="margin-right: 5px;">Department Management</span>',
    'AdminProfile.php' => '<span class="menu-item-wrapper"><img src="IconProfile.svg" alt="Settings" width="20" height="20" style="margin-right: 5px;"> Profile </span>',
    'logout.php' => '<span class="menu-item-wrapper"><img src="IconLogOut.svg" alt="Logout" width="20" height="20" style="margin-right: 5px;"> Logout</span>',
];
?>
<div class="sidebar">
    <div class="sidebar-header"><img src="logo.png" alt="AdadasSport Logo" width="100" height="100"></div>
    <nav>
        <?php foreach ($navItems as $file => $label): ?>
            <a class="<?= ($activePage ?? '') === $file ? 'active' : '' ?>" href="<?= $file ?>">
                <?= $label ?>
            </a>
        <?php endforeach; ?>
    </nav>
</div>
