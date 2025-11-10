#!/bin/bash

echo "=========================================="
echo "CAG Attendance System - Database Setup"
echo "=========================================="
echo ""

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Check if MySQL is installed
echo "Checking MySQL installation..."
if ! command -v mysql &> /dev/null; then
    echo -e "${RED}❌ MySQL is not installed${NC}"
    echo ""
    echo "Please install MySQL first:"
    echo "  Ubuntu/Debian: sudo apt-get install mysql-server"
    echo "  MacOS: brew install mysql"
    echo "  Windows: Download from https://dev.mysql.com/downloads/"
    exit 1
fi

echo -e "${GREEN}✓ MySQL is installed${NC}"
echo ""

# Check if MySQL service is running
echo "Checking if MySQL service is running..."
if ! mysqladmin ping -h"127.0.0.1" --silent 2>/dev/null; then
    echo -e "${YELLOW}⚠ MySQL service is not running${NC}"
    echo ""
    echo "Starting MySQL service..."

    # Try different methods to start MySQL
    if command -v systemctl &> /dev/null; then
        sudo systemctl start mysql || sudo systemctl start mysqld
    elif command -v service &> /dev/null; then
        sudo service mysql start || sudo service mysqld start
    else
        echo -e "${RED}❌ Could not start MySQL service${NC}"
        echo "Please start MySQL manually and run this script again"
        exit 1
    fi

    # Wait a bit for MySQL to start
    sleep 3

    # Check again
    if ! mysqladmin ping -h"127.0.0.1" --silent 2>/dev/null; then
        echo -e "${RED}❌ MySQL service failed to start${NC}"
        echo "Please start MySQL manually and run this script again"
        exit 1
    fi
fi

echo -e "${GREEN}✓ MySQL service is running${NC}"
echo ""

# Create database if it doesn't exist
echo "Creating database..."
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS cag_attendance CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>/dev/null

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Database created/verified${NC}"
else
    echo -e "${YELLOW}⚠ Could not create database (you may need to enter your MySQL password)${NC}"
    echo "Trying without password..."
    mysql -u root -e "CREATE DATABASE IF NOT EXISTS cag_attendance CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>/dev/null

    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✓ Database created/verified${NC}"
    else
        echo -e "${RED}❌ Could not create database${NC}"
        echo ""
        echo "Please create the database manually:"
        echo "  mysql -u root -p"
        echo "  CREATE DATABASE cag_attendance CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
        exit 1
    fi
fi
echo ""

# Run migrations
echo "Running database migrations..."
php artisan migrate:fresh --force

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Migrations completed${NC}"
else
    echo -e "${RED}❌ Migration failed${NC}"
    exit 1
fi
echo ""

# Run seeders
echo "Seeding database with sample data..."
php artisan db:seed --force

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Database seeded successfully${NC}"
else
    echo -e "${RED}❌ Seeding failed${NC}"
    exit 1
fi
echo ""

echo "=========================================="
echo -e "${GREEN}✓ Database setup completed!${NC}"
echo "=========================================="
echo ""
echo "Default login credentials:"
echo ""
echo "  ADMIN:"
echo "    Employee No: EMP001"
echo "    Password: admin123"
echo ""
echo "  INSTRUCTOR:"
echo "    Employee No: EMP002  "
echo "    Password: password123"
echo ""
echo "  OFFICE STAFF:"
echo "    Employee No: EMP003"
echo "    Password: password123"
echo ""
echo "You can now start the application:"
echo "  php artisan serve"
echo ""
echo "Then visit: http://localhost:8000"
echo "=========================================="
