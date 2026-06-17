const password = document.getElementById("Password");
const toggle = document.getElementById("toggle");

toggle.addEventListener("click", function () {
    if (password.type === "password") {
        password.type = "text";
        toggle.textContent = "Hide";
    } else {
        password.type = "password";
        toggle.textContent = "Show";
    }
});

function liveSearch() {
    // 1. Grab the text typed by the user and convert to lowercase
    const filter = document.getElementById('tableSearch').value.toLowerCase();
    
    // 2. Select all rows inside your table body
    const tableRows = document.querySelectorAll('.table-responsive table tbody tr');

    tableRows.forEach(row => {
        // Skip the "No matching expense claims found" row if it's showing
        if (row.cells.length === 1 && row.cells[0].colSpan > 1) return;

        // 3. Combine text from all columns in this row (Employee, Department, Category, Status)
        const rowText = row.textContent.toLowerCase();

        // 4. If the text matches, show the row; otherwise, hide it instantly
        if (rowText.includes(filter)) {
            row.style.display = ''; // Shows the row
        } else {
            row.style.display = 'none'; // Hides the row
        }
    });
}
