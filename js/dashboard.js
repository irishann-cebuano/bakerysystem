function loadDashboard() {
    const dashboard = $("#dashboardCards");
    const today = new Date();
    const yyyy = today.getFullYear();
    const mm = String(today.getMonth() + 1).padStart(2, "0");
    const dd = String(today.getDate()).padStart(2, "0");
    const todayStr = `${yyyy}-${mm}-${dd}`;

    // Initialize totals
    let totals = {
        "Today's Expenses": 0,
        "Labor Cost": 0,
        "Ingredients Cost": 0,
        "Utilities": 0,
        "Supply": 0,
        "Production/Packaging": 0,
        
    };

  
    $.get("/bakery_records_system/php/dailyrec.php", function(data) {
        const records = data.records || [];

        // Sum today's records
        records.forEach(r => {
            if ((r.date || "").slice(0, 10) === todayStr) {   // <-- fix here
            const amount = parseFloat(r.amount) || 0;
            totals["Today's Expenses"] += amount;

                const cat = (r.expense_category || "").toLowerCase();
                if (cat.includes("labor")) totals["Labor Cost"] += amount;
                if (cat.includes("ingredient")) totals["Ingredients Cost"] += amount;
                if (cat.includes("utilities")) totals["Utilities"] += amount;
                if (cat.includes("supply")) totals["Supply"] += amount;
                if (cat.includes("production")) totals["Production/Packaging"] += amount; 

            }   
        });

       
        $.get("/bakery_records_system/php/laborrec.php?action=fetch", function(laborData) {
            const laborRecords = laborData.records || [];

            // Map record_id → date from daily records
            let recordDateMap = {};
            records.forEach(r => {
                recordDateMap[r.record_id] = r.date;
            });

            laborRecords.forEach(l => {
                const recDate = (recordDateMap[l.record_id] || "").slice(0, 10); // <-- fix here
                if (recDate === todayStr) {
                totals["Labor Cost"] += parseFloat(l.total_pay) || 0;
    }
});

            // Build dashboard cards HTML
            let html = "";
            const icons = {
                "Today's Expenses": "💰",
                "Labor Cost": "👥",
                "Ingredients Cost": "🥖",
                "Utilities": "⚡",
                "Supply": "📦",
                "Production/Packaging": "🏭"
            };
            for (let key in totals) {
                const icon = icons[key] || "📊";
                html += `
                    <div class="card">
                        <div class="icon">${icon}</div>
                        <h3>${key}</h3>
                        <p>₱${totals[key].toFixed(2)}</p>
                    </div>
                `;
            }

            dashboard.html(`
                <div class="welcome-message">
                    <h2>🍞 Welcome to Your Bakery Dashboard! 🥖</h2>
                    <p>Track your daily expenses, manage employees, and keep your bakery running smoothly.</p>
                </div>
                <div class="cards-container">
                    ${html}
                </div>
            `);
        }, "json");
    }, "json");
}

// Initial load
$(document).ready(function() {
    loadDashboard();
});
