#!/bin/bash 

if [ -n "${_ACCOUNT_UTILS_SH_INCLUDED:-}" ]; then
  echo return 0
fi

dir=$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)
set -a 
. $dir/../env/act.env
. $dir/../env/db.env
. $dir/../env/secret.env
set +a 

_ACCOUNT_UTILS_SH_INCLUDED=1

isTableExist() {
   # $1 = Table name
   # Return val: 0 = False ; 1 = True
   res=$(( $(mysql $MYSQL_DATABASE -h $MYSQL_HOST -u$MYSQL_USER -p$MYSQL_PASSWORD -e "select table_name from information_schema.tables where table_name = \"$1\" and table_schema = \"$MYSQL_DATABASE\"" | wc -l) != 0 ))
   echo "$res"
   return $res
}

createSumTables() {
  isTableExist "category_yearly_totals"
  if [ $? -eq 0 ];then
    mysql $MYSQL_DATABASE -h $MYSQL_HOST -u$MYSQL_USER -p$MYSQL_PASSWORD -e """
      CREATE TABLE category_yearly_totals (
          category_id INT,
          year YEAR NOT NULL,
          total_amount DECIMAL(15,2) NOT NULL DEFAULT 0,
          transaction_count INT DEFAULT 0,
          last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          PRIMARY KEY (category_id, year)
      )
    """
  fi
  if [ $(isTableExist "category_monthly_totals") -eq 0 ];then
    mysql $MYSQL_DATABASE -h $MYSQL_HOST -u$MYSQL_USER -p$MYSQL_PASSWORD -e """
      CREATE TABLE category_monthly_totals (
          category_id INT,
          month_end DATE NOT NULL,
          total_amount DECIMAL(15,2) NOT NULL DEFAULT 0,
          transaction_count INT DEFAULT 0,
          last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          PRIMARY KEY (category_id, month_end)
      )
    """
  fi
}


