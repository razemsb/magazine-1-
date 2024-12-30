<?php
function getPaginatedData($conn, $tableName, $limit, $page) {
    $start = ($page - 1) * $limit;
    $stmt = $conn->prepare("SELECT * FROM $tableName LIMIT ?, ?");
    $stmt->bind_param("ii", $start, $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $countResult = $conn->query("SELECT COUNT(*) AS total FROM $tableName");
    $total = $countResult->fetch_assoc()['total'];
    $pages = ceil($total / $limit);
    
    return [
        'data' => $result->fetch_all(MYSQLI_ASSOC),
        'pages' => $pages
    ];
}
?>