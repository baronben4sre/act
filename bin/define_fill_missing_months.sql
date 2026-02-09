DELIMITER //

CREATE PROCEDURE fill_missing_months(IN acc_id INT)
BEGIN
  -- Step 1: Create temporary table for months
  DROP TEMPORARY TABLE IF EXISTS tmp_months;
  CREATE TEMPORARY TABLE tmp_months (month_start DATE);

  -- Step 2: Generate all months between min/max operations
  INSERT INTO tmp_months (month_start)
  WITH RECURSIVE months AS (
    SELECT DATE_FORMAT(MIN(operation), '%Y-%m-01') AS month_start,
           DATE_FORMAT(MAX(operation), '%Y-%m-01') AS max_month
    FROM transactions
    WHERE account_id = acc_id
    UNION ALL
    SELECT DATE_ADD(month_start, INTERVAL 1 MONTH), max_month
    FROM months
    WHERE DATE_ADD(month_start, INTERVAL 1 MONTH) <= max_month
  )
  SELECT month_start FROM months;

  -- Step 3: Insert missing months
  INSERT INTO transactions (account_id, libel, operation, amount, category_id)
  SELECT
    acc_id,
    (SELECT libel FROM transactions WHERE account_id=acc_id LIMIT 1),
    LAST_DAY(m.month_start),
    (
      SELECT amount
      FROM transactions
      WHERE account_id=acc_id AND operation < DATE_ADD(m.month_start, INTERVAL 1 MONTH)
      ORDER BY operation DESC
      LIMIT 1
    ),
    (SELECT category_id FROM transactions WHERE account_id=acc_id LIMIT 1)
  FROM tmp_months m
  WHERE NOT EXISTS (
    SELECT 1 FROM transactions t
    WHERE t.account_id=acc_id
      AND DATE_FORMAT(t.operation, '%Y-%m-01') = m.month_start
  );

END//

DELIMITER ;

