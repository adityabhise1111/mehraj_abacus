https://verbose-spoon-vx4j567vj463wvx4-8000.app.github.dev/
start command  php -S 0.0.0.0:8000

msql cmd  php7.4 -m | grep mysqli

run cmd new 
php7.4 -S 0.0.0.0:8000

sudo service mysql start


### 1. PHP Fatal error: Class "mysqli" not found

**Cause:**  
The MySQLi extension was not installed for your PHP version.

**Solution:**  
- Switched to PHP 7.4, which had the extension available.
- Installed PHP 7.4 and its MySQL extension:
    ```sh
    sudo apt-get install php7.4 php7.4-cli php7.4-mysql
    ```

---

### 2. Can't connect to local MySQL server through socket '/var/run/mysqld/mysqld.sock' (13)

**Cause:**  
MySQL server was not running.

**Solution:**  
- Checked MySQL status:
    ```sh
    sudo service mysql status
    ```
- Started MySQL server:
    ```sh
    sudo service mysql start
    ```

---

### 3. Connection failed: Permission denied

**Cause:**  
The MySQL user/database did not exist or had no privileges.

**Solution:**  
- Logged into MySQL as root:
    ```sh
    sudo mysql
    ```
- Created the database, user, and granted privileges:
    ```sql
    CREATE DATABASE u804948088_abacus;
    CREATE USER 'u804948088_abacus'@'localhost' IDENTIFIED BY '#Abacus123';
    GRANT ALL PRIVILEGES ON u804948088_abacus.* TO 'u804948088_abacus'@'localhost';
    FLUSH PRIVILEGES;
    EXIT;
    ```

---

### 4. How to exit MySQL prompt

**Solution:**  
Type:
```sql
exit;
```
or
```sql
quit;
```
and press Enter.

---

### 5. Switching PHP version (if needed)

**Solution:**  
To use PHP 7.4 for the built-in server:
```sh
php7.4 -S 0.0.0.0:8000
```

---

### 6. Check if MySQLi is enabled

**Solution:**  
Stop the server, then run:
```sh
php7.4 -m | grep mysqli
```
You should see `mysqli` in the output.

### 7. Starting the PHP built-in server
**Solution:**
To start the PHP built-in server on port 8000:
```sh
php7.4 -S



### MySQL Common Usage Commands

#### 1. **Login to MySQL**
```sh
mysql -u username -p
```

#### 2. **Create a Database**
```sql
CREATE DATABASE dbname;
```

#### 3. **Show All Databases**
```sql
SHOW DATABASES;
```

#### 4. **Use a Database**
```sql
USE dbname;
```

#### 5. **Create a Table**
```sql
CREATE TABLE tablename (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    age INT
);
```

#### 6. **Show All Tables**
```sql
SHOW TABLES;
```

#### 7. **Describe Table Structure**
```sql
DESCRIBE tablename;
```

#### 8. **Insert Data**
```sql
INSERT INTO tablename (name, age) VALUES ('Alice', 25);
```

#### 9. **Select Data**
```sql
SELECT * FROM tablename;
```

#### 10. **Update Data**
```sql
UPDATE tablename SET age = 26 WHERE name = 'Alice';
```

#### 11. **Delete Data**
```sql
DELETE FROM tablename WHERE name = 'Alice';
```

#### 12. **Drop (Delete) a Table**
```sql
DROP TABLE tablename;
```

#### 13. **Drop (Delete) a Database**
```sql
DROP DATABASE dbname;
```

#### 14. **Create a User**
```sql
CREATE USER 'username'@'localhost' IDENTIFIED BY 'password';
```

#### 15. **Grant Privileges to a User**
```sql
GRANT ALL PRIVILEGES ON dbname.* TO 'username'@'localhost';
FLUSH PRIVILEGES;
```

#### 16. **Exit MySQL**
```sql
exit;
```