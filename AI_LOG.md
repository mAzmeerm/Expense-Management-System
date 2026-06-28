# AI Usage & Disclosure Log

Project Component: Real-time Search and Filter Integration (Admin & Employee Dashboards)
AI Tool Used: Gemini 3.5
---
1. The Intent
What specific problem were you trying to solve with AI?

I needed to implement an instant, client-side live search and dropdown filtering mechanism for multiple management tables (`AdminBudgetManagement.php`, `AdminDepartmentManagement.php`, and employee expense claims) without triggering slow page reloads. I also needed to fix pagination logic and SQL syntax errors where data counts and user scopes were colliding.

---

2. The Interaction (Prompting)
What was your primary prompt, and did you have to refine it?

Initial Prompt: I asked why the live search bar stopped working after setting up server-side pagination.
- Refinement 1: After identifying that pagination limits (`LIMIT 10`) prevented front-end scripts from seeing records on other pages, I shifted to a single-bar unified approach.
- Refinement 2:I provided the exact `liveSearch()` code from `script.js` to see why it broke on pages with fewer table columns (like Department Management). 
- Refinement 3: I shared the backend PHP logic for expense claims when column case-sensitivity issues (`Status` vs `status`) and `AND/OR` logical operator traps caused search states to vanish or display incorrect employee metrics.

---

3. The Output & Verification
Did the AI provide working code? How did you test/verify it?

The AI provided optimized versions of the code, but architectural mismatches occurred during implementation:
- The script initially failed because the HTML table structure in `AdminDepartmentManagement.php` lacked the outer container wrapper class (`.table-responsive`) that `script.js` relied on to fetch rows. 
- The table array keys mismatched because the database column was queried as `c.Status` while the frontend was trying to render it via lowercase `$row['status']`. 
Verification: Verified by wrapping the table in the correct CSS div and adding a SQL alias (`c.Status AS Status, c.Status AS status`) to satisfy both casing variations seamlessly.

---

4. Human Refinement
What changes did YOU make to the AI’s code to make it fit your specific project?

- Universal Script Patching: Modified the global `script.js` file to safely ignore *any* fallback "No data found" rows dynamically (`if (row.cells.length === 1) return;`) instead of relying on restrictive column-span checking. This made the script universally compatible across all modules regardless of whether a table has 3 columns or 8 columns.
- Security Scoping: Isolated global data streams by grouping the search queries using explicit brackets `AND (cat.CategoryName LIKE ... OR c.Description LIKE ...)` to guarantee that logged-in students/employees only see their own private claims data.

--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Project Component: UI Layout, Real-Time Search, & Backend Filtering Systems
AI Tool Used: Gemini 3.5

---

 1. The Intent
What specific problem were you trying to solve with AI?

I needed to implement an instant, client-side live search and dropdown filtering mechanism for multiple administrative and employee management views. The goal was to allow quick data auditing without slow page reloads, while maintaining stable server-side table pagination and secure user-session boundaries.

---

2. The Interaction (Prompting)
What was your primary prompt, and did you have to refine it?

Initial Prompt: I requested a solution for why a client-side JavaScript live search stopped working immediately after implementing server-side pagination layout limits (`LIMIT 10`).
- Refinement 1: The AI suggested switching back to raw HTML form submissions. I rejected this because it broke the fluid experience, and forced a workflow change to keep it purely instant.
- Refinement 2: I provided the exact logic block inside `script.js` to diagnose why the script broke on tables with varying column lengths (like Departments).
- Refinement 3: I shared the backend database queries when data states vanished due to SQL priority bugs, missing query variables, and case-sensitivity string collisions (`Status` vs `status`).

---

3. The Output & Verification
Did the AI provide working code? How did you test/verify it?

The AI provided optimized script and query blocks, but implementation blocks required hands-on debugging due to front-end structural mismatches:
- The Selector Break:The global search script initially returned zero results on the Department page because the HTML table lacked the outer container wrapper class (`.table-responsive`) that `script.js` used as its target hook.
- The Array Key Crash: Table data cells turned blank because the backend SQL query outputted data using a capitalized string field (`c.Status`), while the front-end layout code attempted to echo it using lowercase variables (`$row['status']`).
- Verification: Verified by checking the browser developer tools console tab, adding the required structural `div` class, and using SQL aliases (`SELECT c.Status AS Status, c.Status AS status`) to fulfill both casing variants natively.

---

4. Human Refinement
What changes did YOU make to the AI’s code to make it fit your specific project?

- Universal Script Overhaul: I rewrote the core condition inside `script.js`. I stripped out the restrictive column-span parameters and replaced them with a dynamic row checker (`if (row.cells.length === 1) return;`). This simple fix allows the single script file to run flawlessly across every single page in the project, whether a table has 3 columns or 8 columns.
- UI Syncing: I removed structural form elements that triggered forced browser refreshes and linked dropdown changes directly to input events using clean, hidden inline assignments (`onchange="document.getElementById('tableSearch').value = this.value; liveSearch();"`).
- Security & Priority Scoping: I manually restructured the backend queries to wrap the keyword matching conditions within explicit brackets: `WHERE c.EmployeeID = '$loggedInUser' AND (cat.CategoryName LIKE ... OR c.Description LIKE ...)`. This fixed a dangerous query leak, ensuring users can only search through records belonging exclusively to their own session ID.

--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Project Component: Database Relationships & Dynamic Dropdown Generation
AI Tool Used: Gemini 3.5

---

 1. The Intent
What specific problem were you trying to solve with AI?

I needed to dynamically populate filter dropdown options (like filtering by Year or Department) directly from the database entries. The values needed to stay completely synchronized with the live rows on the page without hardcoding values into the HTML.
---

2. The Interaction (Prompting)
What was your primary prompt, and did you have to refine it?

Initial Prompt: I asked how to loop database data into an HTML `<select>` element to generate filter options automatically.
- Refinement 1: The AI first generated standard query loops, but I had to refine the implementation because the original database records contained duplicate values (e.g., multiple budgets from the same year). This caused the dropdown menu to display the same option multiple times.

---

3. The Output & Verification
Did the AI provide working code? How did you test/verify it?

The AI provided standard looping structures using `while($row = mysqli_fetch_assoc())`, which worked structurally but required database modification to fix data redundancy.
- Verification: I looked at the dropdown UI and saw repetitive lists. To verify the fix, I updated the SQL query clauses to filter out duplicates at the database level before rendering the HTML.

---

4. Human Refinement
What changes did YOU make to the AI’s code to make it fit your specific project?

- SQL Query Optimization: I added the `DISTINCT` keyword to the select queries (e.g., `SELECT DISTINCT DepartmentName` and `SELECT DISTINCT Year`). This forced MySQL to only return unique values, keeping the user interface clean.
- Security Layer Integration: I wrapped every dynamic option output in `htmlspecialchars()` to prevent potential Cross-Site Scripting (XSS) payload injection vulnerabilities through database fields.

--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
Project Component: Database Relationships & Dynamic Dropdown Generation
AI Tool Used: Gemini 3.5

---

 1. The Intent
What specific problem were you trying to solve with AI?

I needed to dynamically populate filter dropdown options (like filtering by Year or Department) directly from the database entries. The values needed to stay completely synchronized with the live rows on the page without hardcoding values into the HTML.
---

2. The Interaction (Prompting)
What was your primary prompt, and did you have to refine it?

Initial Prompt: I asked how to loop database data into an HTML `<select>` element to generate filter options automatically.
- Refinement 1: The AI first generated standard query loops, but I had to refine the implementation because the original database records contained duplicate values (e.g., multiple budgets from the same year). This caused the dropdown menu to display the same option multiple times.

---

3. The Output & Verification
Did the AI provide working code? How did you test/verify it?

The AI provided standard looping structures using `while($row = mysqli_fetch_assoc())`, which worked structurally but required database modification to fix data redundancy.
- Verification: I looked at the dropdown UI and saw repetitive lists. To verify the fix, I updated the SQL query clauses to filter out duplicates at the database level before rendering the HTML.

---

4. Human Refinement
What changes did YOU make to the AI’s code to make it fit your specific project?

- SQL Query Optimization: I added the `DISTINCT` keyword to the select queries (e.g., `SELECT DISTINCT DepartmentName` and `SELECT DISTINCT Year`). This forced MySQL to only return unique values, keeping the user interface clean.
- Security Layer Integration: I wrapped every dynamic option output in `htmlspecialchars()` to prevent potential Cross-Site Scripting (XSS) payload injection vulnerabilities through database fields.

