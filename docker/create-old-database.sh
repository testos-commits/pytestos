# old database

mysql --user=root --password="$MYSQL_ROOT_PASSWORD" <<-EOSQL
    CREATE DATABASE IF NOT EXISTS pyv1;
    GRANT ALL PRIVILEGES ON pyv1.* TO '$MYSQL_USER'@'%';
EOSQL