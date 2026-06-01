<?php
session_start();
include("dbconn.php");
include("function.php");
?>
<html>

<head>
    <link rel="stylesheet" href="style.css">
    <title>Admin Employee Management</title>
</head>

<body>
    <div class="layout">
        <?php
        $activePage = 'AdminEmployeeManagement.php';
        include 'AdminSidebar.php';
        ?>

        <div class="main-content">
            <?php show_header('Admin Employee Management', $adminName); ?>
            <div class="mn-content">

                <div class="container">

                    <div class="card">

                        <div
                            style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
                            <h3>Employee Management</h3>
                            <a href="AdminAddEmployee.php" class="btn btn-primary" style="text-decoration:none;">
                                + Add New Employee
                            </a>
                        </div>

                        <form class="searchbar" method="get">
                            <div style="flex: 1;">
                                <label>Search employee:</label>
                                <input type="text" name="search" placeholder="Name, email, or department"
                                    value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                            </div>
                            <button class="btn btn-primary" type="submit">Search</button>
                            <a class="btn btn-secondary" href="AdminEmployeeManagement.php"
                                style="text-decoration: none;">Reset</a>
                        </form>

                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Department</th>
                                        <th>Role</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php
</body>

</html>