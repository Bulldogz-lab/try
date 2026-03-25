<?php
header('Content-Type: application/json');
include '../../includes/db.php';

$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

// Same functions as main page
function getFinancialDataFromDB($mysqli, $year) {
    // Get monthly revenue and expenses
    $query = "
        SELECT 
            month,
            SUM(revenue) as total_revenue,
            SUM(maintenance + utilities + salaries + admin) as total_expenses,
            SUM(maintenance) as maintenance,
            SUM(utilities) as utilities,
            SUM(salaries) as salaries,
            SUM(admin) as admin
        FROM financial_records
        WHERE year = ?
        GROUP BY month
        ORDER BY month ASC
    ";
    
    $stmt = $mysqli->prepare($query);
    if (!$stmt) {
        error_log('Prepare failed: ' . $mysqli->error);
        return null;
    }
    
    $stmt->bind_param('i', $year);
    if (!$stmt->execute()) {
        error_log('Execute failed: ' . $stmt->error);
        $stmt->close();
        return null;
    }
    
    $result = $stmt->get_result();
    $monthly_data = [];
    while ($row = $result->fetch_assoc()) {
        $monthly_data[] = $row;
    }
    $stmt->close();

    // Get total revenue for percentage calculation
    $query = "SELECT SUM(revenue) as total_revenue FROM financial_records WHERE year = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('i', $year);
    $stmt->execute();
    $total_result = $stmt->get_result();
    $total_revenue_row = $total_result->fetch_assoc();
    $total_revenue = $total_revenue_row['total_revenue'] ?? 0;
    $stmt->close();

    // Get revenue mix by property
    $query = "
        SELECT 
            p.name,
            SUM(fr.revenue) as total,
            ROUND(SUM(fr.revenue) / ? * 100, 0) as percentage
        FROM financial_records fr
        JOIN properties p ON fr.property_id = p.id
        WHERE fr.year = ?
        GROUP BY fr.property_id, p.name
        ORDER BY total DESC
    ";
    
    $stmt = $mysqli->prepare($query);
    if (!$stmt) {
        error_log('Prepare failed: ' . $mysqli->error);
        return null;
    }
    
    $stmt->bind_param('di', $total_revenue, $year);
    if (!$stmt->execute()) {
        error_log('Execute failed: ' . $stmt->error);
        $stmt->close();
        return null;
    }
    
    $result = $stmt->get_result();
    $revenue_mix = [];
    while ($row = $result->fetch_assoc()) {
        $revenue_mix[$row['name']] = (int)$row['percentage'];
    }
    $stmt->close();

    // Format monthly data into arrays for charts
    $revenue = [];
    $expenses = [];
    $maintenance = [];
    $utilities = [];
    $salaries = [];
    $admin = [];
    $pnl_summary = [];
    
    $month_names = ['', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    $prev_profit = null;
    
    foreach ($monthly_data as $row) {
        $month = (int)$row['month'];
        $rev = (int)$row['total_revenue'] / 1000; // Convert to thousands
        $exp = (int)$row['total_expenses'] / 1000;
        $profit = $rev - $exp;
        $margin = $rev > 0 ? round(($profit / $rev) * 100, 1) : 0;
        
        $revenue[] = $rev;
        $expenses[] = $exp;
        $maintenance[] = (int)$row['maintenance'] / 1000;
        $utilities[] = (int)$row['utilities'] / 1000;
        $salaries[] = (int)$row['salaries'] / 1000;
        $admin[] = (int)$row['admin'] / 1000;
        
        // Format for table display
        $rev_formatted = '₱ ' . number_format($row['total_revenue'], 0);
        $exp_formatted = '₱ ' . number_format($row['total_expenses'], 0);
        $profit_formatted = '₱ ' . number_format($profit * 1000, 0);
        
        // Calculate vs prior month
        $vs_prior = '—';
        if ($prev_profit !== null) {
            $prev_profit_amount = $prev_profit * 1000;
            $current_profit_amount = $profit * 1000;
            $change_pct = $prev_profit_amount > 0 ? round((($current_profit_amount - $prev_profit_amount) / $prev_profit_amount) * 100, 1) : 0;
            
            if ($change_pct > 0) {
                $vs_prior = '▲ ' . $change_pct . '%';
            } elseif ($change_pct < 0) {
                $vs_prior = '▼ ' . abs($change_pct) . '%';
            } else {
                $vs_prior = '—';
            }
        }
        
        $pnl_summary[] = [
            $month_names[$month],
            $rev_formatted,
            $exp_formatted,
            $profit_formatted,
            $margin . '%',
            $vs_prior
        ];
        
        $prev_profit = $profit;
    }

    return [
        'revenue' => $revenue,
        'expenses' => $expenses,
        'maintenance' => $maintenance,
        'utilities' => $utilities,
        'salaries' => $salaries,
        'admin' => $admin,
        'revenue_mix' => $revenue_mix,
        'pnl_summary' => $pnl_summary
    ];
}

function calculateStatsFromDB($mysqli, $year) {
    // Get current year totals
    $query = "
        SELECT 
            SUM(revenue) as total_revenue,
            SUM(maintenance + utilities + salaries + admin) as total_expenses
        FROM financial_records
        WHERE year = ?
    ";
    
    $stmt = $mysqli->prepare($query);
    if (!$stmt) {
        error_log('Prepare failed: ' . $mysqli->error);
        return getDefaultStats();
    }
    
    $stmt->bind_param('i', $year);
    $stmt->execute();
    $result = $stmt->get_result();
    $totals = $result->fetch_assoc();
    $stmt->close();

    $total_revenue = (float)($totals['total_revenue'] ?? 0);
    $total_expenses = (float)($totals['total_expenses'] ?? 0);
    $net_profit = $total_revenue - $total_expenses;
    $roi = $total_revenue > 0 ? round(($net_profit / $total_revenue) * 100, 1) : 0;

    // Calculate YoY growth
    $prev_year = $year - 1;
    $query = "
        SELECT 
            SUM(revenue) as prev_revenue,
            SUM(maintenance + utilities + salaries + admin) as prev_expenses
        FROM financial_records
        WHERE year = ?
    ";
    
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('i', $prev_year);
    $stmt->execute();
    $result = $stmt->get_result();
    $prev_data = $result->fetch_assoc();
    $stmt->close();

    $revenue_growth = 0;
    $expense_growth = 0;
    $profit_growth = 0;

    $prev_revenue = (float)($prev_data['prev_revenue'] ?? 0);
    $prev_expenses = (float)($prev_data['prev_expenses'] ?? 0);
    $prev_profit = $prev_revenue - $prev_expenses;

    if ($prev_revenue > 0) {
        $revenue_growth = round((($total_revenue - $prev_revenue) / $prev_revenue) * 100, 1);
    }
    if ($prev_expenses > 0) {
        $expense_growth = round((($total_expenses - $prev_expenses) / $prev_expenses) * 100, 1);
    }
    if ($prev_profit > 0) {
        $profit_growth = round((($net_profit - $prev_profit) / $prev_profit) * 100, 1);
    }

    return [
        'total_revenue' => $total_revenue,
        'total_expenses' => $total_expenses,
        'net_profit' => $net_profit,
        'roi' => $roi,
        'revenue_growth' => $revenue_growth,
        'expense_growth' => $expense_growth,
        'profit_growth' => $profit_growth
    ];
}

function formatCurrency($amount) {
    if ($amount >= 1000000) {
        return '₱ ' . number_format($amount / 1000000, 2) . 'M';
    } elseif ($amount >= 1000) {
        return '₱ ' . number_format($amount / 1000, 2) . 'K';
    }
    return '₱ ' . number_format($amount, 0);
}

try {
    $financial_data = getFinancialDataFromDB($conn, $year);
    $stats = calculateStatsFromDB($conn, $year);
    
    echo json_encode([
        'success' => true,
        'financial_data' => $financial_data,
        'stats' => $stats
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

$conn->close();
?>