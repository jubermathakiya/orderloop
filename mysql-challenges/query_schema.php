<?php 


echo "1) Question 1: Total Sales Revenue by Product". "<br><br>";

echo "<br>". "SELECT oi.product_id, p.name, SUM(oi.quantity * oi.price) AS total_revenue FROM order_items oi JOIN products p ON p.id=oi.product_id GROUP BY oi.product_id ORDER BY oi.product_id";

echo "<br>"."==============================================================="."<br>";

echo "2) Question 2: Top Customers by Spending". "<br><br>";

echo  "SELECT c.id, c.name, SUM(oi.quantity * oi.price) AS total_spending FROM order_items oi JOIN orders o ON o.id=oi.order_id JOIN customers c ON c.id=o.customer_id GROUP BY c.id ORDER BY total_spending DESC LIMIT 5";

echo "<br>"."==============================================================="."<br>";

echo "Question 3: Average Order Value per Customer". "<br><br>";

echo "SELECT c.id as custom_id, c.name, oi.order_id, (SUM(oi.quantity * oi.price) / COUNT( oi.order_id)) average_order_value FROM order_items oi JOIN orders o ON o.id=oi.order_id JOIN customers c ON o.customer_id = c.id
 GROUP BY oi.order_id";

echo "<br>"."==============================================================="."<br>";

echo "Question 4: Recent Orders". "<br><br>";

echo "SELECT o.id AS order_id, c.name AS customer_name, o.order_date, o.status AS order_status FROM orders o JOIN customers c ON o.customer_id = c.id WHERE o.order_date >= NOW() - INTERVAL 30 DAY ORDER BY o.order_date DESC";


echo "<br>"."==============================================================="."<br>";
echo "Question 5: Running Total of Customer Spending". "<br><br>";

echo "WITH OrderTotals AS (
    SELECT 
        o.customer_id,
        o.id AS order_id,
        o.order_date,
        SUM(oi.quantity * oi.price) AS order_total
    FROM order_items oi
    JOIN orders o ON oi.order_id = o.id
    GROUP BY o.customer_id, o.id, o.order_date
)
SELECT 
    ot.customer_id,
    ot.order_id,
    ot.order_date,
    ot.order_total,
    SUM(ot.order_total) OVER (
        PARTITION BY ot.customer_id 
        ORDER BY ot.order_date
    ) AS running_total
FROM OrderTotals ot
ORDER BY ot.customer_id, ot.order_date";

echo "<br>"."==============================================================="."<br>";

echo "6: Product Review Summary". "<br><br>";
echo "SELECT 
    p.id AS product_id,
    p.name AS product_name,
    COALESCE(AVG(r.rating), 0) AS average_rating,
    COUNT(r.id) AS total_reviews
FROM products p
LEFT JOIN reviews r ON p.id = r.product_id
GROUP BY p.id, p.name
ORDER BY average_rating DESC, total_reviews DESC";

echo "<br>"."==============================================================="."<br>";

echo "Question 7: Customers Without Orders". "<br><br>";

echo "SELECT 
    c.id AS customer_id,
    c.name AS customer_name
FROM customers c
LEFT JOIN orders o ON c.id = o.customer_id
WHERE o.id IS NULL";

echo "<br>"."==============================================================="."<br>";

echo "Question 8: Update Last Purchased Date". "<br><br>";
echo "UPDATE products p
JOIN (
    SELECT 
        oi.product_id,
        MAX(o.order_date) AS last_purchased_date
    FROM order_items oi
    JOIN orders o ON oi.order_id = o.id
    GROUP BY oi.product_id
) latest_orders ON p.id = latest_orders.product_id
SET p.last_purchased = latest_orders.last_purchased_date";
echo "<br>"."==============================================================="."<br>";

echo "Question 10: Query Optimization and Indexing (Short Answer)". "<br><br>";

echo     "EXPLAIN 
    SELECT 
        c.id AS customer_id,
        c.name AS customer_name,
        SUM(oi.quantity * oi.price) AS total_spending
    FROM order_items oi
    JOIN orders o ON oi.order_id = o.id
    JOIN customers c ON o.customer_id = c.id
    GROUP BY c.id, c.name
    ORDER BY total_spending DESC
    LIMIT 5";
echo "<br>"."==============================================================="."<br>";

echo "Question 11: Query Optimization Challenge"."<br><br>";    
echo "SELECT 
    c.id AS customer_id, 
    c.name, 
    COALESCE(SUM(oi.quantity * oi.price), 0) AS total_spent
FROM customers c
LEFT JOIN orders o ON c.id = o.customer_id
LEFT JOIN order_items oi ON o.id = oi.order_id
GROUP BY c.id, c.name
ORDER BY total_spent DESC";