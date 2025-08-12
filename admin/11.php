<?php
// Redirect to dashboard
header('Location: dashboard.php');
exit();
?>
        <div class="col-md-2">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <div class="mb-2"><i class="fas fa-users fa-2x text-primary"></i></div>
                    <h4><?= $patient_count ?></h4>
                    <div>Total Patients</div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <div class="mb-2"><i class="fas fa-user-md fa-2x text-success"></i></div>
                    <h4><?= $doctor_count ?></h4>
                    <div>Total Doctors</div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <div class="mb-2"><i class="fas fa-procedures fa-2x text-info"></i></div>
                    <h4><?= $admitted_patients ?></h4>
                    <div>Admitted Patients</div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <div class="mb-2"><i class="fas fa-bed fa-2x text-warning"></i></div>
                    <h4><?= $occupied_wards ?>/<?= $total_wards ?></h4>
                    <div>Occupied/Total Wards</div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <div class="mb-2"><i class="fas fa-calendar-day fa-2x text-danger"></i></div>
                    <h4><?= $appointments_today ?></h4>
                    <div>Today's Appointments</div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <div class="mb-2"><i class="fas fa-rupee-sign fa-2x text-secondary"></i></div>
                    <h4><?= $total_revenue ?></h4>
                    <div>Total Revenue (Paid)</div>
                </div>
            </div>
        </div>
    </div>
    <!-- Charts -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Appointment Trends (Last 7 Days)</div>
                <div class="card-body">
                    <canvas id="appointmentTrends"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">Patient by Department</div>
                <div class="card-body">
                    <canvas id="deptPie"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">Billing Trend</div>
                <div class="card-body">
                    <canvas id="revenueBar"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Year-wise Budget Analysis</div>
                <div class="card-body">
                    <canvas id="yearlyBudget"></canvas>
                    <div id="yearlyGrowthSummary" class="mt-3"></div>
                </div>
            </div>
        </div>
        <!-- Notifications/Alerts -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Notifications / Alerts</div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><i class="fas fa-clock text-warning"></i> Pending Appointments: <b><?= $pending_appointments ?></b></li>
                    <li class="list-group-item"><i class="fas fa-vials text-info"></i> Pending Lab Tests: <b><?= $pending_lab_tests ?></b></li>
                    <li class="list-group-item"><i class="fas fa-envelope text-primary"></i> Unread Messages: <b><?= $unread_messages ?></b></li>
                    <li class="list-group-item"><i class="fas fa-ambulance text-danger"></i> Emergency Ambulance Requests: <b><?= $emergency_ambulance ?></b></li>
                </ul>
            </div>
        </div>
        <!-- Recent Activity Panel -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Recent Activity</div>
                <ul class="list-group list-group-flush">
                    <?php foreach ($recent_activities as $activity): ?>
                        <li class="list-group-item">
                            <b><?= htmlspecialchars($activity['type']) ?>:</b> <?= htmlspecialchars($activity['title']) ?>
                            <span class="text-muted float-end"><?= date('d M Y H:i', strtotime($activity['created'])) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
    <!-- <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Monthly Revenue Trend</span>
                    <select id="monthlyRevenueYear" class="form-select form-select-sm" style="width:auto;">
                        <?php foreach (array_keys($monthly_revenue) as $yr): ?>
                            <option value="<?= $yr ?>"><?= $yr ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="card-body">
                    <canvas id="monthlyRevenueChart"></canvas>
                </div>
            </div>
        </div>
    </div> -->
    <!-- <div class="row mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Top 5 Revenue Sources (Departments)</div>
                <div class="card-body">
                    <canvas id="topDeptRevenue"></canvas>
                </div>
            </div>
        </div>
    </div> -->
    <!-- Export Modal -->
    <div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <form method="get" action="export_data.php" target="_blank">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exportModalLabel">Export Data</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <div class="mb-3">
                <label for="data_type" class="form-label">Select Data Type</label>
                <select class="form-select" id="data_type" name="data_type" required>
                  <option value="patients">Patients</option>
                  <option value="doctors">Doctors</option>
                  <option value="admissions">Admissions</option>
                  <option value="appointments">Appointments</option>
                  <option value="bills">Bills</option>
                  <option value="lab_tests">Lab Tests</option>
                  <option value="medicines_orders">Medicine Orders</option>
                </select>
              </div>
              <div class="mb-3">
                <label for="from_date" class="form-label">From Date (optional)</label>
                <input type="date" class="form-control" id="from_date" name="from_date">
              </div>
              <div class="mb-3">
                <label for="to_date" class="form-label">To Date (optional)</label>
                <input type="date" class="form-control" id="to_date" name="to_date">
              </div>
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-info"><i class="fas fa-download"></i> Export CSV</button>
            </div>
          </div>
        </form>
      </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Appointment Trends
    new Chart(document.getElementById('appointmentTrends').getContext('2d'), {
        type: 'line',
        data: {
            labels: <?= json_encode($trend_labels) ?>,
            datasets: [{
                label: 'Appointments',
                data: <?= json_encode($trend_data) ?>,
                backgroundColor: 'rgba(19,197,221,0.2)',
                borderColor: 'rgba(19,197,221,1)',
                borderWidth: 2,
                tension: 0.4,
                fill: true,
                pointRadius: 4,
                pointBackgroundColor: 'rgba(19,197,221,1)'
            }]
        },
        options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
    });
    // Department-wise Patient Distribution
    new Chart(document.getElementById('deptPie').getContext('2d'), {
        type: 'pie',
        data: {
            labels: <?= json_encode($dept_labels) ?>,
            datasets: [{
                data: <?= json_encode($dept_data) ?>,
                backgroundColor: ['#13c5dd', '#354f8e', '#f7b731', '#e74c3c', '#2ecc71', '#8e44ad']
            }]
        },
        options: { responsive: true }
    });
    // Billing Trend
    new Chart(document.getElementById('revenueBar').getContext('2d'), {
        type: 'bar',
        data: {
            labels: <?= json_encode($rev_labels) ?>,
            datasets: [{
                label: 'Revenue',
                data: <?= json_encode($rev_data) ?>,
                backgroundColor: 'rgba(53,79,142,0.7)'
            }]
        },
        options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
    });
    // Year-wise Budget Analysis
    new Chart(document.getElementById('yearlyBudget').getContext('2d'), {
        type: 'bar',
        data: {
            labels: <?= json_encode($yearly_labels) ?>,
            datasets: [{
                label: 'Total Revenue',
                data: <?= json_encode($yearly_data) ?>,
                backgroundColor: 'rgba(19,197,221,0.7)'
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });
    // Year-over-year growth summary
    const yearlyLabels = <?= json_encode($yearly_labels) ?>;
    const yearlyGrowth = <?= json_encode($yearly_growth) ?>;
    let growthHtml = '<h6>Year-over-Year Growth:</h6><ul class="list-group">';
    for (let i = 1; i < yearlyLabels.length; i++) {
        let growth = yearlyGrowth[i];
        let sign = growth > 0 ? '+' : '';
        let color = growth > 0 ? 'text-success' : (growth < 0 ? 'text-danger' : 'text-secondary');
        if (growth !== null) {
            growthHtml += `<li class="list-group-item d-flex justify-content-between align-items-center">${yearlyLabels[i-1]} → ${yearlyLabels[i]} <span class="${color}">${sign}${growth}%</span></li>`;
        }
    }
    growthHtml += '</ul>';
    document.getElementById('yearlyGrowthSummary').innerHTML = growthHtml;

    // Monthly Revenue Trend
    const monthlyRevenueData = <?= json_encode($monthly_revenue) ?>;
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    const yearSelect = document.getElementById('monthlyRevenueYear');
    const ctxMonthly = document.getElementById('monthlyRevenueChart').getContext('2d');
    let currentYear = yearSelect.value;

    let monthlyChart = new Chart(ctxMonthly, {
        type: 'line',
        data: {
            labels: months,
            datasets: [{
                label: 'Revenue',
                data: monthlyRevenueData[currentYear],
                backgroundColor: 'rgba(53,79,142,0.2)',
                borderColor: 'rgba(53,79,142,1)',
                borderWidth: 2,
                tension: 0.4,
                fill: true,
                pointRadius: 4,
                pointBackgroundColor: 'rgba(53,79,142,1)'
            }]
        },
        options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
    });

    yearSelect.addEventListener('change', function() {
        currentYear = this.value;
        monthlyChart.data.datasets[0].data = monthlyRevenueData[currentYear];
        monthlyChart.update();
    });

    // Top 5 Revenue Sources (Departments)
    new Chart(document.getElementById('topDeptRevenue').getContext('2d'), {
        type: 'pie',
        data: {
            labels: <?= json_encode($dept_names) ?>,
            datasets: [{
                data: <?= json_encode($dept_totals) ?>,
                backgroundColor: ['#13c5dd', '#354f8e', '#f7b731', '#e74c3c', '#2ecc71']
            }]
        },
        options: { responsive: true }
    });
});
</script>


<?php include '../includes/footer.php'; ?> 