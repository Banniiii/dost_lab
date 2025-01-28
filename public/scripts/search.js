// Function to filter table rows based on search query
function filterTableRows() {
    // Get the search query
    const query = document.getElementById('searchInput').value.toLowerCase();

    // Determine the current page based on the presence of specific containers
    const isLabPage = document.querySelector('.lab-container') !== null;
    const isItemPage = document.querySelector('.item-container') !== null;
    const isInventoryPage = document.querySelector('.inventory-container') !== null;
    const isReportPage = document.querySelector('.report-container') !== null;
    const isTransactionPage = document.querySelector('.transaction-container') !== null;
    const isItemHistoryPage = document.querySelector('.transaction-container') !== null;
    const isInventoryHistoryPage = document.querySelector('.transaction-container') !== null;

    if (isLabPage) {
        const labRows = document.querySelectorAll('.lab-container table tbody tr');
        labRows.forEach(row => {
            const cells = row.querySelectorAll('td');
            let match = false;
            cells.forEach(cell => {
                if (cell.textContent.toLowerCase().includes(query)) {
                    match = true;
                }
            });
            row.style.display = match ? '' : 'none';
        });
    }

    if (isItemPage) {
        const itemRows = document.querySelectorAll('.item-container table tbody tr');
        itemRows.forEach(row => {
            const cells = row.querySelectorAll('td');
            let match = false;
            cells.forEach(cell => {
                if (cell.textContent.toLowerCase().includes(query)) {
                    match = true;
                }
            });
            row.style.display = match ? '' : 'none';
        });
    }

    if (isInventoryPage) {
        const inventoryRows = document.querySelectorAll('.inventory-container table tbody tr');
        inventoryRows.forEach(row => {
            const cells = row.querySelectorAll('td');
            let match = false;
            cells.forEach(cell => {
                if (cell.textContent.toLowerCase().includes(query)) {
                    match = true;
                }
            });
            row.style.display = match ? '' : 'none';
        });
    }

    if (isReportPage) {
        const reportRows = document.querySelectorAll('.report-container table tbody tr');
        reportRows.forEach(row => {
            const cells = row.querySelectorAll('td');
            let match = false;
            cells.forEach(cell => {
                if (cell.textContent.toLowerCase().includes(query)) {
                    match = true;
                }
            });
            row.style.display = match ? '' : 'none';
        });
    }

    if (isTransactionPage || isItemHistoryPage || isInventoryHistoryPage) {
        const transactionRows = document.querySelectorAll('.transaction-container table tbody tr');
        transactionRows.forEach(row => {
            const cells = row.querySelectorAll('td');
            let match = false;
            cells.forEach(cell => {
                if (cell.textContent.toLowerCase().includes(query)) {
                    match = true;
                }
            });
            row.style.display = match ? '' : 'none';
        });
    }
}

// Add event listener to the search input
document.getElementById('searchInput').addEventListener('input', filterTableRows);
